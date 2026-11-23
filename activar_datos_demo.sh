#!/usr/bin/env bash

# Script para activar/desactivar datos demo de Floración y Programación de Cruzamientos en SIVAR
DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
API_DIR="$DIR/api_sivar"

# Detectar binario PHP
if command -v php >/dev/null 2>&1; then
    PHP_BIN="php"
elif [ -x "/opt/homebrew/opt/php@8.2/bin/php" ]; then
    PHP_BIN="/opt/homebrew/opt/php@8.2/bin/php"
elif [ -x "/Applications/MAMP/bin/php/php8.2.26/bin/php" ]; then
    PHP_BIN="/Applications/MAMP/bin/php/php8.2.26/bin/php"
else
    echo "❌ Error: No se encontró un binario de PHP (8.2)."
    exit 1
fi

ACTION="activar"
if [ "$1" == "--restore" ] || [ "$1" == "--desactivar" ] || [ "$1" == "restore" ] || [ "$1" == "desactivar" ]; then
    ACTION="desactivar"
fi

$PHP_BIN -r '
require "'"$API_DIR"'/vendor/autoload.php";
$app = require_once "'"$API_DIR"'/bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

$action = "'"$ACTION"'";

if ($action === "desactivar") {
    if (!Schema::hasTable("floracion_demo_backup")) {
        echo "⚠️ No se encontró la tabla de respaldo (floracion_demo_backup). No hay cambios que revertir.\n";
        exit(0);
    }

    $backupRecords = DB::table("floracion_demo_backup")->get();
    if ($backupRecords->isEmpty()) {
        echo "⚠️ No hay registros respaldados.\n";
        exit(0);
    }

    foreach ($backupRecords as $b) {
        DB::table("floracion")
            ->where("id_flrcion", $b->id_flrcion)
            ->update([
                "fcha" => $b->fcha_original,
                "estado" => $b->estado_original
            ]);
    }

    DB::table("floracion_demo_backup")->truncate();
    echo "✅ Datos demo desactivados. Fechas y estados originales restaurados (" . count($backupRecords) . " flores).\n";
    exit(0);
}

// 1. Crear tabla de respaldo si no existe
if (!Schema::hasTable("floracion_demo_backup")) {
    Schema::create("floracion_demo_backup", function ($table) {
        $table->id();
        $table->integer("id_flrcion")->unique();
        $table->date("fcha_original")->nullable();
        $table->integer("estado_original")->default(0);
        $table->timestamp("created_at")->useCurrent();
    });
}

// 2. Seleccionar flores representativas (hasta 60 por proyecto) para cubrir todos los proyectos
$projects = DB::table("floracion")->select("id_pr")->distinct()->whereNotNull("id_pr")->pluck("id_pr");
$selectedIds = [];
foreach ($projects as $prId) {
    $ids = DB::table("floracion")
        ->where("id_pr", $prId)
        ->orderBy("fcha", "desc")
        ->orderBy("id_flrcion", "desc")
        ->limit(60)
        ->pluck("id_flrcion")
        ->toArray();
    $selectedIds = array_merge($selectedIds, $ids);
}

// 3. Respaldar fecha y estado original antes de modificar
$flowersToBackup = DB::table("floracion")->whereIn("id_flrcion", $selectedIds)->get();
foreach ($flowersToBackup as $f) {
    DB::table("floracion_demo_backup")->updateOrInsert(
        ["id_flrcion" => $f->id_flrcion],
        [
            "fcha_original" => $f->fcha,
            "estado_original" => $f->estado,
            "created_at" => now()
        ]
    );
}

// 4. Actualizar fecha a HOY y estado a 0 (activo para cruce)
$today = Carbon::today()->format("Y-m-d");
$updated = DB::table("floracion")
    ->whereIn("id_flrcion", $selectedIds)
    ->update([
        "fcha" => $today,
        "estado" => 0
    ]);

// 5. Mostrar resumen de datos activos
$activeInitial = DB::table("remote_pg_sipro")
    ->selectRaw("remote_pg_sipro.nm_prycto, remote_pg_sipro.cd_cntble, remote_pg_sipro.id_prycto, count(*) as numero")
    ->join("floracion", "floracion.id_pr", "=", "remote_pg_sipro.id_prycto")
    ->whereBetween("floracion.fcha", [Carbon::yesterday()->format("Y-m-d"), Carbon::today()->format("Y-m-d")])
    ->where("floracion.estado", 0)
    ->groupBy("remote_pg_sipro.id_prycto", "remote_pg_sipro.nm_prycto", "remote_pg_sipro.cd_cntble")
    ->get();

$sexStats = DB::table("floracion")
    ->whereIn("id_flrcion", $selectedIds)
    ->select("sxo", DB::raw("count(*) as total"))
    ->groupBy("sxo")
    ->get();

echo "========================================================\n";
echo "🌸 DATOS DEMO ACTIVADOS CON ÉXITO PARA FLORACIÓN Y CRUZAMIENTOS\n";
echo "========================================================\n";
echo "📅 Fecha asignada a las flores demo: " . $today . " (Hoy)\n";
echo "🌻 Total de flores activadas: " . $updated . "\n\n";

echo "📊 Distribución por sexo:\n";
foreach ($sexStats as $s) {
    echo "  - " . ($s->sxo ?: "Sin sexo") . ": " . $s->total . "\n";
}

echo "\n📁 Proyectos listos con flores en Programación de Cruzamientos:\n";
foreach ($activeInitial as $p) {
    echo "  - [" . $p->cd_cntble . "] " . $p->nm_prycto . " (" . $p->numero . " flores)\n";
}

echo "\n💡 Puedes revertir a los datos originales en cualquier momento ejecutando:\n";
echo "   ./activar_datos_demo.sh --restore\n";
echo "========================================================\n";
'

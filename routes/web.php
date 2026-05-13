<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    $user = auth()->user();

    // Strict Access Restriction Gate
    if (!in_array($user->role, ['JEFE', 'LIDER'])) {
        abort(403, 'Acceso restringido al panel de control.');
    }
    
    // Contar ensayos accesibles para este usuario
    $baseQuery = \App\Models\Ensayo::query();
    if ($user->role !== 'JEFE') {
        if (is_array($user->ambiente) && count($user->ambiente) > 0) {
            $baseQuery->whereIn('amb_seleccion', $user->ambiente);
        } else {
            $baseQuery->where('user_id', $user->id);
        }
    }

    return Inertia::render('Dashboard', [
        'total_ensayos' => $baseQuery->count(),
        'total_cruzamientos' => \App\Models\Cruzamiento::count(),
        'total_usuarios' => \App\Models\User::count(),
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

use App\Http\Controllers\EnsayoController;

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Módulo de Registro de Ensayos
    Route::get('/ensayos/dashboard', [EnsayoController::class, 'dashboard'])->name('ensayos.dashboard');
    Route::get('/ensayos', [EnsayoController::class, 'index'])->name('ensayos.index');
    Route::get('/ensayos/export', [EnsayoController::class, 'export'])->name('ensayos.export');
    Route::post('/ensayos/import', [EnsayoController::class, 'store'])->name('ensayos.import');
    Route::post('/ensayos/import/confirm', [EnsayoController::class, 'confirmImport'])->name('ensayos.confirm-import');
    Route::patch('/ensayos/{ensayo}', [EnsayoController::class, 'update'])->name('ensayos.update');
    Route::get('/ensayos/standardization/preview', [EnsayoController::class, 'standardizationPreview'])->name('ensayos.standardization.preview');
    Route::post('/ensayos/standardization/execute', [EnsayoController::class, 'standardizationExecute'])->name('ensayos.standardization.execute');

    // Módulo de Cruzamientos
    Route::resource('cruzamientos', \App\Http\Controllers\CruzamientoController::class);

    // Catálogos Maestros
    Route::post('catalogos/merge', [\App\Http\Controllers\CatalogoController::class, 'merge'])->name('catalogos.merge');
    Route::resource('catalogos', \App\Http\Controllers\CatalogoController::class);

    // Gestión de Usuarios
    Route::resource('users', \App\Http\Controllers\UserController::class)->only(['index', 'update', 'destroy']);

    // Historial & Auditoría
    Route::get('actividades', [\App\Http\Controllers\ActividadController::class, 'index'])->name('actividades.index');

    // Módulo de Mapas y Adjuntos
    Route::get('/ensayos/{ensayo}/adjuntos', [\App\Http\Controllers\AdjuntoController::class, 'index'])->name('adjuntos.index');
    Route::post('/ensayos/{ensayo}/adjuntos', [\App\Http\Controllers\AdjuntoController::class, 'store'])->name('adjuntos.store');
    Route::get('/adjuntos/{adjunto}/download', [\App\Http\Controllers\AdjuntoController::class, 'download'])->name('adjuntos.download');
    Route::delete('/adjuntos/{adjunto}', [\App\Http\Controllers\AdjuntoController::class, 'destroy'])->name('adjuntos.destroy');
});

require __DIR__.'/auth.php';

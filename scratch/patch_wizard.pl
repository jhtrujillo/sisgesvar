use strict;
use warnings;

my $file = 'sivar/src/components/viveros/ViveroParcelasImportWizard.vue';
open(my $in, '<', $file) or die $!;
my $content = do { local $/; <$in> };
close($in);

# 1. Add caracter to mapping ref
$content =~ s/const mapping = ref\(\{.*?\}\);/const mapping = ref({ plot: "", variedad: "", caracter: "" });/s;

# 2. Add Select for Caracter in Step 3
my $col_variedad = <<'HTML';
                  <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm border-l-4 border-l-cenicana">
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Columna Variedad</label>
                    <select
                      v-model="mapping.variedad"
                      class="w-full pl-3 pr-10 py-2 border-slate-300 rounded-md border text-sm focus:outline-none focus:ring-1 focus:ring-cenicana bg-slate-50"
                    >
                      <option value="">-- Seleccionar --</option>
                      <option v-for="col in headers" :key="col" :value="col">{{ col }}</option>
                    </select>
                  </div>
HTML

my $col_caracter = <<'HTML';
                  <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm border-l-4 border-l-cenicana">
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Columna Variedad</label>
                    <select
                      v-model="mapping.variedad"
                      class="w-full pl-3 pr-10 py-2 border-slate-300 rounded-md border text-sm focus:outline-none focus:ring-1 focus:ring-cenicana bg-slate-50"
                    >
                      <option value="">-- Seleccionar --</option>
                      <option v-for="col in headers" :key="col" :value="col">{{ col }}</option>
                    </select>
                  </div>
                  <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm border-l-4 border-l-blue-400">
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Columna Carácter (Opcional)</label>
                    <select
                      v-model="mapping.caracter"
                      class="w-full pl-3 pr-10 py-2 border-slate-300 rounded-md border text-sm focus:outline-none focus:ring-1 focus:ring-cenicana bg-slate-50"
                    >
                      <option value="">-- No incluir --</option>
                      <option v-for="col in headers" :key="col" :value="col">{{ col }}</option>
                    </select>
                  </div>
HTML
$content =~ s/\Q$col_variedad\E/$col_caracter/s;

# Change grid cols
$content =~ s/grid-cols-1 md:grid-cols-2 gap-6 max-w-3xl/grid-cols-1 md:grid-cols-3 gap-6 max-w-4xl/g;

# 3. Fix Validate Data
my $validateDataSearch = <<'JS';
  const raw = XLSX.utils.sheet_to_json(worksheet);
  rawData.value = raw.map((row: any) => {
    const newRow: any = {};
    for (const key in row) {
      const val = row[key];
      newRow[String(key).trim()] = typeof val === "string" ? val.trim() : val;
    }
    return newRow;
  });
JS

my $validateDataReplace = <<'JS';
  const carCol = headers.value.find((h) => h.toLowerCase().includes("caracter") || h.toLowerCase().includes("carácter"));
  if (carCol) mapping.value.caracter = carCol;

  const raw = XLSX.utils.sheet_to_json(worksheet);
  rawData.value = raw.map((row: any) => {
    const newRow: any = {};
    for (const key in row) {
      const val = row[key];
      newRow[String(key).trim()] = typeof val === "string" ? val.trim() : val;
    }
    return newRow;
  });
JS
$content =~ s/\Q$validateDataSearch\E/$validateDataReplace/s;

my $pushDataSearch = <<'JS';
    const exactMatch = varMap.get(varVal.toLowerCase());

    if (exactMatch) {
      readyToImport.value.push({
        numero_parcela: plotVal,
        variedad_id: exactMatch.id_nm_vrdad,
        numero_parcela_origen: null,
        id_plot_origen: `${props.viveroIdentificador}-${plotVal}`,
        caracter_id: props.caracterId || null
      });
    } else {
JS

my $pushDataReplace = <<'JS';
    const exactMatch = varMap.get(varVal.toLowerCase());
    
    // Find caracter match if mapped
    let resolvedCaracterId = props.caracterId || null;
    if (mapping.value.caracter && row[mapping.value.caracter]) {
      const carText = String(row[mapping.value.caracter]).trim().toLowerCase();
      const carMatch = props.caracteres.find(c => c.nombre.toLowerCase() === carText || c.nombre.toLowerCase().includes(carText));
      if (carMatch) {
        resolvedCaracterId = carMatch.id;
      }
    }

    if (exactMatch) {
      readyToImport.value.push({
        numero_parcela: plotVal,
        variedad_id: exactMatch.id_nm_vrdad,
        numero_parcela_origen: null,
        id_plot_origen: `${props.viveroIdentificador}-${plotVal}`,
        caracter_id: resolvedCaracterId
      });
    } else {
JS
$content =~ s/\Q$pushDataSearch\E/$pushDataReplace/s;

# 4. Remove overflow-y-auto and max-h-96 from the table wrapper
$content =~ s/max-h-96 overflow-y-auto border border-slate-200 rounded-lg shadow-inner bg-slate-50 relative/border border-slate-200 rounded-lg shadow-inner bg-slate-50 relative/g;

# 5. Remove overflow-hidden from the modal container
$content =~ s/inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl/inline-block align-bottom bg-white rounded-lg text-left overflow-visible shadow-xl/g;

# 6. Make the dropdown wider and longer
$content =~ s/class="absolute z-20 w-\[90%\] mt-1 bg-white shadow-xl max-h-48 rounded-lg py-1 text-xs overflow-auto border border-slate-200 left-4"/class="absolute z-50 w-full mt-1 bg-white shadow-2xl max-h-60 rounded-lg py-1 text-xs overflow-auto border border-slate-300 left-0"/g;

open(my $out, '>', $file) or die $!;
print $out $content;
close($out);

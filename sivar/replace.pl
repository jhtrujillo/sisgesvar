use strict;
use warnings;
my $file = 'src/views/mejoramiento/siembra-campo/viveros/ViveroFormView.vue';
open(my $in, '<', $file) or die $!;
my $content = do { local $/; <$in> };
close($in);

# 1. Update HTML
my $html_old = <<'OLD';
              <!-- Carácter -->
              <div class="relative md:col-span-2">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5" for="caracter_id">Carácter (Opcional)</label>
                <div class="relative">
                  <input
                    type="text"
                    v-model="searchCaracter"
                    @focus="showCaracteres = true"
                    @blur="hideCaracteresDelay"
                    placeholder="Buscar o agregar..."
                    class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs font-semibold rounded-xl px-3.5 py-3 focus:bg-white focus:ring-4 focus:ring-cenicana/10 focus:border-cenicana transition-all outline-none shadow-sm"
                    :disabled="!form.proyecto_id"
                    :class="{ 'bg-slate-100 border-slate-200 text-slate-400 cursor-not-allowed shadow-inner': !form.proyecto_id }"
                  />
                  <button
                    v-if="form.caracter_id"
                    @click="clearCaracter"
                    type="button"
                    class="absolute right-3.5 top-3 text-slate-400 hover:text-red-500 transition-colors"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                      <path
                        fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                        clip-rule="evenodd"
                      />
                    </svg>
                  </button>
                </div>
                  <div
                    v-if="showCaracteres && form.proyecto_id"
                    class="absolute z-20 w-full mt-1 bg-white shadow-xl max-h-60 rounded-xl py-1 text-xs ring-1 ring-black/5 overflow-auto border border-slate-100"
                  >
                    <div
                      v-if="searchCaracter && !exactMatchCaracter"
                      @mousedown="selectNewCaracter"
                      class="cursor-pointer select-none py-2 px-3.5 hover:bg-emerald-50 text-cenicana font-bold border-b border-slate-100 transition-colors"
                    >
                      + Agregar nuevo: "{{ searchCaracter }}"
                    </div>
                    <div v-if="filteredCaracteres.length === 0 && !searchCaracter" class="cursor-default select-none py-2 px-3.5 text-slate-400 font-medium">
                      No hay caracteres (escribe para crear)
                    </div>
                    <div
                      v-for="car in filteredCaracteres"
                      :key="car.id"
                      @mousedown="selectCaracter(car)"
                      class="cursor-pointer select-none py-2.5 px-3.5 hover:bg-slate-50 text-slate-700 font-medium transition-colors"
                      :class="form.caracter_id === car.id ? 'bg-emerald-50 text-cenicana font-bold border-l-2 border-cenicana' : ''"
                    >
                      {{ car.nombre }}
                    </div>
OLD

my $html_new = <<'NEW';
              <!-- Carácter -->
              <div class="relative md:col-span-2">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5" for="caracter_id">Carácter (Opcional)</label>
                
                <div class="flex flex-wrap gap-2 mb-2" v-if="form.caracteres_ids && form.caracteres_ids.length > 0">
                  <div v-for="c_id in form.caracteres_ids" :key="c_id" class="bg-emerald-100 text-emerald-800 text-xs font-semibold px-2.5 py-1 rounded-full flex items-center gap-1.5 border border-emerald-200">
                    {{ getCaracterName(c_id) }}
                    <button type="button" @click="removeCaracter(c_id)" class="text-emerald-600 hover:text-emerald-900 focus:outline-none">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                      </svg>
                    </button>
                  </div>
                </div>

                <div class="relative">
                  <input
                    type="text"
                    v-model="searchCaracter"
                    @focus="showCaracteres = true"
                    @blur="hideCaracteresDelay"
                    placeholder="Buscar o agregar..."
                    class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs font-semibold rounded-xl px-3.5 py-3 focus:bg-white focus:ring-4 focus:ring-cenicana/10 focus:border-cenicana transition-all outline-none shadow-sm"
                    :disabled="!form.proyecto_id"
                    :class="{ 'bg-slate-100 border-slate-200 text-slate-400 cursor-not-allowed shadow-inner': !form.proyecto_id }"
                  />
                </div>
                  <div
                    v-if="showCaracteres && form.proyecto_id"
                    class="absolute z-20 w-full mt-1 bg-white shadow-xl max-h-60 rounded-xl py-1 text-xs ring-1 ring-black/5 overflow-auto border border-slate-100"
                  >
                    <div
                      v-if="searchCaracter && !exactMatchCaracter"
                      @mousedown="selectNewCaracter"
                      class="cursor-pointer select-none py-2 px-3.5 hover:bg-emerald-50 text-cenicana font-bold border-b border-slate-100 transition-colors"
                    >
                      + Agregar nuevo: "{{ searchCaracter }}"
                    </div>
                    <div v-if="filteredCaracteres.length === 0 && !searchCaracter" class="cursor-default select-none py-2 px-3.5 text-slate-400 font-medium">
                      No hay caracteres (escribe para crear)
                    </div>
                    <div
                      v-for="car in filteredCaracteres"
                      :key="car.id"
                      v-show="!form.caracteres_ids || !form.caracteres_ids.includes(car.id)"
                      @mousedown="selectCaracter(car)"
                      class="cursor-pointer select-none py-2.5 px-3.5 hover:bg-slate-50 text-slate-700 font-medium transition-colors"
                    >
                      {{ car.nombre }}
                    </div>
NEW

$content =~ s/\Q$html_old\E/$html_new/g;


# 2. Props mapping in wizard
$content =~ s/:caracterId="form.caracter_id"/:caracterId="form.caracteres_ids && form.caracteres_ids.length > 0 ? form.caracteres_ids[0] : ''"/g;

# 3. form definition
$content =~ s/caracter_id: "",\n  origen_ingenio/caracteres_ids: [] as number[],\n  origen_ingenio/g;
$content =~ s/caracter_id: "",\n    origen_ingenio/caracteres_ids: [],\n    origen_ingenio/g;

# 4. Computed activeCaracter
$content =~ s/const inheritedCaracter = form.value.caracter_id \? getCaracterGlobalNombre\(\) : "";/const inheritedCaracter = getCaracterGlobalNombre();/g;

# 5. Functions
my $old_get = <<'OLD';
const getCaracterGlobalNombre = () => {
  if (!form.value.caracter_id) return "";
  const c = caracteres.value.find((car) => car.id == form.value.caracter_id);
  return c ? c.nombre : "";
};
OLD

my $new_get = <<'NEW';
const getCaracterGlobalNombre = () => {
  if (!form.value.caracteres_ids || form.value.caracteres_ids.length === 0) return "";
  const nombres = form.value.caracteres_ids.map((id) => getCaracterName(id)).filter(n => n !== "");
  return nombres.join(", ");
};

const getCaracterName = (id: number | string) => {
  const c = caracteres.value.find((car) => car.id == id);
  return c ? c.nombre : "";
};
NEW

$content =~ s/\Q$old_get\E/$new_get/g;

# 6. Clears
$content =~ s/form\.value\.caracter_id = "";\n  searchCaracter/form.value.caracteres_ids = [];\n  searchCaracter/g;

# 7. Select
my $old_select = <<'OLD';
const selectCaracter = (car: any) => {
  form.value.caracter_id = car.id;
  searchCaracter.value = car.nombre;
  showCaracteres.value = false;
};
OLD

my $new_select = <<'NEW';
const selectCaracter = (car: any) => {
  if (!form.value.caracteres_ids) {
    form.value.caracteres_ids = [];
  }
  if (!form.value.caracteres_ids.includes(car.id)) {
    form.value.caracteres_ids.push(car.id);
  }
  searchCaracter.value = "";
};

const removeCaracter = (id: number) => {
  form.value.caracteres_ids = form.value.caracteres_ids.filter((c) => c !== id);
};
NEW

$content =~ s/\Q$old_select\E/$new_select/g;

# 8. Load in resetAndLoad
my $old_init = <<'OLD';
        if (form.value.caracter_id) {
          const car = caracteres.value.find((c) => c.id == form.value.caracter_id);
          if (car) searchCaracter.value = car.nombre;
        }
OLD

my $new_init = <<'NEW';
        if (res.data.caracteres && res.data.caracteres.length > 0) {
          form.value.caracteres_ids = res.data.caracteres.map((c: any) => c.id);
        } else if (res.data.caracter_id) {
          form.value.caracteres_ids = [res.data.caracter_id];
        }
NEW

$content =~ s/\Q$old_init\E/$new_init/g;

my $old_query = <<'OLD';
      if (route.query.caracter_id) {
        form.value.caracter_id = Number(route.query.caracter_id);
        const car = caracteres.value.find((c) => c.id == form.value.caracter_id);
        if (car) searchCaracter.value = car.nombre;
      }
OLD

my $new_query = <<'NEW';
      if (route.query.caracter_id) {
        form.value.caracteres_ids = [Number(route.query.caracter_id)];
      }
NEW

$content =~ s/\Q$old_query\E/$new_query/g;

open(my $out, '>', $file) or die $!;
print $out $content;
close($out);

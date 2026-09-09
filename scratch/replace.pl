use strict;
use warnings;
my $file = 'sivar/src/views/mejoramiento/siembra-campo/viveros/ViveroFormView.vue';
open(my $in, '<', $file) or die $!;
my $content = do { local $/; <$in> };
close($in);

$content =~ s/caracter_id: "",/caracteres_ids: [] as number[],/g;
$content =~ s/const inheritedCaracter = form.value.caracter_id \? getCaracterGlobalNombre\(\) : "";/const inheritedCaracter = getCaracterGlobalNombre();/g;

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

$content =~ s/form\.value\.caracter_id = "";/form.value.caracteres_ids = [];/g;

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

$content =~ s/caracter_id: "",/caracteres_ids: [],/g;

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

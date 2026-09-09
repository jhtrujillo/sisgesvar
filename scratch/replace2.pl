use strict;
use warnings;
my $file = 'sivar/src/views/mejoramiento/siembra-campo/viveros/ViveroFormView.vue';
open(my $in, '<', $file) or die $!;
my $content = do { local $/; <$in> };
close($in);

# HTML Template
$content =~ s/form.caracteres_ids && form.caracteres_ids.length > 0/selectedCaracteres.value \&\& selectedCaracteres.value.length > 0/g;
# Wait, in template we don't use .value!
$content =~ s/selectedCaracteres\.value \&\& selectedCaracteres\.value\.length > 0/selectedCaracteres \&\& selectedCaracteres.length > 0/g;

$content =~ s/v-for="c_id in form.caracteres_ids"/v-for="c_id in selectedCaracteres"/g;
$content =~ s/!Array.isArray\(form.caracteres_ids\) || !form.caracteres_ids.includes\(car.id\)/!selectedCaracteres.includes(car.id)/g;
$content =~ s/:caracterId="form.caracteres_ids \&\& form.caracteres_ids.length > 0 \? form.caracteres_ids\[0\] : ''"/:caracterId="selectedCaracteres.length > 0 ? selectedCaracteres[0] : ''"/g;

# Variables
$content =~ s/caracteres_ids: \[\] as number\[\],\n//g;
$content =~ s/caracteres_ids: \[\],\n//g;
$content =~ s/const searchCaracter = ref\(""\);/const searchCaracter = ref("");\nconst selectedCaracteres = ref<number[]>([]);/g;

# Logic
$content =~ s/form\.value\.caracteres_ids/selectedCaracteres.value/g;

# Form Submit
$content =~ s/await viverosServices\.updateVivero\(route\.params\.id as string, form\.value\);/const payload = { ...form.value, caracteres_ids: selectedCaracteres.value };\n      await viverosServices.updateVivero(route.params.id as string, payload);/g;
$content =~ s/await viverosServices\.createVivero\(form\.value\);/const payload = { ...form.value, caracteres_ids: selectedCaracteres.value };\n      await viverosServices.createVivero(payload);/g;

# Fix form.value init
$content =~ s/form\.value = \{ \.\.\.vivero, caracteres_ids: \[\] \};/form.value = { ...vivero };\n      selectedCaracteres.value = [];/g;

# Fix selectCaracter
my $old_select = <<'OLD';
const selectCaracter = (car: any) => {
  if (!Array.isArray(selectedCaracteres.value)) {
    selectedCaracteres.value = [];
  }
  if (!selectedCaracteres.value.includes(car.id)) {
    selectedCaracteres.value = [...selectedCaracteres.value, car.id];
  }
  searchCaracter.value = "";
};
OLD

my $new_select = <<'NEW';
const selectCaracter = (car: any) => {
  if (!selectedCaracteres.value.includes(car.id)) {
    selectedCaracteres.value.push(car.id);
  }
  searchCaracter.value = "";
};
NEW
$content =~ s/\Q$old_select\E/$new_select/g;

my $old_remove = <<'OLD';
const removeCaracter = (id: number) => {
  if (!Array.isArray(selectedCaracteres.value)) {
    return;
  }
  selectedCaracteres.value = selectedCaracteres.value.filter((c) => c !== id);
};
OLD

my $new_remove = <<'NEW';
const removeCaracter = (id: number) => {
  selectedCaracteres.value = selectedCaracteres.value.filter((c) => c !== id);
};
NEW
$content =~ s/\Q$old_remove\E/$new_remove/g;


open(my $out, '>', $file) or die $!;
print $out $content;
close($out);

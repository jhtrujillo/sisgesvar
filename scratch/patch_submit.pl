use strict;
use warnings;

my $file = 'sivar/src/components/viveros/ViveroParcelasImportWizard.vue';
open(my $in, '<', $file) or die $!;
my $content = do { local $/; <$in> };
close($in);

my $search = <<'JS';
  const payload = [
    ...readyToImport.value,
    ...conflicts.value.map((c) => {
      const plotVal = c.row[mapping.value.plot];
      return {
        numero_parcela: plotVal,
        variedad_id: c.resolvedId,
        numero_parcela_origen: null,
        id_plot_origen: `${props.viveroIdentificador}-${plotVal}`,
        caracter_id: props.caracterId || null
      };
    })
  ];
JS

my $replace = <<'JS';
  const payload = [
    ...readyToImport.value,
    ...conflicts.value.map((c) => {
      const plotVal = c.row[mapping.value.plot];
      
      let resolvedCaracterId = props.caracterId || null;
      if (mapping.value.caracter && c.row[mapping.value.caracter]) {
        const carText = String(c.row[mapping.value.caracter]).trim().toLowerCase();
        const carMatch = props.caracteres.find(car => car.nombre.toLowerCase() === carText || car.nombre.toLowerCase().includes(carText));
        if (carMatch) {
          resolvedCaracterId = carMatch.id;
        }
      }

      return {
        numero_parcela: plotVal,
        variedad_id: c.resolvedId,
        numero_parcela_origen: null,
        id_plot_origen: `${props.viveroIdentificador}-${plotVal}`,
        caracter_id: resolvedCaracterId
      };
    })
  ];
JS

$content =~ s/\Q$search\E/$replace/s;

open(my $out, '>', $file) or die $!;
print $out $content;
close($out);

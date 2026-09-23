<?php
$file = 'src/components/ParentComparatorModal.vue';
$content = file_get_contents($file);

// 1. Add `causaVeto` to props
$oldProps = <<<EOD
  initiallyViable: {
    type: Boolean,
    default: true
  }
});
EOD;

$newProps = <<<EOD
  initiallyViable: {
    type: Boolean,
    default: true
  },
  causaVeto: {
    type: String,
    default: ""
  }
});
EOD;

$content = str_replace($oldProps, $newProps, $content);

// 2. Modify `viabilityDiagnosis` computed property
$oldDiag = <<<EOD
const viabilityDiagnosis = computed(() => {
  if (isViable.value) {
    return {
      title: "DIAGNÓSTICO: COMBINACIÓN VIABLE",
      description: "Esta pareja progenitora cumple con los criterios sanitarios y de mérito para la polinización en campo.",
      isViable: true
    };
  }

  // Si no es viable, determinamos la causa exacta:
  if (motherProfile.value.traits && fatherProfile.value.traits) {
    const mTraits = motherProfile.value.traits;
    const fTraits = fatherProfile.value.traits;

    const sumaMosaico = Number(mTraits.mosaico_p || 0) + Number(fTraits.mosaico_p || 0);
    if (sumaMosaico > 10) {
      return {
        title: "DIAGNÓSTICO: CRUZAMIENTO CON VETO SANITARIO (MOSAICO)",
        description: `La susceptibilidad acumulada para Mosaico (\${sumaMosaico.toFixed(1)}) sobrepasa el umbral sanitario de seguridad (10.0).`,
        isViable: false
      };
    }

    const sumaCarbon = Number(mTraits.carbon_p || 0) + Number(fTraits.carbon_p || 0);
    if (sumaCarbon > 10) {
      return {
        title: "DIAGNÓSTICO: CRUZAMIENTO CON VETO SANITARIO (CARBÓN)",
        description: `La susceptibilidad acumulada para Carbón (\${sumaCarbon.toFixed(1)}) sobrepasa el umbral sanitario de seguridad (10.0).`,
        isViable: false
      };
    }

    const sumaRoyaCafe = Number(mTraits.roya_cafe_r || 0) + Number(fTraits.roya_cafe_r || 0);
    if (sumaRoyaCafe > 11) {
      return {
        title: "DIAGNÓSTICO: CRUZAMIENTO CON VETO SANITARIO (ROYA)",
        description: `La susceptibilidad acumulada para Roya (\${sumaRoyaCafe.toFixed(1)}) sobrepasa el umbral sanitario de seguridad (11.0).`,
        isViable: false
      };
    }
  }

  return {
    title: "DIAGNÓSTICO: CRUZAMIENTO INVIABLE",
    description: "Este cruzamiento fue vetado biológica o genéticamente según los umbrales de seguridad definidos en el Paso 2.",
    isViable: false
  };
});
EOD;

$newDiag = <<<EOD
const viabilityDiagnosis = computed(() => {
  if (isViable.value) {
    return {
      title: "DIAGNÓSTICO: COMBINACIÓN VIABLE",
      description: "Esta pareja progenitora cumple con los criterios biológicos y genéticos para la polinización en campo.",
      isViable: true
    };
  }

  // Si no es viable, y nos pasaron la causa desde la matriz, la mostramos:
  if (props.causaVeto) {
      return {
          title: "DIAGNÓSTICO: CRUZAMIENTO INVIABLE",
          description: props.causaVeto,
          isViable: false
      };
  }

  return {
    title: "DIAGNÓSTICO: CRUZAMIENTO INVIABLE",
    description: "Este cruzamiento fue vetado biológica o genéticamente según los umbrales de seguridad definidos en el Paso 2.",
    isViable: false
  };
});
EOD;

$content = str_replace($oldDiag, $newDiag, $content);

file_put_contents($file, $content);

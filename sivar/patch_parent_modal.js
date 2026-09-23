const fs = require('fs');
const file = 'src/components/ParentComparatorModal.vue';
let content = fs.readFileSync(file, 'utf8');

const oldProps = `initiallyViable: {
    type: Boolean,
    default: true
  }
});`;

const newProps = `initiallyViable: {
    type: Boolean,
    default: true
  },
  causaVeto: {
    type: String,
    default: ""
  }
});`;
content = content.replace(oldProps, newProps);

const startIndex = content.indexOf('const viabilityDiagnosis = computed(() => {');
const endIndex = content.indexOf('});', startIndex) + 3;

const newDiag = `const viabilityDiagnosis = computed(() => {
  if (isViable.value) {
    return {
      title: "DIAGNÓSTICO: COMBINACIÓN VIABLE",
      description: "Esta pareja progenitora cumple con los criterios biológicos y genéticos para la polinización en campo.",
      isViable: true
    };
  }

  if (props.causaVeto && props.causaVeto !== "-") {
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
});`;

content = content.substring(0, startIndex) + newDiag + content.substring(endIndex);

fs.writeFileSync(file, content);

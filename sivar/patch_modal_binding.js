const fs = require('fs');

function patchFile(file) {
    let content = fs.readFileSync(file, 'utf8');
    const oldTag = `<ParentComparatorModal
    v-model:isOpen="isComparatorOpen"
    :motherName="comparatorMother"
    :fatherName="comparatorFather"
    :initiallyViable="comparatorInitiallyViable"
  />`;
    const newTag = `<ParentComparatorModal
    v-model:isOpen="isComparatorOpen"
    :motherName="comparatorMother"
    :fatherName="comparatorFather"
    :initiallyViable="comparatorInitiallyViable"
    :causaVeto="comparatorCausa"
  />`;
    
    if (content.includes(oldTag)) {
        content = content.replace(oldTag, newTag);
        fs.writeFileSync(file, content);
        console.log("Patched " + file);
    } else {
        console.log("Tag not found in " + file);
    }
}

patchFile('src/views/mejoramiento/cruzamientos/CrossingMatrixView.vue');
patchFile('src/views/mejoramiento/cruzamientos/CrossingSuggestionPerProjectView.vue');

const fs = require('fs');

function fixRoots(file) {
  let content = fs.readFileSync(file, 'utf8');
  
  // Encontrar el inicio del modal
  const modalStart = '    <!-- Modal HTML de Emasculación -->';
  const modalIndex = content.indexOf(modalStart);
  
  if (modalIndex === -1) return;
  
  // Buscar el cierre del div principal justo antes del modal
  const beforeModal = content.substring(0, modalIndex);
  
  // Mover el </div> que está justo antes del modal hacia DESPUÉS del modal
  // El texto es:
  //   </div>
  // 
  //     <!-- Modal HTML de Emasculación -->
  //     <div v-if="showEmasculateModal" ...> ... </div>
  // </template>

  const closingDivMatch = /<\/div>\s*$/i.exec(beforeModal);
  if (closingDivMatch) {
      const fixedBefore = beforeModal.substring(0, closingDivMatch.index);
      
      const afterModalContent = content.substring(modalIndex);
      // afterModalContent termina en </template>
      
      const fixedContent = fixedBefore + '\n' + afterModalContent.replace('</template>', '</div>\n</template>');
      
      fs.writeFileSync(file, fixedContent);
      console.log('Fixed ' + file);
  }
}

fixRoots('src/views/mejoramiento/cruzamientos/CrossingMatrixView.vue');
fixRoots('src/views/mejoramiento/cruzamientos/CrossingSuggestionPerProjectView.vue');


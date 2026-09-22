#!/bin/bash

# Move 'variedad' to the top of tableColumns
sed -i '' '/key: "ensayo",/i\
  {\
    key: "variedad",\
    text: "Variedad"\
  },' sivar/src/views/mejoramiento/variedades/GermoplasmBankListView.vue

sed -i '' '/key: "variedad",/{
N
N
N
d
}' sivar/src/views/mejoramiento/variedades/GermoplasmBankListView.vue

# Move 'variedad' to the top of columnsToShow
sed -i '' '/"ensayo",/i\
  "variedad",' sivar/src/views/mejoramiento/variedades/GermoplasmBankListView.vue

sed -i '' '/"variedad",/{
:a
n
/"variedad",/d
}' sivar/src/views/mejoramiento/variedades/GermoplasmBankListView.vue


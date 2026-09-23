with open("sivar/src/views/mejoramiento/siembra-campo/viveros/ViveroFormView.vue", "r") as f:
    content = f.read()

import re
# Find the first import and add ComboBoxMultiple before it
match = re.search(r'import\s+', content)
if match:
    idx = match.start()
    content = content[:idx] + 'import ComboBoxMultiple from "@/components/admin/ComboBoxMultiple.vue";\n' + content[idx:]
    with open("sivar/src/views/mejoramiento/siembra-campo/viveros/ViveroFormView.vue", "w") as f:
        f.write(content)
    print("Import added")
else:
    print("No imports found??")

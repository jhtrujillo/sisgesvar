with open("src/views/mejoramiento/siembra-campo/viveros/ViveroFormView.vue", "r") as f:
    content = f.read()

content = content.replace('import ComboBoxMultiple from "@/components/admin/ComboBoxMultiple.vue";', 'import ComboBoxMultiple from "@/components/ComboBoxMultiple.vue";')

with open("src/views/mejoramiento/siembra-campo/viveros/ViveroFormView.vue", "w") as f:
    f.write(content)

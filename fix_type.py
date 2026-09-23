with open("sivar/src/views/mejoramiento/siembra-campo/viveros/ViveroFormView.vue", "r") as f:
    content = f.read()

content = content.replace("c.proyecto_id === id_prycto", "c.proyecto_id == id_prycto")
with open("sivar/src/views/mejoramiento/siembra-campo/viveros/ViveroFormView.vue", "w") as f:
    f.write(content)
print("Type coercion fixed!")

with open("sivar/src/views/mejoramiento/siembra-campo/viveros/ViveroFormView.vue", "r") as f:
    content = f.read()

content = content.replace(
    'caracteres.value = results.flat();',
    'caracteres.value = results.map(res => res.data).flat();'
)

with open("sivar/src/views/mejoramiento/siembra-campo/viveros/ViveroFormView.vue", "w") as f:
    f.write(content)
print("Fixed array flattening!")

with open("sivar/src/views/mejoramiento/siembra-campo/viveros/ViverosListView.vue", "r") as f:
    content = f.read()

if "const formatHaciendaName =" not in content:
    func = """const formatHaciendaName = (name: string) => {
  if (!name) return "N/A";
  return name.split('_')[0].trim();
};

const router = useRouter();"""
    content = content.replace("const router = useRouter();", func)

content = content.replace("{{ vivero.hacienda || \"N/A\" }}", "{{ formatHaciendaName(vivero.hacienda) }}")
content = content.replace("{{ viveroSeleccionado?.hacienda || \"N/A\" }}", "{{ formatHaciendaName(viveroSeleccionado?.hacienda) }}")

with open("sivar/src/views/mejoramiento/siembra-campo/viveros/ViverosListView.vue", "w") as f:
    f.write(content)

print("Hacienda name format fixed in ViverosListView!")

with open("sivar/src/views/mejoramiento/siembra-campo/viveros/ViveroFormView.vue", "r") as f:
    content = f.read()

content = content.replace("decodeHTMLEntities(hac.nm_hcnda)", "formatHaciendaName(hac.nm_hcnda)")

with open("sivar/src/views/mejoramiento/siembra-campo/viveros/ViveroFormView.vue", "w") as f:
    f.write(content)

with open("sivar/src/views/mejoramiento/siembra-campo/viveros/LotesView.vue", "r") as f:
    content_lotes = f.read()

if "const formatHaciendaName =" not in content_lotes:
    func = """const formatHaciendaName = (name: string) => {
  if (!name) return "";
  const decoded = decodeHTMLEntities(name);
  return decoded.split('_')[0].trim();
};

const decodeHTMLEntities = (text: string) => {"""
    content_lotes = content_lotes.replace("const decodeHTMLEntities = (text: string) => {", func)

content_lotes = content_lotes.replace("decodeHTMLEntities(hda.nm_hcnda)", "formatHaciendaName(hda.nm_hcnda)")

with open("sivar/src/views/mejoramiento/siembra-campo/viveros/LotesView.vue", "w") as f:
    f.write(content_lotes)

print("Hacienda name format fixed globally!")

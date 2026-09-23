import re

with open("sivar/src/views/mejoramiento/siembra-campo/viveros/ViveroFormView.vue", "r") as f:
    content = f.read()

# Add formatHaciendaName function
if "const formatHaciendaName =" not in content:
    func = """const formatHaciendaName = (name: string) => {
  if (!name) return "";
  const decoded = decodeHTMLEntities(name);
  return decoded.split('_')[0].trim();
};

const decodeHTMLEntities = (text: string) => {"""
    content = content.replace("const decodeHTMLEntities = (text: string) => {", func)

# Replace usage
content = content.replace("decodeHTMLEntities(hda.nm_hcnda)", "formatHaciendaName(hda.nm_hcnda)")

with open("sivar/src/views/mejoramiento/siembra-campo/viveros/ViveroFormView.vue", "w") as f:
    f.write(content)
print("Hacienda name format fixed in ViveroFormView!")

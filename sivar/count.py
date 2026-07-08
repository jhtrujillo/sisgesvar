with open('/Users/estuvar4/Documents/2. software/13. SIVAR/sivar/src/views/mejoramiento/cruzamientos/CrossingSuggestionPerProjectView.vue', 'r') as f:
    lines = f.readlines()

opens = 0
closes = 0
for i in range(21, 618):
    line = lines[i]
    opens += line.count('<div')
    closes += line.count('</div')

print(f"Opens: {opens}, Closes: {closes}")

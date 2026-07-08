with open('/Users/estuvar4/Documents/2. software/13. SIVAR/sivar/src/views/mejoramiento/cruzamientos/CrossingSuggestionPerProjectView.vue', 'r') as f:
    lines = f.readlines()

def print_balance(start, end, label):
    o = 0
    c = 0
    for i in range(start-1, end):
        o += lines[i].count('<div')
        c += lines[i].count('</div')
    print(f"{label}: Opens {o}, Closes {c}, Diff {o-c}")

print_balance(22, 258, "Before Matrix")
print_balance(259, 440, "Matrix")
print_balance(441, 618, "Drag & Drop")
print_balance(619, 703, "Botones de Nav")
print_balance(704, 765, "Final Summary")
print_balance(1, 21, "Root")

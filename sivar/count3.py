with open('/Users/estuvar4/Documents/2. software/13. SIVAR/sivar/src/views/mejoramiento/cruzamientos/CrossingSuggestionPerProjectView.vue', 'r') as f:
    lines = f.readlines()

def track_divs(start, end):
    stack = []
    for i in range(start-1, end):
        line = lines[i]
        for _ in range(line.count('<div')):
            stack.append(i+1)
        for _ in range(line.count('</div')):
            if stack:
                stack.pop()
    print(f"Unclosed divs from {start} to {end}: {stack}")

track_divs(22, 258)

import re

with open('/Users/estuvar4/Documents/2. software/13. SIVAR/sivar/src/views/mejoramiento/cruzamientos/CrossingSuggestionPerProjectView.vue', 'r') as f:
    content = f.read()

template = re.search(r'<template>(.*?)</template>', content, re.DOTALL)
if not template:
    print("No template found")
    exit()

text = template.group(1)
lines = text.split('\n')

stack = []
for i, line in enumerate(lines):
    line_num = i + 2
    
    # Very rudimentary finding of <div and </div
    # ignoring comments and multi-line attributes for a moment
    idx = 0
    while idx < len(line):
        open_idx = line.find('<div', idx)
        close_idx = line.find('</div', idx)
        
        if open_idx == -1 and close_idx == -1:
            break
            
        if open_idx != -1 and (close_idx == -1 or open_idx < close_idx):
            stack.append(line_num)
            idx = open_idx + 4
        elif close_idx != -1:
            if stack:
                start = stack.pop()
                if start == 22 or start == 259 or start == 703:
                    print(f"div opened at {start} closed at {line_num}")
            else:
                print(f"EXTRA </div found at line {line_num}")
            idx = close_idx + 5

if stack:
    print(f"UNCLOSED divs opened at: {stack}")

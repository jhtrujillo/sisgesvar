from html.parser import HTMLParser

class MyHTMLParser(HTMLParser):
    def __init__(self):
        super().__init__()
        self.stack = []
        self.errors = []
        
    def handle_starttag(self, tag, attrs):
        if tag not in ['br', 'img', 'input', 'hr', 'meta', 'link']:
            self.stack.append((tag, self.getpos()))
            
    def handle_endtag(self, tag):
        if tag not in ['br', 'img', 'input', 'hr', 'meta', 'link']:
            if not self.stack:
                self.errors.append(f"Extra closing tag {tag} at {self.getpos()}")
                return
            
            # Find matching start tag
            for i in range(len(self.stack)-1, -1, -1):
                if self.stack[i][0] == tag:
                    # Pop everything up to the matching tag
                    self.stack = self.stack[:i]
                    return
            self.errors.append(f"Mismatched closing tag {tag} at {self.getpos()}")

with open('/Users/estuvar4/Documents/2. software/13. SIVAR/sivar/src/views/mejoramiento/cruzamientos/CrossingSuggestionPerProjectView.vue', 'r') as f:
    content = f.read()

# Only parse the template part
import re
match = re.search(r'<template>(.*?)</template>', content, re.DOTALL)
if match:
    parser = MyHTMLParser()
    parser.feed(match.group(1))
    for err in parser.errors:
        print(err)
    for tag, pos in parser.stack:
        print(f"Unclosed {tag} at {pos}")
else:
    print("No template found")

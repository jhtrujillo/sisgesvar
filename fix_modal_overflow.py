with open("sivar/src/components/viveros/AddProyectoCaracterModal.vue", "r") as f:
    content = f.read()

# Remove overflow-hidden from modal container
content = content.replace(
    'class="bg-white rounded-3xl max-w-2xl w-full shadow-2xl overflow-hidden border border-slate-100 transform transition-all flex flex-col"',
    'class="bg-white rounded-3xl max-w-2xl w-full shadow-2xl border border-slate-100 transform transition-all flex flex-col"'
)

# Add rounded corners to header
content = content.replace(
    'class="p-5 bg-gradient-to-r from-slate-900 via-slate-800 to-emerald-950 text-white flex items-center justify-between"',
    'class="p-5 bg-gradient-to-r from-slate-900 via-slate-800 to-emerald-950 text-white flex items-center justify-between rounded-t-3xl"'
)

# Add rounded corners to footer
content = content.replace(
    'class="p-5 bg-white border-t border-slate-100 flex justify-end gap-3"',
    'class="p-5 bg-white border-t border-slate-100 flex justify-end gap-3 rounded-b-3xl"'
)

# Remove overflow-y-auto from body and add min-h
content = content.replace(
    'class="p-6 bg-slate-50 flex-1 overflow-y-auto space-y-6"',
    'class="p-6 bg-slate-50 flex-1 overflow-visible space-y-6 min-h-[350px]"'
)

with open("sivar/src/components/viveros/AddProyectoCaracterModal.vue", "w") as f:
    f.write(content)
print("Overflow fixed!")

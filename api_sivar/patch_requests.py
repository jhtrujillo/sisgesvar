import glob

for path in glob.glob("app/Http/Requests/*ViveroRequest.php"):
    with open(path, "r") as f:
        content = f.read()
    
    content = content.replace("'proyecto_id' => 'required|integer", "'proyecto_id' => 'nullable|integer")
    
    if "'proyectos'" not in content:
        content = content.replace("'proyecto_id'", "'proyectos' => 'nullable|array',\n            'proyectos.*' => 'integer|exists:sivar.remote_pg_sipro,id_prycto',\n            'proyecto_id'")
        
    with open(path, "w") as f:
        f.write(content)
print("Requests patched")

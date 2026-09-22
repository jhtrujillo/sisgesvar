with open("app/Models/Vivero.php", "r") as f:
    content = f.read()

new_rel = """    public function proyectos()
    {
        return $this->belongsToMany(\App\Models\Proyecto::class, 'vivero_proyectos', 'vivero_id', 'proyecto_id', 'id', 'id_prycto')->withTimestamps();
    }

    public function proyecto()"""

content = content.replace("    public function proyecto()", new_rel)

with open("app/Models/Vivero.php", "w") as f:
    f.write(content)
print("Vivero model patched")

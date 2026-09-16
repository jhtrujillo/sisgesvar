sed -i '' 's/->paginate(10)/->paginate($request->query("per_page", 10))/g' app/Http/Controllers/VarietyController.php

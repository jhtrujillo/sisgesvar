# 1. Update Controller to accept 'search' and filter by 'variedad' or 'ensayo'
sed -i '' '/\$model = DB::connection/c\
            $query = DB::connection("sivar")->table("caracterizacion_banco_germoplasma");\
            $search = $request->query("search");\
            if (!empty($search)) {\
                $query->where("variedad", "ilike", "%" . $search . "%")\
                      ->orWhere("ensayo", "ilike", "%" . $search . "%")\
                      ->orWhere("madre", "ilike", "%" . $search . "%")\
                      ->orWhere("padre", "ilike", "%" . $search . "%");\
            }\
            $model = $query->paginate($request->query("perPage", 15));\
' app/Http/Controllers/VarietyController.php


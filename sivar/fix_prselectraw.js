const fs = require('fs');
const file = '../api_sivar/app/Http/Controllers/VarietyController.php';
let content = fs.readFileSync(file, 'utf8');

const oldRaw = `$prSelectRaw = "
                    avg(CAST(REPLACE(CAST(mosaico AS TEXT), ',', '.') AS FLOAT)) as mosaico_p,
                    avg(CAST(REPLACE(CAST(roya AS TEXT), ',', '.') AS FLOAT)) as roya_cafe_r,
                    avg(CAST(REPLACE(CAST(\\"179\\" AS TEXT), ',', '.') AS FLOAT)) as roya_naranja_r,
                    avg(CAST(REPLACE(CAST(carbon AS TEXT), ',', '.') AS FLOAT)) as carbon_p,
                    avg(CAST(REPLACE(CAST(\\"163\\" AS TEXT), ',', '.') AS FLOAT)) as sacarosa,
                    avg(CAST(REPLACE(CAST(\\"173\\" AS TEXT), ',', '.') AS FLOAT)) as tchm,
                    avg(CAST(REPLACE(CAST(\\"Tallo Altura (cm)\\" AS TEXT), ',', '.') AS FLOAT)) as altura_planta,
                    avg(CAST(REPLACE(CAST(\\"DiametroTallo\\" AS TEXT), ',', '.') AS FLOAT)) as diametro_tallo,
                    MAX(CAST(estdo_slccion AS TEXT)) as procedencia
                ";`;

const newRaw = `$prSelectRaw = "
                    AVG(CASE WHEN TRIM(REPLACE(mosaico::text, ',', '.')) ~ '^\\\\d+(\\\\.\\\\d+)?$$' THEN TRIM(REPLACE(mosaico::text, ',', '.'))::double precision ELSE NULL END) as mosaico_p,
                    AVG(CASE WHEN TRIM(REPLACE(roya::text, ',', '.')) ~ '^\\\\d+(\\\\.\\\\d+)?$$' THEN TRIM(REPLACE(roya::text, ',', '.'))::double precision ELSE NULL END) as roya_cafe_r,
                    AVG(CASE WHEN TRIM(REPLACE(\\"179\\"::text, ',', '.')) ~ '^\\\\d+(\\\\.\\\\d+)?$$' THEN TRIM(REPLACE(\\"179\\"::text, ',', '.'))::double precision ELSE NULL END) as roya_naranja_r,
                    AVG(CASE WHEN TRIM(REPLACE(carbon::text, ',', '.')) ~ '^\\\\d+(\\\\.\\\\d+)?$$' THEN TRIM(REPLACE(carbon::text, ',', '.'))::double precision ELSE NULL END) as carbon_p,
                    AVG(CASE WHEN TRIM(REPLACE(\\"163\\"::text, ',', '.')) ~ '^\\\\d+(\\\\.\\\\d+)?$$' THEN TRIM(REPLACE(\\"163\\"::text, ',', '.'))::double precision ELSE NULL END) as sacarosa,
                    AVG(CASE WHEN TRIM(REPLACE(\\"173\\"::text, ',', '.')) ~ '^\\\\d+(\\\\.\\\\d+)?$$' THEN TRIM(REPLACE(\\"173\\"::text, ',', '.'))::double precision ELSE NULL END) as tchm,
                    AVG(CASE WHEN TRIM(REPLACE(\\"Tallo Altura (cm)\\"::text, ',', '.')) ~ '^\\\\d+(\\\\.\\\\d+)?$$' THEN TRIM(REPLACE(\\"Tallo Altura (cm)\\"::text, ',', '.'))::double precision ELSE NULL END) as altura_planta,
                    AVG(CASE WHEN TRIM(REPLACE(\\"DiametroTallo\\"::text, ',', '.')) ~ '^\\\\d+(\\\\.\\\\d+)?$$' THEN TRIM(REPLACE(\\"DiametroTallo\\"::text, ',', '.'))::double precision ELSE NULL END) as diametro_tallo,
                    MAX(CAST(estdo_slccion AS TEXT)) as procedencia
                ";`;

if (content.includes('avg(CAST(REPLACE(CAST(mosaico AS TEXT)')) {
  // Use a function for replacement to avoid ANY $ special patterns
  content = content.replace(oldRaw, () => newRaw.replace(/\$\$/g, '$'));
  fs.writeFileSync(file, content);
  console.log("Replaced using Node safely!");
} else {
  console.log("String not found. Check exact formatting.");
}

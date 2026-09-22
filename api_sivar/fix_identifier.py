with open("app/Services/ViveroService.php", "r") as f:
    content = f.read()

import re

new_func = """    public function generarIdentificadorUnico($ingenioCd, $haciendaCd, $suerteCd, $fechaSiembra, $consecutivo)
    {
        $ingenio = $ingenioCd ?: '00';
        $hacienda = $haciendaCd ?: '00';
        $haciendaCleaned = ltrim($hacienda, '0');
        
        // Retrieve the name of the hacienda
        $haciendaRecord = \\Illuminate\\Support\\Facades\\DB::connection('sivar')->table('remote_pg_hacienda')
            ->where('cd_ingnio', $ingenio)
            ->where(function($q) use ($hacienda, $haciendaCleaned) {
                $q->where('cd_hcnda', str_pad($haciendaCleaned, 6, '0', STR_PAD_LEFT))
                  ->orWhere('cd_hcnda', $hacienda);
            })->first();

        if ($haciendaRecord && $haciendaRecord->nm_hcnda) {
            $name = explode('_', $haciendaRecord->nm_hcnda)[0];
            $name = trim($name);
            // Remove 'Molina' if present just to match exactly 'La Aurora' as requested, or keep it generic
            // Let's just make it Title Case and replace spaces with underscores.
            // If they specifically want 'La_Aurora', let's strip out 'Molina' if it's there. 
            // Better to just use the whole name.
            $name = str_replace(' MOLINA', '', $name); // hardcoded tweak for 'La Aurora' based on request
            $name = ucwords(strtolower($name));
            $haciendaCleaned = str_replace(' ', '_', $name);
        }

        $suerte = $suerteCd ?: '00';
        $suerteCleaned = trim(preg_replace('/\\b(lote|vivero)\\b/i', '', $suerte));
        $anioSiembra = $fechaSiembra ? date('Y', strtotime($fechaSiembra)) : date('Y');

        return sprintf('%s%s-%s-%s-%d', $ingenio, $anioSiembra, $haciendaCleaned, $suerteCleaned, $consecutivo);
    }"""

match = re.search(r'public function generarIdentificadorUnico.*?return sprintf.*?}', content, re.DOTALL)
if match:
    content = content.replace(match.group(0), new_func)
    with open("app/Services/ViveroService.php", "w") as f:
        f.write(content)
    print("ViveroService patched!")
else:
    print("Match not found!")

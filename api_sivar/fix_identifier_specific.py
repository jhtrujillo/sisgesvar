with open("app/Services/ViveroService.php", "r") as f:
    content = f.read()

import re

new_func = """    public function generarIdentificadorUnico($ingenioCd, $haciendaCd, $suerteCd, $fechaSiembra, $consecutivo)
    {
        $ingenio = $ingenioCd ?: '00';
        $hacienda = $haciendaCd ?: '00';
        $haciendaCleaned = ltrim($hacienda, '0');
        
        // Specific exception requested by the user for Hacienda 620
        if ($haciendaCleaned === '620') {
            $haciendaCleaned = 'La_Aurora';
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
    print("ViveroService patched specifically for 620!")
else:
    print("Match not found!")

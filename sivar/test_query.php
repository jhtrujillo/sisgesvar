<?php
$file = '../api_sivar/app/Services/CrossingService.php';
$content = file_get_contents($file);

// check if it has count already somewhere
if (strpos($content, 'cantidad_flores') !== false) {
    echo "Already has cantidad_flores";
} else {
    echo "No cantidad_flores";
}

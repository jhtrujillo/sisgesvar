<?php
$file = 'src/stores/crossingmatrix.ts';
$content = file_get_contents($file);

$content = str_replace(
    'const getMatrixCrossingList = async (proyectos: string, proyecto: string, testigo: string, ambiente: string): Promise<void> => {',
    'const getMatrixCrossingList = async (proyectos: string, proyecto: string, testigo: string, ambiente: string, caracter?: string | null): Promise<void> => {',
    $content
);
$content = str_replace(
    'const result = await CrossingsService.getMatrix(proyectos, proyecto, testigo, ambiente);',
    'const result = await CrossingsService.getMatrix(proyectos, proyecto, testigo, ambiente, caracter);',
    $content
);

file_put_contents($file, $content);

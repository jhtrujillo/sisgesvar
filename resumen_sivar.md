# Resumen del Proyecto SIVar para Presentación

## Contexto General y Arquitectura
SIVar es un avanzado Sistema de Gestión Agrícola y de Fitomejoramiento. Su objetivo es digitalizar, centralizar y potenciar las operaciones de investigación genética, biológica y de campo. 
La arquitectura técnica consta de un Backend robusto en Laravel (PHP) para reglas de negocio complejas y un Frontend reactivo en Vue.js 3 / Vite para dashboards en tiempo real.

## SIVar como Data Lake
El sistema actúa como un **Data Lake Especializado** para fitomejoramiento. En lugar de tener archivos separados por departamento, SIVar unifica:
- **Datos de Campo:** Viveros, ensayos, cosechas y fechas de floración.
- **Datos Genéticos:** Genealogías (Parents Diagram) y catálogos de bancos de germoplasma.
- **Datos Moleculares/Bioinformáticos:** Resultados de BLAST y métricas de secuencias genómicas (Sequence Server / JBrowse).

Esta centralización (Single Source of Truth) permite cruzar variables que antes estaban aisladas. Por ejemplo: relacionar secuencias genómicas de una planta con su desempeño real (Valor de Mérito) en el campo.

## Puntos de Control y Chequeo (Checkpoints)
Para garantizar que los datos en el Data Lake sean 100% confiables, SIVar incorpora múltiples puntos de control:
1. **Filtros de Importación:** Endpoints como `validate-import` y `standardization/preview` evitan que datos erróneos de Excel entren a la base de datos.
2. **Estandarización de Catálogos:** Obliga a los usuarios a utilizar diccionarios de variables cerrados (Catalogues), previniendo errores tipográficos en las evaluaciones cualitativas.
3. **Flujos de Estado Obligatorios:** Los viveros y ensayos tienen estados definidos. Por ejemplo, una parcela requiere `marcar-cosechado` y cumplir hitos estructurales para pasar a un histórico, garantizando la trazabilidad.

## Matemáticas y Lógica de Cruzamientos
El corazón del módulo de Breeding es su **motor de cálculo y sugerencias de cruzamientos**. 
A diferencia de sistemas manuales, SIVar aplica algoritmos matemáticos en tiempo real basados en los "Ponderados" (pesos de las características).

### 1. Cálculo de Viabilidad (Threshold Logics)
Antes de sugerir un cruce entre la Flor A y la Flor B, el sistema evalúa si las características negativas no superan un umbral máximo frente a un `testigo`.
La fórmula lógica evaluada por el `CrossingService` es:
`Viabilidad = (Nivel_FlorA + Nivel_FlorB) <= Nivel_Ponderado_Máximo`
Si esta suma supera el nivel máximo tolerado, el cruce se descarta automáticamente.

### 2. Valor de Mérito (VM)
Para rankear los cruces sugeridos, se calcula un Valor de Mérito por característica:
`Valor de Mérito Parcial = (Ponderado × Nivel_Evaluación) / 100`

El sistema suma el VM de todas las características para la Flor A y Flor B, entregando un score global consolidado que le dice al mejorador genético: *"De las miles de combinaciones en la matriz, estas son las Top 5 matemáticamente más prometedoras"*.

### 3. Matriz de Cruzamientos (Sugerencias Inteligentes)
A través del endpoint `suggestionCrossings`, el sistema genera combinaciones cartesianas (N x M) entre las flores disponibles, aplica los filtros de Viabilidad y puntúa cada par con su Valor de Mérito, reduciendo semanas de análisis manual a milisegundos.

## Integraciones Avanzadas (Bioinformática)
- **BLAST Nativo (`linux-blast-runner`):** Ejecución de algoritmos locales de alineamiento de secuencias (ADN/ARN).
- **BioJava (`biojava-runner`):** Cálculos estructurales biológicos avanzados directamente interconectados con las métricas de campo.
- **Dashboards (BI):** Módulos como `DashboardVariedades` y `DashboardBioinformatica` traducen los datos crudos en gráficas, eliminando la dependencia de herramientas externas.

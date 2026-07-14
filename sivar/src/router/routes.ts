// Variables de entorno de cada ruta
export const ROUTE_HOME = "/";
export const ROUTE_LOGIN = "/login";
export const ROUTE_USERS = "/users";

//Bioinformatica
export const ROUTE_BIOINFORMATICA = "bioinformatica/";
export const ROUTE_SEQUENCE_SERVER = "bioinformatica/sequence_server/";
export const ROUTE_JBROWSE = "bioinformatica/jbrowse/";

//BioJava
export const ROUTE_BIOJAVA = "biojava/";
export const ROUTE_COMP_GEN = "biojava/comp-gen/";
export const ROUTE_BLAST_NATIVO = "biojava/blast-nativo/";

// Laboratorio
export const ROUTE_LABORATORIO = "laboratorio/";
export const ROUTE_INVENTARIO_LAB = "laboratorio/inventario/";


export const ROUTE_MEJORAMIENTO = "mejoramiento/";
export const ROUTE_VARIEDADES = "mejoramiento/variedades/";
export const ROUTE_CRUZAMIENTOS = "mejoramiento/cruzamientos/";
export const ROUTE_FLOWERING_LIST = "mejoramiento/flowering_list/";
export const ROUTE_VARIETYS_LIST = "mejoramiento/varietys_list/";
export const ROUTE_CROSSING_LIST = "mejoramiento/crossing_list/";
export const ROUTE_VARIETY_HISTORY = "mejoramiento/variety_history/";
export const ROUTE_GERMOPLASM_BANK = "mejoramiento/germoplasm_bank/";
export const ROUTE_PARENTS_DIAGRAM_LEVEL = "mejoramiento/parents_diagram_level/";
export const ROUTE_CROSSING_INITIAL_DATA = "mejoramiento/crossing_initial_data/";
export const ROUTE_CROSSING_WEIGHTED = "mejoramiento/crossing_weighted/";
export const ROUTE_CROSSING_SUGGESTION = "mejoramiento/crossing_suggestion/";
export const ROUTE_CROSSING_SUGGESTION_PER_PROJECT = "mejoramiento/crossing_suggestion_per_project/";
export const ROUTE_CROSSING_MATRIX = "mejoramiento/crossing_matrix/";

//EXperimentos
export const ROUTE_EXPERIMENTS = "mejoramiento/experimentos/";

// Registro Ensayos
export const ROUTE_ENSAYOS_INDEX = "mejoramiento/ensayos/";
export const ROUTE_ENSAYOS_DASHBOARD = "mejoramiento/ensayos/dashboard/";
export const ROUTE_ENSAYOS_CATALOGOS = "mejoramiento/ensayos/catalogos/";
export const ROUTE_ENSAYOS_ACTIVIDADES = "mejoramiento/ensayos/actividades/";

export const ROUTE_ABOUT = "about/";

export const ERROR_404 = "";

// Siembra-Campo
export const ROUTE_SIEMBRA_CAMPO_VIVEROS = "mejoramiento/siembra-campo/viveros/";
export const ROUTE_SIEMBRA_CAMPO_VIVERO_NUEVO = "mejoramiento/siembra-campo/viveros/nuevo/";
export const ROUTE_SIEMBRA_CAMPO_VIVERO_EDITAR = "mejoramiento/siembra-campo/viveros/editar/:id/";

export const NON_SIGNIFICANT_ROUTES = [
  ROUTE_HOME,
  ROUTE_LOGIN,
  ROUTE_USERS,

  ROUTE_BIOINFORMATICA,
  ROUTE_BIOJAVA,
  ROUTE_COMP_GEN,
  ROUTE_BLAST_NATIVO,
  ROUTE_SEQUENCE_SERVER,
  ROUTE_JBROWSE,
  ROUTE_LABORATORIO,
  ROUTE_INVENTARIO_LAB,
  ROUTE_MEJORAMIENTO,
  ROUTE_VARIEDADES,
  ROUTE_CRUZAMIENTOS,
  ROUTE_FLOWERING_LIST,
  ROUTE_VARIETYS_LIST,
  ROUTE_CROSSING_LIST,
  ROUTE_VARIETY_HISTORY,
  ROUTE_GERMOPLASM_BANK,
  ROUTE_PARENTS_DIAGRAM_LEVEL,
  ROUTE_CROSSING_INITIAL_DATA,
  ROUTE_CROSSING_WEIGHTED,
  ROUTE_CROSSING_MATRIX,
  ROUTE_CROSSING_SUGGESTION,
  ROUTE_CROSSING_SUGGESTION_PER_PROJECT,
  ROUTE_EXPERIMENTS,
  ROUTE_ENSAYOS_INDEX,
  ROUTE_ENSAYOS_DASHBOARD,
  ROUTE_ENSAYOS_CATALOGOS,
  ROUTE_ENSAYOS_ACTIVIDADES,
  ROUTE_SIEMBRA_CAMPO_VIVEROS,
  ROUTE_SIEMBRA_CAMPO_VIVERO_NUEVO,
  ROUTE_SIEMBRA_CAMPO_VIVERO_EDITAR,
  ROUTE_ABOUT,
  ERROR_404
];

const SERVER_URL = import.meta.env.VITE_API_URL || " http://127.0.0.1:8000";
const API_URL = SERVER_URL + "/api/";
const API_AUTH = API_URL + "auth/";
const API_AUTH_LOGIN = API_AUTH + "login";

const API_AUTH_USER_INFO = API_AUTH + "me/";
const API_AUTH_REFRESH_TOKEN = API_AUTH + "token/refresh";

//URLS Siembra-Campo
const API_VIVEROS = API_URL + "siembra-campo/viveros";
const API_INGENIOS = API_URL + "siembra-campo/ingenios";
const API_HACIENDAS = API_URL + "siembra-campo/haciendas";
const API_SUERTES = API_URL + "siembra-campo/suertes";

// URLS USERs
const API_USERS = API_URL + "users";
const API_LINKS = API_URL + "links";
const API_ALIGNMENTS = API_URL + "processes";
const API_ALIGNMENTSLIST = API_URL + "processesList";

// URLS Varietys
const API_FLOWERINGLIST = API_URL + "floweringList";
const API_VARIETYSLIST = API_URL + "varietysList";
const API_VARIETY = API_URL + "variety";
const API_VARIETY_PROFILE = API_URL + "varietyProfile";
const API_VARIETY_HISTORY = API_URL + "historyDatatable";
const API_GERMOPLASM_BANK = API_URL + "germoplasmBankList";
const API_PARENTS_DIAGRAM = API_URL + "getParents";
const API_PARENTS_DIAGRAM_LEVEL = API_URL + "getParentsLevel";

//URLS Crossings
const API_CROSSING_LIST = API_URL + "crossingList";
const API_CROSSING_INITIAL_DATA = API_URL + "crossingInitialData";
const API_PARAMETIZE_WEIGHTED_CROSSING = API_URL + "parametizeWeightedCrossing";
const API_MODIFY_FEATURES_CROSSING = API_URL + "modifyFeatures";
const API_GENERATE_MATRIX = API_URL + "generateMatrix";
const API_SUGGESTION_CROSSING = API_URL + "suggestionCrossings";
const API_SUGGESTION_CROSSING_PER_PROJECT = API_URL + "suggestionCrossingsPerProject";

// URLS Experiments
const API_SEARCH_PARAMETERS = API_URL + "getSearchParameters";
const API_AREAS_PROGRAM = API_URL + "getAreasProgram";
const API_PROJECTS_AREA = API_URL + "getProjectsArea";
const API_EXPERIMENT = API_URL + "getExperiment";
const API_TREATMENTS_SEASON = API_URL + "getTreatmentsSeason";
const API_TREATMENTS_EXPERIMENTS = API_URL + "getTreatmentsExperiments";
const API_ADD_DESIGNS_DETAILS = API_URL + "addDesingsDetails";

// URLS Registro Ensayos
const API_ENSAYOS = API_URL + "ensayos";
const API_CATALOGOS = API_URL + "catalogos";
const API_ACTIVIDADES = API_URL + "actividades";

export default {
  SERVER_URL,
  API_URL,
  API_AUTH,
  API_AUTH_LOGIN,
  API_AUTH_USER_INFO,
  API_AUTH_REFRESH_TOKEN,
  API_USERS,
  API_LINKS,
  API_ALIGNMENTS,
  API_ALIGNMENTSLIST,
  API_FLOWERINGLIST,
  API_CROSSING_LIST,
  API_VARIETYSLIST,
  API_VARIETY_HISTORY,
  API_VARIETY,
  API_VARIETY_PROFILE,
  API_GERMOPLASM_BANK,
  API_PARENTS_DIAGRAM_LEVEL,
  API_PARENTS_DIAGRAM,
  API_CROSSING_INITIAL_DATA,
  API_PARAMETIZE_WEIGHTED_CROSSING,
  API_MODIFY_FEATURES_CROSSING,
  API_GENERATE_MATRIX,
  API_SUGGESTION_CROSSING,
  API_SUGGESTION_CROSSING_PER_PROJECT,
  API_SEARCH_PARAMETERS,
  API_AREAS_PROGRAM,
  API_PROJECTS_AREA,
  API_EXPERIMENT,
  API_TREATMENTS_SEASON,
  API_TREATMENTS_EXPERIMENTS,
  API_ADD_DESIGNS_DETAILS,
  API_ENSAYOS,
  API_CATALOGOS,
  API_ACTIVIDADES,
  API_VIVEROS,
  API_INGENIOS,
  API_HACIENDAS,
  API_SUERTES,
  API_PROYECTOS: API_URL + "siembra-campo/proyectos",
  API_RESPONSABLES: API_URL + "siembra-campo/responsables",
  API_AMBIENTES: API_URL + "siembra-campo/ambientes"
};

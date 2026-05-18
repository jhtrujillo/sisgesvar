// Tipado de cada campo, para que cada formulario reciba los parámetros de manera correcta

interface Login {
  usuario: string;
  clave: string;
}
type BasicType = string | boolean | number;
interface AnyObject {
  [key: string]: any;
}
interface Alignments {
  parametros: Parametros;
  // comando: string;
}

interface Parametros {
  genomaReferencia: string;
  individuo: string;
  ngs: string;
  comando: string;
  usuario: string;
}
interface AlignmentsList {
  ID: string;
  OWNER: string;
  SUBMITTED: string;
  RUN_TIME: string;
  ST: string;
  COMPLETED: string;
  CMD: string;
}

interface User {
  id_usrio: string;
  cdla: string;
  lgin: string;
  clve: string;
  nmbre: string;
  email: string;
  crgo: string;
  id_area: string;
  sexo: string;
  fcha_ncmnto: string;
  fcha_ingrso: string;
  drccion: string;
  cdad: string;
  extnsion: string;
  estdo: string;
  fcha_rtro: string;
  tpo_cntrto: string;
  id_prfil: string;
  tpo: string;
  emprsa: string;
  prmer_nmbre: string;
  aplldo: string;
  incles: string;
  fc_mdfccion: string;
  id_area_trbjo: string;
  last_login: string;
  is_superuser: string;
  is_staff: string;
}

interface Links {
  id: number;
  name: string;
  url: string;
}
interface FLowering {
  id_flrcion: string;
  hcnda: string;
  fcha: string;
  hra: string;
  prcla: string;
  srco: string;
  vrdad: string;
  flrcion: string;
  grpo: string;
  polen: string;
  grnos_vbles1: string;
  ttal_grnos1: string;
  grnos_vbles2: string;
  ttal_grnos2: string;
  grnos_vbles3: string;
  ttal_grnos3: string;
  grnos_vbles4: string;
  ttal_grnos4: string;
  grnos_vbles5: string;
  ttal_grnos5: string;
  sxo: string;
  slcciondo: string;
  cmbio_sxo: string;
  nm_prycto: string;
  nmbre_crcter: string;
  vivero: string;
  obsrvcn: string;
  usuario: string;
  ingnio: string;
  id_smbra_cmpo: string;
  lte: string;
}
interface Varietys {
  nm_vrdad: string;
  vrdad_madre: string;
  vrdad_pdre: string;
  tpo: string;
  [key: string]: any;
}
interface Crossings {
  id_crzmnto: string;
  pdgree: string;
  vrdad_mdre: string;
  vrdad_pdre1: string;
  vrdad_pdre2: string;
  vrdad_pdre3: string;
  vrdad_pdre4: string;
  vrdad_pdre5: string;
}
interface Variety {
  id_nm_vrdad: string;
  nm_vrdad: string;
  [key: string]: any;
}

interface HistoryVariety {
  id_crzmnto: string;
  vrdad_mdre: string;
  vrdad_pdre1: string;
  ano: string;
  pdgree: string;
  nm_fmlias: string;
  plntlas_ttles: string;
  id_stio_estcion: string;
  [key: string]: any;
}
interface TreeItem {
  name: string;
  type: string;
  nivel: number;
  children?: TreeItem[];
}

interface TreeData {
  headers: any; // Ajusta el tipo según los headers específicos que puedas tener
  original: TreeItem[];
  exception: any | null; // Ajusta el tipo según las excepciones que puedas manejar
}
interface Germoplasma {
  caracterizacion_id: string;
  ensayo: string;
  sitio_seleccion: string;
  estado_seleccion: string;
  serie: string;
  ingenio: string;
  hacienda: string;
  suerte: string;
  za: string;
  gs: string;
  gh: string;
  origen: string;
  area: string;
  variedad: string;
  madre: string;
  padre: string;
  grupo_snp: string;
  grupo_fenotipico: string;
  spp_hibrido: string;
  procedencia: string;
  estacion: string;
  especie: string;
  rep: string;
  block: string;
  plot: string;
  entry: string;
  col: string;
  chk: string;
  corte: string;
  tllo: string;
  fs: string;
  fc: string;
  dds: string;
  fcha_eval_id: string;
  fcha_eval: string;
  tubos: string;
  spad: string;
  raiz_nf: string;
  altura_planta: string;
  numero_entrenudos: string;
  longitud_entrenudo: string;
  longitud_cogollo: string;
  diametro_1_3: string;
  diametro_2_3: string;
  diametro_3_3: string;
  diametro_tallo: string;
  longitud_hoja: string;
  ancho_hoja_1_3: string;
  ancho_hoja_2_3: string;
  ancho_hoja_3_3: string;
  poblacion_1m: string;
  floracion_tllos: string;
  floracion_p: string;
  aspecto_planta: string;
  aspecto_seleccion: string;
  pelusa: string;
  volcamiento: string;
  deshoje: string;
  materia_seca: string;
  humedad: string;
  sacarosa: string;
  brix: string;
  fibra: string;
  no_sacarosa: string;
  pureza: string;
  are: string;
  reductores: string;
  atr: string;
  peso: string;
  tch: string;
  tah: string;
  tsh: string;
  tchm: string;
  tahm: string;
  tshm: string;
  roya_cafe_r: string;
  roya_cafe_s: string;
  roya_naranja_r: string;
  roya_naranja_s: string;
  mosaico_r: string;
  mosaico_e: string;
  mosaico_t: string;
  mosaico_p: string;
  carbon_c: string;
  carbon_l: string;
  carbon_t: string;
  carbon_p: string;
  lsdte: string;
  lsdtt: string;
  lsdtv: string;
  lsdt: string;
  rsd: string;
  sclyv: string;
  te: string;
  ed: string;
  eb: string;
  id: string;
  ib: string;
  tallo_evaluados: string;
  tallo_rajados: string;
  rajadura_inc: string;
  entrenudos_tallo: string;
  entrenudos_rajados: string;
  rajadura_sev: string;
  hojas_erectas: string;
  raices_tallos: string;
  yemas_protuberantes: string;
  medula: string;
  habito_de_crecimiento: string;
  germinacion: string;
  tolerancia_herbicida: string;
  raices_adventicias: string;
  obsrvcnes: string;
}
interface CrossingInitialData {
  nm_prycto: string;
  cd_cntble: string;
  id_prycto: string;
  numero: string;
}
interface Weighted {
  id_proyecto: string | null;
  ponderado: string | null;
  id_caracteristica: string;
  nivel: string | null;
  id_ponderado: string | null;
  nombre: string;
}

interface ParametizeWeighted {
  proyectos: string;
  ponderados: Weighted[];
  suma_ponderados: number;
  ambiente: string;
}
interface ModifyWeighted {
  id_proyecto: string | null;
  ponderado: string | null;
  caracteristica: number;
  nivel: number | null;
  id_ponderado: string | null;
  nombre: string;
  nuevo: number;
}

interface Proyecto {
  id_prycto: number;
  cd_cntble: string;
}

interface Flor {
  numero: number;
  vrdad: string;
  id_pr: number;
  id_crcter: number;
  sxo: string;
  polen: string;
  msco_r: string | null;
  rya_cfe_r: string | null;
  roya_naranja: string | null;
  carbon: string | null;
  tchm: string | null;
  dmtro_tllo: string;
  volcamiento: string;
  altura_planta: string;
  poblacion: string;
  scrsa: string;
}

interface Viabilidad {
  varA: string;
  varB: string;
  viabilidad: boolean;
  vm: number;
  vm2: number;
  polen: string;
  polen2: string;
  proyecto: number;
  proyecto2: number;
  caracter: string;
  caracter2: string;
  nombre_proyecto: string;
  nombre_proyecto2: string;
  id_caracter: number;
  id_caracter2: number;
}

interface Distancias {
  [key: string]: {
    [key: string]: string | null;
  };
}

interface SuggestionCrossings {
  proyectos: Proyecto[];
  proyecto: Proyecto;
  fecha_i: string;
  fecha_f: string;
  flores: Flor[];
  testigo: string;
  viabilidades: Viabilidad[][][];
  distancias: Distancias;
  ambiente: string;
}
interface Flor1 {
  numero: number;
  vrdad: string;
  id_pr: number;
  id_crcter: number;
}
interface SuggestionCrossingsPerProject {
  flores: Flor1[];
  viabilidades: Viabilidad[][];
  distancias: Distancias;
}

interface Matrix {
  proyectos: string;
  proyecto: string;
  fecha_i: string;
  fecha_f: string;
  flores: Flor[];
  viabilidad: Viabilidad[][];
  distancias: Distancias;
  testigo: string;
}
interface CruzamientoSeleccionado {
  varA: string;
  varB: string;
  viabilidad: boolean;
  vm: string;
  vm2: string;
}

//Experimentos
interface listProgramas {
  id: string;
  text: string;
}
interface listAreas {
  id: string;
  text: string;
}
interface listSeries {
  id: string;
  text: string;
}
interface listEstados {
  id: string;
  text: string;
}
interface listTemporadas {
  id: string;
  text: string;
}
interface listCruzamientoMadre {
  id: string;
  text: string;
}
interface listCruzamientoPadre {
  id: string;
  text: string;
}
interface listTipoEnsayo {
  id: string;
  text: string;
}
interface listTipoParcela {
  id: string;
  text: string;
}
interface listDisenoExp {
  id: string;
  text: string;
}
interface Variables {
  nmro_cmpo: string;
  nmbre_cmpo: string;
}
interface listVariables {
  area: string;
  variables: Variables[];
}
interface listProyectos {
  id: string;
  text: string;
  id_area_trbjo: string;
  nm_area_trbjo: string;
}
interface searchParameters {
  listProgramas: listProgramas[];
  listAreas: listAreas[];
  listProyectos: listProyectos[];
  listSeries: listSeries[];
  listEstados: listEstados[];
  listTemporadas: listTemporadas[];
  listCruzamientoMadre: listCruzamientoMadre[];
  listCruzamientoPadre: listCruzamientoPadre[];
  listTipoEnsayo: listTipoEnsayo[];
  listTipoParcela: listTipoParcela[];
  listDisenoExp: listDisenoExp[];
  listVariables: listVariables[];
}

interface Detalle {
  id_dsno_det: string;
  id_dsno_enc: string;
  entrda: string;
  trtmnto: string;
  tstgo: string;
  nmro_clnes: string;
  tpo_prcla: string;
}

interface ProyectoArea {
  nmbre: string;
  nm_area_trbjo: string;
  id_area_trbjo: string;
  id_area: string;
}

interface ProyectoExperiment {
  id_prycto: string;
  id_area: string;
  id_area_trbjo: string;
  nm_prycto: string;
  estdo: string;
  cd_cntble: string;
  area: ProyectoArea;
}

interface Experimento {
  id_dsno_enc: string;
  id_dsno_exprmntal: string;
  id_ambnte: string;
  estdo: string;
  srie: string;
  entrdas: string;
  lclddes: string;
  rptcnes: string;
  blques: string;
  prcla_prncpal: string;
  sub_prclas: string;
  dscrpcion: string;
  exste_dsno: string;
  tstgos: string;
  tstgos_mvil: string;
  nm_ensyo: string;
  id_pr: string;
  dscrpcion_exprmnto: string | null;
  tpo_ensyo: string;
  proyecto: ProyectoExperiment;
  detalle: Detalle[];
}

interface Experimentos {
  success: string;
  experimento: Experimento[];
}
interface Tratamiento {
  id_crzmnto: string;
  no_crzmnto: string;
  pdgree: string;
  orgen: string;
  plntlas_ttles: string;
  grpo_crzmnto: string;
  grpo_crzmnto_mdre: string;
  grpo_crzmnto_pdre: string;
}
interface TratamientosTemporada {
  success: string;
  tratamientos: Tratamiento[];
}
interface tratamientosF {
  id_dsno_det: string;
  id_dsno_enc: string;
  trtmnto: string;
  no_crzmnto: string;
  pdgree: string;
  orgen: string;
  nmro_clnes: string;
  plntlas_ttles: string;
}
interface tratamientosF {
  id_dsno_det: string;
  id_dsno_enc: string;
  trtmnto: string;
  no_crzmnto: string;
  pdgree: string;
  orgen: string;
  nmro_clnes: string;
  plntlas_ttles: string;
}
interface tratamientosI {
  id_dsno_det: string;
  id_dsno_enc: string;
  trtmnto: string;
  no_crzmnto: string;
  pdgree: string;
  orgen: string;
  nmro_clnes: string;
  plntlas_ttles: string;
}
interface testigosFijosF {
  id_dsno_det: string;
  nm_vrdad: string;
  pdgree: string;
  orgen: string;
}
interface testigosMovilesF {
  id_dsno_det: string;
  nm_vrdad: string;
  pdgree: string;
  orgen: string;
}
interface testigosFijosI {
  id_dsno_det: string;
  nm_vrdad: string;
  pdgree: string;
  orgen: string;
}
interface testigosMovilesI {
  id_dsno_det: string;
  nm_vrdad: string;
  pdgree: string;
  orgen: string;
}
interface distTratF {
  id_dsno_det: string;
  nm_vrdad: string;
  pdgree: string;
  orgen: string;
}
interface distTratI {
  id_dsno_det: string;
  nm_vrdad: string;
  pdgree: string;
  orgen: string;
}
interface TratamientosExperimentos {
  tratamientosF: tratamientosF[];
  tratamientosI: tratamientosI[];
  testigosFijosF: testigosFijosF[];
  testigosFijosI: testigosFijosI[];
  testigosMovilesF: testigosMovilesF[];
  testigosMovilesI: testigosMovilesI[];
  distTratF: distTratF[];
  distTratI: distTratI[];
  experimentoF: Experimento;
  experimentoI: Experimento;
}

interface arrayIds {
  id_crzmnto: string;
  plntlas_ttles: number;
}
interface DiseñosDetalles {
  nIdDiseno: string;
  nTipoParcela: string;
  cTestigo: string;
  nTotalPlantas: number;
  arrayIds: arrayIds[];
}

export type {
  Login,
  AnyObject,
  BasicType,
  User,
  Links,
  Alignments,
  AlignmentsList,
  FLowering,
  Varietys,
  HistoryVariety,
  Variety,
  Germoplasma,
  Crossings,
  TreeData,
  TreeItem,
  CrossingInitialData,
  ParametizeWeighted,
  ModifyWeighted,
  SuggestionCrossings,
  SuggestionCrossingsPerProject,
  Matrix,
  Distancias,
  CruzamientoSeleccionado,
  searchParameters,
  listAreas,
  listProyectos,
  Experimentos,
  Experimento,
  TratamientosTemporada,
  TratamientosExperimentos,
  DiseñosDetalles
};

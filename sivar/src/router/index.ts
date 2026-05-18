import { createRouter, createWebHashHistory } from "vue-router";
import {
  ROUTE_LOGIN,
  ROUTE_HOME,
  ROUTE_BIOINFORMATICA,
  ROUTE_MEJORAMIENTO,
  ROUTE_VARIEDADES,
  ROUTE_CRUZAMIENTOS,
  ROUTE_FLOWERING_LIST,
  ROUTE_VARIETYS_LIST,
  ROUTE_VARIETY_HISTORY,
  ROUTE_GERMOPLASM_BANK,
  ROUTE_CROSSING_LIST,
  ROUTE_SEQUENCE_SERVER,
  ROUTE_WIKI_CENICANA,
  ROUTE_JBROWSE,
  ROUTE_GALAXY,
  ROUTE_ALIGNMENTS,
  ROUTE_ALIGNMENTS_LIST,
  ROUTE_PARENTS_DIAGRAM_LEVEL,
  ROUTE_CROSSING_INITIAL_DATA,
  ROUTE_CROSSING_WEIGHTED,
  ROUTE_CROSSING_MATRIX,
  ROUTE_CROSSING_SUGGESTION,
  ROUTE_CROSSING_SUGGESTION_PER_PROJECT,
  ROUTE_EXPERIMENTS
} from "./routes";
import { useUserStore } from "@/stores/user";

//  Estructura de rutas Padre a hijo
const authGuard = (to: any, from: any, next: (arg0: string | undefined) => void) => {
  const UserStore = useUserStore();

  if (UserStore.isAuthenticated) {
    next(undefined);
  } else {
    next(ROUTE_LOGIN);
  }
};

const routes = [
  {
    path: ROUTE_LOGIN,
    name: "login.show",
    component: () => import("@/views/LoginView.vue")
  },
  {
    path: ROUTE_HOME,
    name: "main.show",
    component: () => import("@/views/MainView.vue"),
    beforeEnter: authGuard,
    children: [
      {
        path: ROUTE_BIOINFORMATICA,
        name: "bioinformatica.show",
        component: () => import("@/views/DashboardBioinformaticaView.vue"),
        beforeEnter: authGuard
      },
      {
        path: ROUTE_WIKI_CENICANA,
        name: "wiki_cenicana.show",
        component: () => import("@/views/bioinformatica/WikiCenicanaView.vue"),
        beforeEnter: authGuard
      },
      {
        path: ROUTE_JBROWSE,
        name: "jbrowse.show",
        component: () => import("@/views/bioinformatica/JBrowseView.vue"),
        beforeEnter: authGuard
      },
      {
        path: ROUTE_GALAXY,
        name: "galaxy.show",
        component: () => import("@/views/bioinformatica/GalaxyView.vue"),
        beforeEnter: authGuard
      },
      {
        path: ROUTE_SEQUENCE_SERVER,
        name: "sequence_server.show",
        component: () => import("@/views/bioinformatica/SecuenceServerView.vue"),
        beforeEnter: authGuard
      },
      {
        path: ROUTE_ALIGNMENTS_LIST,
        name: "alignments_list.show",
        component: () => import("@/views/bioinformatica/AlignmentsListView.vue"),
        beforeEnter: authGuard
      },
      {
        path: ROUTE_ALIGNMENTS,
        name: "alignments.show",
        component: () => import("@/views/bioinformatica/AlignmentsView.vue"),
        beforeEnter: authGuard
      },
      {
        path: ROUTE_MEJORAMIENTO,
        name: "mejoramiento.show",
        component: () => import("@/views/DashboardVariedadesView.vue"),
        beforeEnter: authGuard
      },
      {
        path: ROUTE_VARIEDADES,
        name: "variedades.show",
        component: () => import("@/views/mejoramiento/variedades/VariedadesView.vue"),
        beforeEnter: authGuard
      },
      {
        path: ROUTE_CRUZAMIENTOS,
        name: "cruzamientos.show",
        component: () => import("@/views/mejoramiento/cruzamientos/CruzamientosView.vue"),
        beforeEnter: authGuard
      },
      {
        path: ROUTE_FLOWERING_LIST,
        name: "flowering_list.show",
        component: () => import("@/views/mejoramiento/variedades/FloweringListView.vue"),
        beforeEnter: authGuard
      },
      {
        path: ROUTE_CROSSING_LIST,
        name: "crossing_list.show",
        component: () => import("@/views/mejoramiento/cruzamientos/CrossingListView.vue"),
        beforeEnter: authGuard
      },
      {
        path: ROUTE_CROSSING_INITIAL_DATA,
        name: "crossing_initial_data.show",
        component: () => import("@/views/mejoramiento/cruzamientos/CrossingInitialDataView.vue"),
        beforeEnter: authGuard,
        props: true
      },
      {
        path: ROUTE_CROSSING_WEIGHTED,
        name: "crossing_weighted.show",
        component: () => import("@/views/mejoramiento/cruzamientos/CrossingWeightedView.vue"),
        beforeEnter: authGuard,
        props: true
      },
      {
        path: ROUTE_CROSSING_MATRIX,
        name: "crossing_matrix.show",
        component: () => import("@/views/mejoramiento/cruzamientos/CrossingMatrixView.vue"),
        beforeEnter: authGuard,
        props: true
      },
      {
        path: ROUTE_CROSSING_SUGGESTION,
        name: "crossing_suggestion.show",
        component: () => import("@/views/mejoramiento/cruzamientos/CrossingSuggestionView.vue"),
        beforeEnter: authGuard,
        props: true
      },
      {
        path: ROUTE_CROSSING_SUGGESTION_PER_PROJECT,
        name: "crossing_suggestion_per_project.show",
        component: () => import("@/views/mejoramiento/cruzamientos/CrossingSuggestionPerProjectView.vue"),
        beforeEnter: authGuard,
        props: true
      },
      {
        path: ROUTE_VARIETYS_LIST,
        name: "variety_list.show",
        component: () => import("@/views/mejoramiento/variedades/VarietysListView.vue"),
        beforeEnter: authGuard
      },
      {
        path: ROUTE_VARIETY_HISTORY,
        name: "variety_history.show",
        component: () => import("@/views/mejoramiento/variedades/VarietyHistoryView.vue"),
        beforeEnter: authGuard
      },
      {
        path: ROUTE_PARENTS_DIAGRAM_LEVEL,
        name: "diagram_parents_level.show",
        component: () => import("@/views/mejoramiento/variedades/DiagramParentsLevelView.vue"),
        beforeEnter: authGuard
      },
      {
        path: ROUTE_GERMOPLASM_BANK,
        name: "germoplasm_bank.show",
        component: () => import("@/views/mejoramiento/variedades/GermoplasmBankListView.vue"),
        beforeEnter: authGuard
      },
      {
        path: ROUTE_EXPERIMENTS,
        name: "experiments.show",
        component: () => import("@/views/mejoramiento/experimentos/ParametersExperimentsView.vue"),
        beforeEnter: authGuard
      }
    ]
  },
  {
    path: "/:pathMatch(.*)*",
    component: () => import("../views/Error404View.vue")
  }
];
const router = createRouter({
  history: createWebHashHistory(),
  routes: routes
});

export default router;

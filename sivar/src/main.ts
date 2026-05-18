import { createApp } from "vue";
import { createPinia } from "pinia";
import Toast, { POSITION } from "vue-toastification";
import type { PluginOptions } from "vue-toastification";
import "vue-toastification/dist/index.css";
import piniaPluginPersistedstate from "pinia-plugin-persistedstate";
import "tailwindcss/tailwind.css";

import App from "./App.vue";
import router from "./router";

import "./assets/style.css";

const options: PluginOptions = {
  position: POSITION.TOP_LEFT
};

const pinia = createPinia();
pinia.use(piniaPluginPersistedstate);
const app = createApp(App);
app.use(pinia);
app.use(router);
app.use(Toast, options);

app.mount("#app");

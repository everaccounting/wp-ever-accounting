const { createApp } = window.Vue;
const { components } = eaccounting;
const { Link } = components;
import App from './App.vue'

const app = createApp(App);
app.use(components);
app.component(Link.name, Link);
app.mount("#app");

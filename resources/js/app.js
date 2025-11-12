import "./bootstrap";
import "../css/app.css";
import { createApp, h, onMounted } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import helpers from "./helpers";

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob("./Pages/**/*.vue", { eager: true });
        return pages[`./Pages/${name}.vue`];
    },
    setup({ el, App, props, plugin }) {
        createApp({
            render: () => h(App, props),
            provide: {
                helpers: helpers,
            },
            setup() {
                onMounted(() => {
                    document
                        .getElementById("app")
                        ?.removeAttribute("data-page");
                });
            },
        })
            .use(plugin)
            .mount(el);
    },
});

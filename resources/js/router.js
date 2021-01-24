import Vue from 'vue'
import VueRouter from 'vue-router';
import HomePage from "./pages/HomePage";


Vue.use(VueRouter);

const routes = [
    {path: '/home', component: HomePage, name: 'home', meta: {title: 'Home - ERP System'}},
];

const router = new VueRouter({
    mode: 'history',
    hash: false,
    routes: routes,
});

export default router;

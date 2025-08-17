import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import App from './App.vue';
import MenuPage from './components/MenuPage.vue';
import AdminLayout from './AdminLayout/AdminLayout.vue';
import AdminDashboard from './AdminPages/Dashboard.vue';
import TableManagement from './AdminPages/TableManagement.vue';
import Category from './AdminPages/Category.vue';
import Menu from './AdminPages/Menu.vue';
const routes = [
    { path: '/', component: MenuPage },
    { path: '/menu', component: MenuPage },
    { path: '/table/:table/menu', component: MenuPage },
    {
        path: '/admin',
        component: AdminLayout,
        children: [
            { path: '', component: AdminDashboard },
            { path: 'table-management', component: TableManagement },
            { path: 'menu', component: Menu },
            { path: 'category', component: Category }
        ]
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

const app = createApp(App);
app.use(router);

// Make toast available globally
app.config.globalProperties.$toast = toast;

app.mount('#app');

<template>
  <div class="admin-layout d-flex" style="min-height: 100vh;">
    <AdminSidebar />

    <div class="flex-grow-1 d-flex flex-column" style="margin-left: 250px;">
      <AdminHeader />

      <main class="flex-grow-1 p-4 bg-light" style="margin-top: 80px;">
        <router-view />
      </main>
    </div>
  </div>
</template>

<script>
import AdminSidebar from './Sidebar.vue'
import AdminHeader from './Navigation.vue'

export default {
  name: 'AdminLayout',
  components: {
    AdminSidebar,
    AdminHeader,
  },
}
</script>

<style scoped>
.admin-layout {
  background-color: #f8f9fa;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .admin-layout .flex-grow-1 {
    margin-left: 0 !important;
  }

  .admin-layout main {
    margin-top: 60px !important;
  }
}
</style>

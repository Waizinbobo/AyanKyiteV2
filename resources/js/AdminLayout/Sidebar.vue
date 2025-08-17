<template>
  <aside class="bg-dark text-white position-fixed" style="width: 250px; min-height: 100vh; height: 100vh; overflow-y: auto; top: 0; left: 0; z-index: 1030;">
    <div class="p-4">
      <h1 class="h3 fw-bold mb-4 text-warning">AyanKyite</h1>
      <nav>
        <ul class="nav flex-column">
          <li class="nav-item mb-2">
            <router-link to="/admin" class="nav-link text-white d-flex align-items-center" active-class="active">
              <i class="bi bi-house me-3"></i>
              Dashboard
            </router-link>
          </li>
          <li class="nav-item mb-2">
            <router-link to="/admin/menu" class="nav-link text-white d-flex align-items-center" active-class="active">
              <i class="bi bi-menu-button-wide me-3"></i>
              Menu
            </router-link>
          </li>
          <li class="nav-item mb-2">
            <router-link to="/admin/category" class="nav-link text-white d-flex align-items-center" active-class="active">
              <i class="bi bi-tags me-3"></i>
              Categories
            </router-link>
          </li>
          <li class="nav-item mb-2">
            <router-link to="/admin/user" class="nav-link text-white d-flex align-items-center" active-class="active">
              <i class="bi bi-people me-3"></i>
              Users
            </router-link>
          </li>
          <li class="nav-item mb-2">
            <router-link to="/admin/order" class="nav-link text-white d-flex align-items-center" active-class="active">
              <i class="bi bi-cart me-3"></i>
              Orders
            </router-link>
          </li>
          <li class="nav-item mb-2">
            <router-link to="/admin/message" class="nav-link text-white d-flex align-items-center" active-class="active">
              <i class="bi bi-envelope me-3"></i>
              Messages
            </router-link>
          </li>
          <li class="nav-item mb-2">
            <router-link to="/admin/table-management" class="nav-link text-white d-flex align-items-center" active-class="active">
              <i class="bi bi-table me-3"></i>
              Table Management
            </router-link>
          </li>
          <li class="nav-item mb-2">
            <router-link to="/admin/reservation-management" class="nav-link text-white d-flex align-items-center" active-class="active">
              <i class="bi bi-calendar-check me-3"></i>
              Reservations
            </router-link>
          </li>
          <li class="nav-item mb-2">
            <router-link to="/admin/blog" class="nav-link text-white d-flex align-items-center" active-class="active">
              <i class="bi bi-journal-text me-3"></i>
              Blog
            </router-link>
          </li>
        </ul>
      </nav>
      <!-- Logout Section -->
      <div class="mt-auto pt-4 border-top border-secondary">
        <button @click="handleLogout" class="btn btn-outline-warning w-100 d-flex align-items-center justify-content-center">
          <i class="bi bi-box-arrow-right me-2"></i>
          Logout
        </button>
      </div>
    </div>
  </aside>
</template>

<script>
import { useRouter } from 'vue-router'
import axios from 'axios'

export default {
  name: 'AdminSidebar',
  setup() {
    const router = useRouter()

    const handleLogout = async () => {
      try {
        await axios.post('/api/admin/logout')
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        // Clear local storage and redirect
        localStorage.removeItem('adminUser')
        router.push('/admin/login')
      }
    }

    return {
      handleLogout
    }
  }
}
</script>

<style scoped>
.nav-link {
  transition: all 0.3s ease;
  border-radius: 8px;
  padding: 12px 16px;
}
.nav-link:hover {
  background-color: rgba(255, 255, 255, 0.1);
  color: #ffc107 !important;
}
.nav-link.active {
  background-color: #ffc107;
  color: #000 !important;
}
.nav-link.active i {
  color: #000;
}
</style>

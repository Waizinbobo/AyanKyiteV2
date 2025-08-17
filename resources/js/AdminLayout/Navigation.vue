<template>
  <header class="bg-white shadow-sm border-bottom position-fixed w-100" style="top: 0; left: 250px; z-index: 1020; height: 80px;">
    <div class="d-flex justify-content-between align-items-center h-100 px-4">
      <div class="d-flex align-items-center">
        <h2 class="mb-0 text-dark">Admin Dashboard</h2>
      </div>

      <div class="d-flex align-items-center">
        <div class="dropdown me-3">
          <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
            <i class="bi bi-bell"></i>
            <span class="badge bg-danger ms-1">3</span>
          </button>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#">New order received</a></li>
            <li><a class="dropdown-item" href="#">Table reservation</a></li>
            <li><a class="dropdown-item" href="#">New message</a></li>
          </ul>
        </div>

        <div class="dropdown">
          <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
            <i class="bi bi-person-circle me-2"></i>
            Admin
          </button>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profile</a></li>
            <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Settings</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="#" @click="logout"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
          </ul>
        </div>
      </div>
    </div>
  </header>
</template>

<script>
export default {
  name: 'AdminHeader',
  methods: {
    logout() {
      // Handle logout
      console.log('Logout clicked');
    }
  }
}
</script>

<style scoped>
.input-group {
  max-width: 300px;
}

.btn-link {
  color: #6c757d;
}

.btn-link:hover {
  color: #495057;
}
</style>

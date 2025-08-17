<template>
  <div class="user-management">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h2 class="mb-1">
          <i class="bi bi-people-fill me-2 text-warning"></i>
          User Management
        </h2>
        <p class="text-muted mb-0">
          <i class="bi bi-gear me-1"></i>Manage all users in your store
        </p>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
      <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body d-flex align-items-center">
            <div class="bg-warning bg-opacity-10 rounded-circle p-3 me-3">
              <i class="bi bi-people text-warning fs-3"></i>
            </div>
            <div>
              <p class="text-muted small mb-1">Total Users</p>
              <h4 class="fw-bold mb-0">{{ stats.totalUsers }}</h4>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body d-flex align-items-center">
            <div class="bg-danger bg-opacity-10 rounded-circle p-3 me-3">
              <i class="bi bi-shield-check text-danger fs-3"></i>
            </div>
            <div>
              <p class="text-muted small mb-1">Admin</p>
              <h4 class="fw-bold mb-0">{{ stats.adminUsers }}</h4>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body d-flex align-items-center">
            <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3">
              <i class="bi bi-person-check text-success fs-3"></i>
            </div>
            <div>
              <p class="text-muted small mb-1">Active Users</p>
              <h4 class="fw-bold mb-0">{{ stats.activeUsers }}</h4>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body d-flex align-items-center">
            <div class="bg-info bg-opacity-10 rounded-circle p-3 me-3">
              <i class="bi bi-person-plus text-info fs-3"></i>
            </div>
            <div>
              <p class="text-muted small mb-1">New this month</p>
              <h4 class="fw-bold mb-0">{{ stats.newThisMonth }}</h4>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Users Table -->
    <div class="card shadow-sm">
      <div class="card-header bg-warning bg-opacity-10 d-flex justify-content-between align-items-center">
        <h3 class="mb-0">
          <i class="bi bi-person-lines-fill me-2 text-warning"></i>
          User Catalog
        </h3>
        <div class="d-flex gap-2">
          <div class="input-group" style="width: 300px;">
            <input
              v-model="searchQuery"
              type="text"
              class="form-control"
              placeholder="Search users..."
            />
            <button class="btn btn-outline-secondary">
              <i class="bi bi-search"></i>
            </button>
          </div>
          <select v-model="roleFilter" class="form-select" style="width: 150px;">
            <option value="">All Roles</option>
            <option value="admin">Admin</option>
            <option value="user">User</option>
          </select>
          <button @click="showAddModal = true" class="btn btn-warning text-white">
            <i class="bi bi-plus-circle me-2"></i>
            Add New User
          </button>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>User Info</th>
                <th>Role</th>
                <th>Email</th>
                <th>Address</th>
                <th>Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="user in filteredUsers" :key="user.id">
                <td>{{ user.id }}</td>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="bg-warning bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center fw-bold text-white me-3" style="width: 40px; height: 40px;">
                      {{ user.name.charAt(0).toUpperCase() }}
                    </div>
                    <div>
                      <p class="fw-semibold mb-0">{{ user.name }}</p>
                      <small class="text-muted">User ID: {{ user.id }}</small>
                    </div>
                  </div>
                </td>
                <td>
                  <span :class="user.role === 'admin' ? 'badge bg-danger' : 'badge bg-success'">
                    <i :class="user.role === 'admin' ? 'bi bi-shield-check me-1' : 'bi bi-person me-1'"></i>
                    {{ user.role === 'admin' ? 'Admin' : 'User' }}
                  </span>
                </td>
                <td>
                  <div>
                    <p class="mb-0">{{ user.email }}</p>
                    <small class="text-muted">{{ formatDate(user.created_at) }}</small>
                  </div>
                </td>
                <td>{{ user.address || 'N/A' }}</td>
                <td>
                  <div>
                    <p class="mb-0">{{ formatDate(user.created_at) }}</p>
                    <small class="text-muted">{{ formatTime(user.created_at) }}</small>
                  </div>
                </td>
                <td>
                  <div class="btn-group" role="group">
                    <button @click="editUser(user)" class="btn btn-sm btn-outline-success mx-1">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button @click="deleteUser(user.id)" class="btn btn-sm btn-outline-danger">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="filteredUsers.length === 0">
                <td colspan="7" class="text-center py-4">
                  <i class="bi bi-search text-muted fs-1"></i>
                  <p class="text-muted mt-2">No users found</p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Add/Edit User Modal -->
    <div class="modal fade" :class="{ show: showAddModal || showEditModal }" :style="{ display: showAddModal || showEditModal ? 'block' : 'none' }" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              <i class="bi bi-person-plus me-2"></i>
              {{ isEditing ? 'Edit User' : 'Add New User' }}
            </h5>
            <button @click="closeModal" type="button" class="btn-close"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveUser">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Full Name</label>
                  <input v-model="userForm.name" type="text" class="form-control" required />
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Email</label>
                  <input v-model="userForm.email" type="email" class="form-control" required />
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Phone</label>
                  <input v-model="userForm.phone" type="tel" class="form-control" />
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Role</label>
                  <select v-model="userForm.role" class="form-select" required>
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                  </select>
                </div>
                <div class="col-12 mb-3">
                  <label class="form-label">Address</label>
                  <textarea v-model="userForm.address" class="form-control" rows="3"></textarea>
                </div>
                <div class="col-md-6 mb-3" v-if="!isEditing">
                  <label class="form-label">Password</label>
                  <input v-model="userForm.password" type="password" class="form-control" :required="!isEditing" />
                </div>
                <div class="col-md-6 mb-3" v-if="!isEditing">
                  <label class="form-label">Confirm Password</label>
                  <input v-model="userForm.confirmPassword" type="password" class="form-control" :required="!isEditing" />
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button @click="closeModal" type="button" class="btn btn-secondary">Cancel</button>
            <button @click="saveUser" type="button" class="btn btn-warning text-white" :disabled="loading">
              <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
              {{ loading ? 'Saving...' : (isEditing ? 'Update User' : 'Create User') }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" :class="{ show: showDeleteModal }" :style="{ display: showDeleteModal ? 'block' : 'none' }" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title text-danger">
              <i class="bi bi-exclamation-triangle me-2"></i>
              Confirm Delete
            </h5>
            <button @click="showDeleteModal = false" type="button" class="btn-close"></button>
          </div>
          <div class="modal-body">
            <p>Are you sure you want to delete this user? This action cannot be undone.</p>
          </div>
          <div class="modal-footer">
            <button @click="showDeleteModal = false" type="button" class="btn btn-secondary">Cancel</button>
            <button @click="confirmDelete" type="button" class="btn btn-danger">
              <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
              {{ loading ? 'Deleting...' : 'Delete User' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Backdrop -->
    <div v-if="showAddModal || showEditModal || showDeleteModal" class="modal-backdrop fade show"></div>
  </div>
</template>

<script>
import { ref, reactive, computed, onMounted } from 'vue'
import axios from 'axios'
import { toast } from 'vue3-toastify'

export default {
  name: 'UserManagement',
  setup() {
    const users = ref([])
    const loading = ref(false)
    const searchQuery = ref('')
    const roleFilter = ref('')
    const showAddModal = ref(false)
    const showEditModal = ref(false)
    const showDeleteModal = ref(false)
    const isEditing = ref(false)
    const userToDelete = ref(null)

    const userForm = reactive({
      name: '',
      email: '',
      phone: '',
      address: '',
      role: 'user',
      password: '',
      confirmPassword: ''
    })

    const stats = computed(() => {
      const totalUsers = users.value.length
      const adminUsers = users.value.filter(u => u.role === 'admin').length
      const activeUsers = users.value.filter(u => u.status !== 'inactive').length
      const newThisMonth = users.value.filter(u => {
        const created = new Date(u.created_at)
        const now = new Date()
        return created.getMonth() === now.getMonth() && created.getFullYear() === now.getFullYear()
      }).length

      return {
        totalUsers,
        adminUsers,
        activeUsers,
        newThisMonth
      }
    })

    const filteredUsers = computed(() => {
      let filtered = users.value

      if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase()
        filtered = filtered.filter(user =>
          user.name.toLowerCase().includes(query) ||
          user.email.toLowerCase().includes(query) ||
          user.phone?.toLowerCase().includes(query)
        )
      }

      if (roleFilter.value) {
        filtered = filtered.filter(user => user.role === roleFilter.value)
      }

      return filtered
    })

    const fetchUsers = async () => {
      loading.value = true
      try {
        const response = await axios.get('/admin/users')
        if (response.data.success) {
          users.value = response.data.users || []
        } else {
          users.value = []
        }
      } catch (error) {
        console.error('Error fetching users:', error)
        users.value = []
      } finally {
        loading.value = false
      }
    }

    const saveUser = async () => {
      loading.value = true
      try {
        if (!isEditing.value && userForm.password !== userForm.confirmPassword) {
          toast.error('Passwords do not match!')
          return
        }

        if (isEditing.value) {
          // Update existing user
          const response = await axios.put(`/admin/users/${userForm.id}`, {
            name: userForm.name,
            email: userForm.email,
            phone: userForm.phone,
            address: userForm.address,
            role: userForm.role
          })

          if (response.data.success) {
            toast.success('User updated successfully!')
            await fetchUsers()
            closeModal()
          } else {
            toast.error('Error updating user: ' + response.data.message)
          }
        } else {
          // Create new user
          const response = await axios.post('/admin/users', {
            name: userForm.name,
            email: userForm.email,
            phone: userForm.phone,
            address: userForm.address,
            role: userForm.role,
            password: userForm.password
          })

          if (response.data.success) {
            toast.success('User created successfully!')
            await fetchUsers()
            closeModal()
          } else {
            toast.error('Error creating user: ' + response.data.message)
          }
        }
      } catch (error) {
        console.error('Error saving user:', error)
        if (error.response?.data?.message) {
          toast.error('Error: ' + error.response.data.message)
        } else {
          toast.error('Error saving user. Please try again.')
        }
      } finally {
        loading.value = false
      }
    }

    const editUser = (user) => {
      isEditing.value = true
      Object.assign(userForm, user)
      showEditModal.value = true
    }

    const deleteUser = (userId) => {
      userToDelete.value = userId
      showDeleteModal.value = true
    }

    const confirmDelete = async () => {
      loading.value = true
      try {
        const response = await axios.delete(`/admin/users/${userToDelete.value}`)
        if (response.data.success) {
          toast.success('User deleted successfully!')
          await fetchUsers()
        } else {
          toast.error('Error deleting user: ' + response.data.message)
        }
      } catch (error) {
        console.error('Error deleting user:', error)
        if (error.response?.data?.message) {
          toast.error('Error: ' + error.response.data.message)
        } else {
          toast.error('Error deleting user. Please try again.')
        }
      } finally {
        loading.value = false
        showDeleteModal.value = false
        userToDelete.value = null
      }
    }

    const closeModal = () => {
      showAddModal.value = false
      showEditModal.value = false
      isEditing.value = false
      Object.assign(userForm, {
        name: '',
        email: '',
        phone: '',
        address: '',
        role: 'user',
        password: '',
        confirmPassword: ''
      })
    }

    const formatDate = (dateString) => {
      if (!dateString) return 'N/A'
      const date = new Date(dateString)
      return date.toLocaleDateString()
    }

    const formatTime = (dateString) => {
      if (!dateString) return 'N/A'
      const date = new Date(dateString)
      return date.toLocaleTimeString()
    }

    onMounted(() => {
      fetchUsers()
    })

    return {
      users,
      loading,
      searchQuery,
      roleFilter,
      showAddModal,
      showEditModal,
      showDeleteModal,
      isEditing,
      userForm,
      stats,
      filteredUsers,
      saveUser,
      editUser,
      deleteUser,
      confirmDelete,
      closeModal,
      formatDate,
      formatTime
    }
  }
}
</script>

<style scoped>
.user-management {
  padding: 0px;
  overflow-x: hidden;
}
</style>

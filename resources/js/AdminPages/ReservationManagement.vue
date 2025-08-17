<template>
  <div class="reservation-management-page">
    <div class="container-fluid">
      <!-- Header -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="d-flex justify-content-between align-items-center">
            <h1 class="mb-0">Reservation Management</h1>
            <div>
              <button @click="refreshData" class="btn btn-outline-primary me-2">
                <i class="bi bi-arrow-clockwise me-2"></i>Refresh
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Statistics Cards -->
      <div class="row mb-4">
        <div class="col-md-2">
          <div class="card bg-primary text-white">
            <div class="card-body text-center">
              <h4>{{ stats.total || 0 }}</h4>
              <small>Total Reservations</small>
            </div>
          </div>
        </div>
        <div class="col-md-2">
          <div class="card bg-warning text-white">
            <div class="card-body text-center">
              <h4>{{ stats.pending || 0 }}</h4>
              <small>Pending</small>
            </div>
          </div>
        </div>
        <div class="col-md-2">
          <div class="card bg-success text-white">
            <div class="card-body text-center">
              <h4>{{ stats.confirmed || 0 }}</h4>
              <small>Confirmed</small>
            </div>
          </div>
        </div>
        <div class="col-md-2">
          <div class="card bg-danger text-white">
            <div class="card-body text-center">
              <h4>{{ stats.cancelled || 0 }}</h4>
              <small>Cancelled</small>
            </div>
          </div>
        </div>
        <div class="col-md-2">
          <div class="card bg-info text-white">
            <div class="card-body text-center">
              <h4>{{ stats.today || 0 }}</h4>
              <small>Today</small>
            </div>
          </div>
        </div>
        <div class="col-md-2">
          <div class="card bg-secondary text-white">
            <div class="card-body text-center">
              <h4>{{ stats.upcoming || 0 }}</h4>
              <small>Upcoming</small>
            </div>
          </div>
        </div>
      </div>

      <!-- Filters -->
      <div class="row mb-4">
        <div class="col-md-3">
          <select v-model="statusFilter" class="form-select">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="confirmed">Confirmed</option>
            <option value="cancelled">Cancelled</option>
            <option value="completed">Completed</option>
          </select>
        </div>
        <div class="col-md-3">
          <input
            v-model="dateFilter"
            type="date"
            class="form-control"
            placeholder="Filter by date"
          >
        </div>
        <div class="col-md-3">
          <input
            v-model="searchQuery"
            type="text"
            class="form-control"
            placeholder="Search by name, email, phone..."
          >
        </div>
        <div class="col-md-3">
          <select v-model="tableTypeFilter" class="form-select">
            <option value="">All Table Types</option>
            <option value="indoor">Indoor</option>
            <option value="outdoor">Outdoor</option>
            <option value="window">Window</option>
            <option value="bar">Bar</option>
            <option value="private">Private</option>
          </select>
        </div>
      </div>

      <!-- Reservations Table -->
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Customer</th>
                  <th>Contact</th>
                  <th>Date & Time</th>
                  <th>Guests</th>
                  <th>Table</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="reservation in filteredReservations" :key="reservation.id">
                  <td>#{{ reservation.id }}</td>
                  <td>
                    <strong>{{ reservation.name }}</strong>
                    <br>
                    <small class="text-muted">{{ reservation.email }}</small>
                  </td>
                  <td>{{ reservation.phone }}</td>
                  <td>
                    <strong>{{ formatDate(reservation.reservation_date) }}</strong>
                    <br>
                    <small class="text-muted">{{ formatTime(reservation.reservation_time) }}</small>
                  </td>
                  <td>
                    <span class="badge bg-info">{{ reservation.guests }} guests</span>
                  </td>
                  <td>
                    <span class="badge bg-secondary">{{ reservation.table?.name || 'N/A' }}</span>
                    <br>
                    <small class="text-muted">{{ reservation.table_type }}</small>
                  </td>
                  <td>
                    <span :class="`badge bg-${getStatusColor(reservation.status)}`">
                      {{ reservation.status }}
                    </span>
                  </td>
                  <td>
                    <div class="btn-group" role="group">
                      <button
                        @click="viewReservation(reservation)"
                        class="btn btn-sm btn-outline-primary mx-1"
                        title="View Details"
                      >
                        <i class="bi bi-eye"></i>
                      </button>
                      <button
                        v-if="reservation.status === 'pending'"
                        @click="confirmReservation(reservation.id)"
                        class="btn btn-sm btn-outline-success mx-1"
                        title="Confirm"
                      >
                        <i class="bi bi-check"></i>
                      </button>
                      <button
                        v-if="reservation.status === 'confirmed'"
                        @click="completeReservation(reservation.id)"
                        class="btn btn-sm btn-outline-info mx-1"
                        title="Mark Complete"
                      >
                        <i class="bi bi-check-circle"></i>
                      </button>
                      <button
                        @click="deleteReservation(reservation.id)"
                        class="btn btn-sm btn-outline-danger mx-1"
                        title="Delete"
                      >
                        <i class="bi bi-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Empty State -->
          <div v-if="filteredReservations.length === 0" class="text-center py-5">
            <i class="bi bi-calendar-x display-1 text-muted"></i>
            <h3 class="mt-3">No reservations found</h3>
            <p class="text-muted">Try adjusting your filters or check back later.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Reservation Details Modal -->
    <div class="modal fade" :class="{ show: showDetailsModal }" :style="{ display: showDetailsModal ? 'block' : 'none' }" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Reservation Details</h5>
            <button @click="closeDetailsModal" type="button" class="btn-close"></button>
          </div>
          <div class="modal-body" v-if="selectedReservation">
            <div class="row">
              <div class="col-md-6">
                <h6>Customer Information</h6>
                <p><strong>Name:</strong> {{ selectedReservation.name }}</p>
                <p><strong>Email:</strong> {{ selectedReservation.email }}</p>
                <p><strong>Phone:</strong> {{ selectedReservation.phone }}</p>
                <p><strong>User ID:</strong> {{ selectedReservation.user_id || 'Guest' }}</p>
              </div>
              <div class="col-md-6">
                <h6>Reservation Details</h6>
                <p><strong>Date:</strong> {{ formatDate(selectedReservation.reservation_date) }}</p>
                <p><strong>Time:</strong> {{ formatTime(selectedReservation.reservation_time) }}</p>
                <p><strong>Guests:</strong> {{ selectedReservation.guests }}</p>
                <p><strong>Table Type:</strong> {{ selectedReservation.table_type }}</p>
                <p><strong>Table:</strong> {{ selectedReservation.table?.name || 'N/A' }}</p>
                <p><strong>Status:</strong>
                  <span :class="`badge bg-${getStatusColor(selectedReservation.status)}`">
                    {{ selectedReservation.status }}
                  </span>
                </p>
              </div>
            </div>

            <div class="row mt-3">
              <div class="col-12">
                <h6>Special Requests</h6>
                <p v-if="selectedReservation.special_requests">{{ selectedReservation.special_requests }}</p>
                <p v-else class="text-muted">No special requests</p>
              </div>
            </div>

            <div class="row mt-3">
              <div class="col-12">
                <h6>Admin Notes</h6>
                <textarea
                  v-model="selectedReservation.admin_notes"
                  class="form-control"
                  rows="3"
                  placeholder="Add admin notes..."
                ></textarea>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button @click="closeDetailsModal" type="button" class="btn btn-secondary">Close</button>
            <button @click="updateReservation" type="button" class="btn btn-primary">Update</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Backdrop -->
    <div v-if="showDetailsModal" class="modal-backdrop fade show"></div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { showSuccess, showError } from '../utils/toast.js'
import axios from 'axios'

// Reactive data
const reservations = ref([])
const stats = ref({})
const loading = ref(false)
const showDetailsModal = ref(false)
const selectedReservation = ref(null)
const searchQuery = ref('')
const statusFilter = ref('')
const dateFilter = ref('')
const tableTypeFilter = ref('')

// Computed properties
const filteredReservations = computed(() => {
  let filtered = reservations.value

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(reservation =>
      reservation.name.toLowerCase().includes(query) ||
      reservation.email.toLowerCase().includes(query) ||
      reservation.phone.includes(query)
    )
  }

  if (statusFilter.value) {
    filtered = filtered.filter(reservation => reservation.status === statusFilter.value)
  }

  if (dateFilter.value) {
    filtered = filtered.filter(reservation => reservation.reservation_date === dateFilter.value)
  }

  if (tableTypeFilter.value) {
    filtered = filtered.filter(reservation => reservation.table_type === tableTypeFilter.value)
  }

  return filtered
})

// Methods
const loadReservations = async () => {
  try {
    loading.value = true
    const response = await axios.get('/admin/reservations')

    if (response.data.success) {
      reservations.value = response.data.data
    } else {
      showError(response.data.message)
    }
  } catch (error) {
    showError('Failed to load reservations')
    console.error('Error loading reservations:', error)
  } finally {
    loading.value = false
  }
}

const loadStats = async () => {
  try {
    const response = await axios.get('/admin/reservations/stats')

    if (response.data.success) {
      stats.value = response.data.data
    }
  } catch (error) {
    console.error('Error loading stats:', error)
  }
}

const refreshData = async () => {
  await loadReservations()
  await loadStats()
}

const viewReservation = (reservation) => {
  selectedReservation.value = { ...reservation }
  showDetailsModal.value = true
}

const closeDetailsModal = () => {
  showDetailsModal.value = false
  selectedReservation.value = null
}

const updateReservation = async () => {
  try {
    const response = await axios.put(`/admin/reservations/${selectedReservation.value.id}`, {
      admin_notes: selectedReservation.value.admin_notes
    })

    if (response.data.success) {
      showSuccess('Reservation updated successfully')
      closeDetailsModal()
      await loadReservations()
    } else {
      showError(response.data.message)
    }
  } catch (error) {
    showError('Failed to update reservation')
    console.error('Error updating reservation:', error)
  }
}

const confirmReservation = async (id) => {
  try {
    const response = await axios.patch(`/admin/reservations/${id}/confirm`)

    if (response.data.success) {
      showSuccess('Reservation confirmed successfully')
      await loadReservations()
      await loadStats()
    } else {
      showError(response.data.message)
    }
  } catch (error) {
    showError('Failed to confirm reservation')
    console.error('Error confirming reservation:', error)
  }
}

const completeReservation = async (id) => {
  try {
    const response = await axios.put(`/admin/reservations/${id}`, {
      status: 'completed'
    })

    if (response.data.success) {
      showSuccess('Reservation marked as completed')
      await loadReservations()
      await loadStats()
    } else {
      showError(response.data.message)
    }
  } catch (error) {
    showError('Failed to complete reservation')
    console.error('Error completing reservation:', error)
  }
}

const deleteReservation = async (id) => {
  if (!confirm('Are you sure you want to delete this reservation?')) return

  try {
    const response = await axios.delete(`/admin/reservations/${id}`)

    if (response.data.success) {
      showSuccess('Reservation deleted successfully')
      await loadReservations()
      await loadStats()
    } else {
      showError(response.data.message)
    }
  } catch (error) {
    showError('Failed to delete reservation')
    console.error('Error deleting reservation:', error)
  }
}

const getStatusColor = (status) => {
  const colors = {
    pending: 'warning',
    confirmed: 'success',
    cancelled: 'danger',
    completed: 'info'
  }
  return colors[status] || 'secondary'
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString()
}

const formatTime = (time) => {
  return new Date(`2000-01-01T${time}`).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
}

// Lifecycle
onMounted(async () => {
  await loadReservations()
  await loadStats()
})
</script>

<style scoped>
.reservation-management-page {
  background-color: #f8f9fa;
  min-height: 100vh;
  padding: 20px;
}

.card {
  border-radius: 10px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.modal.show {
  background-color: rgba(0, 0, 0, 0.5);
}

.badge {
  font-size: 0.8em;
}

.btn-sm {
  padding: 0.25rem 0.5rem;
  font-size: 0.875rem;
}

.table th {
  background-color: #f8f9fa;
  border-top: none;
}
</style>

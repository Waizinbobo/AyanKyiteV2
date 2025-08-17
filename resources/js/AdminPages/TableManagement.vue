<template>
  <div class="table-management-page">
    <div class="container-fluid">
      <!-- Header -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="d-flex justify-content-between align-items-center">
            <h1 class="mb-0">Table Management</h1>
            <button @click="showAddModal = true" class="btn btn-primary">
              <i class="bi bi-plus-circle me-2"></i>Add New Table
            </button>
          </div>
        </div>
      </div>

      <!-- Statistics Cards -->
      <div class="row mb-4">
        <div class="col-md-2">
          <div class="card bg-primary text-white">
            <div class="card-body text-center">
              <h4>{{ stats.total || 0 }}</h4>
              <small>Total Tables</small>
            </div>
          </div>
        </div>
        <div class="col-md-2">
          <div class="card bg-success text-white">
            <div class="card-body text-center">
              <h4>{{ stats.available || 0 }}</h4>
              <small>Available</small>
            </div>
          </div>
        </div>
        <div class="col-md-2">
          <div class="card bg-danger text-white">
            <div class="card-body text-center">
              <h4>{{ stats.occupied || 0 }}</h4>
              <small>Occupied</small>
            </div>
          </div>
        </div>
        <div class="col-md-2">
          <div class="card bg-warning text-white">
            <div class="card-body text-center">
              <h4>{{ stats.reserved || 0 }}</h4>
              <small>Reserved</small>
            </div>
          </div>
        </div>
        <div class="col-md-2">
          <div class="card bg-secondary text-white">
            <div class="card-body text-center">
              <h4>{{ stats.maintenance || 0 }}</h4>
              <small>Maintenance</small>
            </div>
          </div>
        </div>
        <div class="col-md-2">
          <div class="card bg-info text-white">
            <div class="card-body text-center">
              <h4>{{ stats.active || 0 }}</h4>
              <small>Active Tables</small>
            </div>
          </div>
        </div>
      </div>

      <!-- Filters and Search -->
      <div class="row mb-4">
        <div class="col-md-6">
          <div class="input-group">
            <input
              v-model="searchQuery"
              type="text"
              class="form-control"
              placeholder="Search tables..."
            >
            <button class="btn btn-outline-secondary" type="button">
              <i class="bi bi-search"></i>
            </button>
          </div>
        </div>
        <div class="col-md-3">
          <select v-model="statusFilter" class="form-select">
            <option value="">All Status</option>
            <option value="available">Available</option>
            <option value="occupied">Occupied</option>
            <option value="reserved">Reserved</option>
            <option value="maintenance">Maintenance</option>
          </select>
        </div>
        <div class="col-md-3">
          <select v-model="locationFilter" class="form-select">
            <option value="">All Locations</option>
            <option value="Indoor">Indoor</option>
            <option value="Outdoor">Outdoor</option>
            <option value="Window">Window</option>
            <option value="Bar">Bar</option>
            <option value="Private">Private</option>
          </select>
        </div>
      </div>

      <!-- Tables Grid -->
      <div class="row">
        <div
          v-for="table in filteredTables"
          :key="table.id"
          class="col-md-3 col-lg-2 mb-3"
        >
          <div class="card table-card" :class="getTableCardClass(table.status)">
            <div class="card-body text-center">
              <h5 class="card-title">{{ table.name }}</h5>
              <p class="card-text">
                <i class="bi bi-people me-1"></i>{{ table.seats }} seats
              </p>
              <p class="card-text">
                <i class="bi bi-geo-alt me-1"></i>{{ table.location }}
              </p>
              <span :class="`badge bg-${getStatusColor(table.status)}`">
                {{ table.status }}
              </span>

              <div class="mt-3">
                <button
                  @click="editTable(table)"
                  class="btn btn-sm btn-outline-primary me-1"
                  title="Edit Table"
                >
                  <i class="bi bi-pencil"></i>
                </button>
                <button
                  @click="showQRModalFunction(table)"
                  class="btn btn-sm btn-outline-success me-1"
                  title="View QR Code"
                >
                  <i class="bi bi-qr-code"></i>
                </button>
                <button
                  @click="showStatusModal(table)"
                  class="btn btn-sm btn-outline-warning me-1"
                  title="Change Status"
                >
                  <i class="bi bi-arrow-repeat"></i>
                </button>
                <button
                  @click="viewReservations(table)"
                  class="btn btn-sm btn-outline-info me-1"
                  title="View Reservations"
                >
                  <i class="bi bi-calendar"></i>
                </button>
                <button
                  @click="deleteTable(table.id)"
                  class="btn btn-sm btn-outline-danger"
                  title="Delete Table"
                >
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="filteredTables.length === 0" class="row">
        <div class="col-12 text-center">
          <div class="empty-state">
            <i class="bi bi-table display-1 text-muted"></i>
            <h3 class="mt-3">No tables found</h3>
            <p class="text-muted">Try adjusting your filters or add a new table.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Add/Edit Table Modal -->
    <div class="modal fade" :class="{ show: showAddModal || showEditModal }" :style="{ display: (showAddModal || showEditModal) ? 'block' : 'none' }" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ isEditing ? 'Edit Table' : 'Add New Table' }}</h5>
            <button @click="closeModal" type="button" class="btn-close"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveTable">
              <div class="mb-3">
                <label class="form-label">Table Name</label>
                <input
                  v-model="form.name"
                  type="text"
                  class="form-control"
                  required
                  placeholder="e.g., Table 1, VIP Table"
                >
              </div>
              <div class="mb-3">
                <label class="form-label">Table Number</label>
                <input
                  v-model.number="form.number"
                  type="number"
                  class="form-control"
                  required
                  min="1"
                  placeholder="1"
                >
              </div>
              <div class="mb-3">
                <label class="form-label">Number of Seats</label>
                <input
                  v-model.number="form.seats"
                  type="number"
                  class="form-control"
                  required
                  min="1"
                  max="20"
                  placeholder="4"
                >
              </div>
              <div class="mb-3">
                <label class="form-label">Location</label>
                <select v-model="form.location" class="form-select" required>
                  <option value="">Select Location</option>
                  <option value="Indoor">Indoor</option>
                  <option value="Outdoor">Outdoor</option>
                  <option value="Window">Window</option>
                  <option value="Bar">Bar</option>
                  <option value="Private">Private</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Status</label>
                <select v-model="form.status" class="form-select" required>
                  <option value="available">Available</option>
                  <option value="occupied">Occupied</option>
                  <option value="reserved">Reserved</option>
                  <option value="maintenance">Maintenance</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Description (Optional)</label>
                <textarea
                  v-model="form.description"
                  class="form-control"
                  rows="3"
                  placeholder="Any additional notes about this table..."
                ></textarea>
              </div>
              <div class="mb-3">
                <div class="form-check">
                  <input
                    v-model="form.is_active"
                    type="checkbox"
                    class="form-check-input"
                    id="isActive"
                  >
                  <label class="form-check-label" for="isActive">
                    Active Table
                  </label>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button @click="closeModal" type="button" class="btn btn-secondary">Cancel</button>
            <button @click="saveTable" type="button" class="btn btn-primary">
              {{ isEditing ? 'Update' : 'Create' }} Table
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Status Change Modal -->
    <div class="modal fade" :class="{ show: showStatusModalFlag }" :style="{ display: showStatusModalFlag ? 'block' : 'none' }" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Change Table Status</h5>
            <button @click="closeStatusModal" type="button" class="btn-close"></button>
          </div>
          <div class="modal-body">
            <p>Change status for <strong>{{ selectedTable?.name }}</strong></p>
            <select v-model="newStatus" class="form-select">
              <option value="available">Available</option>
              <option value="occupied">Occupied</option>
              <option value="reserved">Reserved</option>
              <option value="maintenance">Maintenance</option>
            </select>
          </div>
          <div class="modal-footer">
            <button @click="closeStatusModal" type="button" class="btn btn-secondary">Cancel</button>
            <button @click="updateTableStatus" type="button" class="btn btn-primary">Update Status</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Reservations Modal -->
    <div class="modal fade" :class="{ show: showReservationsModal }" :style="{ display: showReservationsModal ? 'block' : 'none' }" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Reservations for {{ selectedTable?.name }}</h5>
            <button @click="closeReservationsModal" type="button" class="btn-close"></button>
          </div>
          <div class="modal-body">
            <div v-if="tableReservations.length === 0" class="text-center py-4">
              <i class="bi bi-calendar-x fs-1 text-muted mb-3"></i>
              <h5 class="text-muted">No reservations for this table</h5>
            </div>
            <div v-else>
              <div v-for="reservation in tableReservations" :key="reservation.id" class="border-bottom pb-3 mb-3">
                <div class="d-flex justify-content-between align-items-start">
                  <div>
                    <h6 class="mb-1">{{ reservation.name }}</h6>
                    <p class="text-muted mb-1">
                      <i class="bi bi-calendar me-1"></i>
                      {{ formatDate(reservation.reservation_date) }} at {{ formatTime(reservation.reservation_time) }}
                    </p>
                    <p class="text-muted mb-1">
                      <i class="bi bi-people me-1"></i>
                      {{ reservation.guests }} guests
                    </p>
                    <p class="text-muted mb-1">
                      <i class="bi bi-telephone me-1"></i>
                      {{ reservation.phone }}
                    </p>
                    <span :class="`badge bg-${getReservationStatusColor(reservation.status)}`">
                      {{ reservation.status }}
                    </span>
                  </div>
                  <div class="btn-group" role="group">
                    <button
                      v-if="reservation.status === 'pending'"
                      @click="confirmReservation(reservation.id)"
                      class="btn btn-sm btn-outline-success"
                      title="Confirm"
                    >
                      <i class="bi bi-check"></i>
                    </button>
                    <button
                      @click="deleteReservation(reservation.id)"
                      class="btn btn-sm btn-outline-danger"
                      title="Delete"
                    >
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button @click="closeReservationsModal" type="button" class="btn btn-secondary">Close</button>
          </div>
        </div>
      </div>
    </div>

    <!-- QR Code Modal -->
    <div class="modal fade" :class="{ show: showQRModal }" :style="{ display: showQRModal ? 'block' : 'none' }" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">QR Code for {{ selectedTable?.name }}</h5>
            <button @click="closeQRModal" type="button" class="btn-close"></button>
          </div>
          <div class="modal-body text-center">
            <div v-if="qrCodeUrl" class="mb-3">
              <img :src="qrCodeUrl" alt="QR Code" class="img-fluid" style="max-width: 300px;" @error="onQrImgError" @load="console.log('QR image loaded successfully')">
              <p class="mt-3 text-muted">Scan this QR code to access the menu for {{ selectedTable?.name }}</p>
              <p class="text-muted small">URL: {{ currentMenuUrl }}</p>
              <p class="text-muted small">Debug: showQRModal = {{ showQRModal }}, qrCodeUrl = {{ qrCodeUrl ? 'Set' : 'Not set' }}</p>
            </div>
            <div v-else class="text-center py-4">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
              <p class="mt-2">Generating QR code...</p>
              <p class="text-muted small">Debug: showQRModal = {{ showQRModal }}, qrCodeUrl = {{ qrCodeUrl ? 'Set' : 'Not set' }}</p>
            </div>
          </div>
          <div class="modal-footer">
            <button @click="downloadQR" class="btn btn-primary me-2" :disabled="!qrCodeUrl">
              <i class="bi bi-download me-1"></i>Download QR
            </button>
            <button @click="closeQRModal" type="button" class="btn btn-secondary">Close</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Backdrop -->
    <div v-if="showAddModal || showEditModal || showStatusModalFlag || showReservationsModal || showQRModal" class="modal-backdrop fade show"></div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { toast } from 'vue3-toastify'

// Helper functions for notifications
const showSuccess = (message) => {
  // Using toastify for notifications
  toast.success(message)
}

const showError = (message) => {
  // Using toastify for notifications
  toast.error(message)
}

// Reactive data
const tables = ref([])
const stats = ref({})
const loading = ref(false)
const showAddModal = ref(false)
const showEditModal = ref(false)
const showStatusModalFlag = ref(false)
const showReservationsModal = ref(false)
const showQRModal = ref(false) // New reactive data for QR code modal
const currentMenuUrl = ref('')
const qrTriedFallback = ref(false)
const searchQuery = ref('')
const statusFilter = ref('')
const locationFilter = ref('')
const selectedTable = ref(null)
const newStatus = ref('')
const tableReservations = ref([])
const qrCodeUrl = ref(null) // New reactive data for QR code URL

// Form data
const form = ref({
  name: '',
  number: '',
  seats: '',
  location: '',
  status: 'available',
  description: '',
  is_active: true
})

// Computed properties
const totalTables = computed(() => tables.value.length)



const filteredTables = computed(() => {
  let filtered = tables.value

  if (searchQuery.value) {
    filtered = filtered.filter(table =>
      table.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      table.number.toString().includes(searchQuery.value)
    )
  }

  if (statusFilter.value) {
    filtered = filtered.filter(table => table.status === statusFilter.value)
  }

  if (locationFilter.value) {
    filtered = filtered.filter(table => table.location === locationFilter.value)
  }

  return filtered
})

const isEditing = computed(() => showEditModal.value)

// Methods
const loadTables = async () => {
  try {
    loading.value = true
    console.log('Loading tables...')
    const response = await axios.get('/admin/tables')
    console.log('Tables response:', response.data)

    if (response.data.success) {
      tables.value = response.data.data
      console.log('Tables loaded:', tables.value)
    } else {
      showError(response.data.message)
    }
  } catch (error) {
    console.error('Error loading tables:', error)
    showError('Failed to load tables: ' + error.message)
  } finally {
    loading.value = false
  }
}

const loadStats = async () => {
  try {
    const response = await axios.get('/admin/tables/stats')

    if (response.data.success) {
      stats.value = response.data.data
    } else {
      console.error('Failed to load stats:', response.data.message)
    }
  } catch (error) {
    console.error('Error loading stats:', error)
    // Set default values if stats fail to load
    stats.value = {
      total: 0,
      active: 0,
      available: 0,
      occupied: 0,
      reserved: 0,
      maintenance: 0
    }
  }
}



const getStatusColor = (status) => {
  const colors = {
    available: 'success',
    occupied: 'danger',
    reserved: 'warning',
    maintenance: 'secondary'
  }
  return colors[status] || 'info'
}

const getTableCardClass = (status) => {
  const classes = {
    available: 'border-success',
    occupied: 'border-danger',
    reserved: 'border-warning',
    maintenance: 'border-secondary'
  }
  return classes[status] || 'border-info'
}

const showQRModalFunction = (table) => {
  console.log('showQRModalFunction called with table:', table)
  selectedTable.value = table
  qrCodeUrl.value = null // Clear previous QR code
  qrTriedFallback.value = false

  // Generate QR code URL using Google Charts API first
  currentMenuUrl.value = `${window.location.origin}/menu?table=${table.number}`
  qrCodeUrl.value = `https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=${encodeURIComponent(currentMenuUrl.value)}`

  console.log('Generated QR URL (primary):', qrCodeUrl.value)
  console.log('Menu URL:', currentMenuUrl.value)

  showQRModal.value = true
  console.log('QR modal should be visible now')
}

const closeQRModal = () => {
  console.log('closeQRModal called')
  showQRModal.value = false
  selectedTable.value = null
  qrCodeUrl.value = null
  console.log('QR modal closed')
}

const generateQRCode = async (tableId) => {
  try {
    // Find the table to get the table number
    const table = tables.value.find(t => t.id === tableId)
    if (!table) {
      showError('Table not found')
      return
    }

    // Generate QR code URL using Google Charts API
    currentMenuUrl.value = `${window.location.origin}/menu?table=${table.number}`
    qrCodeUrl.value = `https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=${encodeURIComponent(currentMenuUrl.value)}`

    showSuccess('QR code generated successfully!')
  } catch (error) {
    showError('Failed to generate QR code: ' + error.message)
    console.error('Error generating QR code:', error)
  }
}

const saveTable = async () => {
  try {
    console.log('Saving table...', form.value)
    const url = isEditing.value ? `/admin/tables/${selectedTable.value.id}` : '/admin/tables'
    console.log('URL:', url)

    let response
    if (isEditing.value) {
      response = await axios.put(url, form.value)
    } else {
      response = await axios.post(url, form.value)
    }
    console.log('Save response:', response.data)

    if (response.data.success) {
      showSuccess(response.data.message)
      closeModal()
      await loadTables()
      await loadStats()
    } else {
      if (response.data.errors) {
        // Show validation errors
        const errorMessages = Object.values(response.data.errors).flat().join(', ')
        showError('Validation failed: ' + errorMessages)
      } else {
        showError(response.data.message || 'Failed to save table')
      }
    }
  } catch (error) {
    console.error('Error saving table:', error)
    showError('Failed to save table: ' + error.message)
  }
}

const editTable = (table) => {
  console.log('editTable called with:', table)
  selectedTable.value = table
  form.value = { ...table }
  showEditModal.value = true
}

const deleteTable = async (id) => {
  console.log('deleteTable called with id:', id)
  if (!confirm('Are you sure you want to delete this table?')) return

  try {
    const response = await axios.delete(`/admin/tables/${id}`)

    if (response.data.success) {
      showSuccess(response.data.message)
      await loadTables()
      await loadStats()
    } else {
      showError(response.data.message || 'Failed to delete table')
    }
  } catch (error) {
    showError('Failed to delete table: ' + error.message)
    console.error('Error deleting table:', error)
  }
}

const showStatusModal = (table) => {
  selectedTable.value = table
  newStatus.value = table.status
  showStatusModalFlag.value = true
}

const updateTableStatus = async () => {
  try {
    const response = await axios.patch(`/admin/tables/${selectedTable.value.id}/status`, {
      status: newStatus.value
    })

    if (response.data.success) {
      showSuccess(response.data.message)
      closeStatusModal()
      await loadTables()
      await loadStats()
    } else {
      showError(response.data.message || 'Failed to update table status')
    }
  } catch (error) {
    showError('Failed to update table status: ' + error.message)
    console.error('Error updating table status:', error)
  }
}

const closeModal = () => {
  showAddModal.value = false
  showEditModal.value = false
  selectedTable.value = null
  resetForm()
}

const closeStatusModal = () => {
  showStatusModalFlag.value = false
  selectedTable.value = null
  newStatus.value = ''
}

const viewReservations = async (table) => {
  selectedTable.value = table
  try {
    const response = await axios.get(`/admin/reservations?table_id=${table.id}`)
    if (response.data.success) {
      tableReservations.value = response.data.data
    }
  } catch (error) {
    console.error('Error loading reservations:', error)
    tableReservations.value = []
  }
  showReservationsModal.value = true
}

const closeReservationsModal = () => {
  showReservationsModal.value = false
  selectedTable.value = null
  tableReservations.value = []
}

const confirmReservation = async (reservationId) => {
  try {
    const response = await axios.patch(`/admin/reservations/${reservationId}/confirm`)
    if (response.data.success) {
      showSuccess('Reservation confirmed successfully')
      await viewReservations(selectedTable.value)
      await loadTables()
      await loadStats()
    }
  } catch (error) {
    showError('Failed to confirm reservation')
  }
}

const deleteReservation = async (reservationId) => {
  if (!confirm('Are you sure you want to delete this reservation?')) return

  try {
    const response = await axios.delete(`/admin/reservations/${reservationId}`)
    if (response.data.success) {
      showSuccess('Reservation deleted successfully')
      await viewReservations(selectedTable.value)
      await loadTables()
      await loadStats()
    }
  } catch (error) {
    showError('Failed to delete reservation')
  }
}

const getReservationStatusColor = (status) => {
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

const resetForm = () => {
  form.value = {
    name: '',
    number: '',
    seats: '',
    location: '',
    status: 'available',
    description: '',
    is_active: true
  }
}

const downloadQR = () => {
  if (qrCodeUrl.value) {
    window.open(qrCodeUrl.value, '_blank')
  }
}

// On QR image error, fallback to another provider once
const onQrImgError = () => {
  console.log('QR image failed to load, attempting fallback...')
  if (!qrTriedFallback.value && currentMenuUrl.value) {
    qrTriedFallback.value = true
    qrCodeUrl.value = `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(currentMenuUrl.value)}`
    console.log('Fallback QR URL:', qrCodeUrl.value)
  }
}

// Test function to debug button clicks
const testButton = (action, table) => {
  console.log('Button clicked:', action, table)

  if (action === 'qr') {
    // Call the actual QR function
    showQRModalFunction(table)
  } else {
    toast.info(`Button clicked: ${action} for table ${table.name}`)
  }
}

// Lifecycle
onMounted(async () => {
  console.log('TableManagement component mounting...')
  await loadTables()
  await loadStats()
  console.log('TableManagement component mounted!')
  console.log('Available functions:', { editTable, deleteTable, showQRModalFunction, showStatusModal, viewReservations })
})
</script>

<style scoped>
.table-management-page {
  background-color: #f8f9fa;
  min-height: 100vh;
  padding: 20px;
}

.table-card {
  transition: transform 0.2s, box-shadow 0.2s;
  cursor: pointer;
}

.table-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.empty-state {
  padding: 60px 20px;
}

.modal.show {
  background-color: rgba(0, 0, 0, 0.5);
}

.card {
  border-radius: 10px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.badge {
  font-size: 0.8em;
}

.btn-sm {
  padding: 0.25rem 0.5rem;
  font-size: 0.875rem;
}
</style>

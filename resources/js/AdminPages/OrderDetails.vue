<template>
  <div class="order-details-page">
    <div class="container-fluid">
      <!-- Header -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
              <button @click="goBack" class="btn btn-outline-secondary me-3">
                <i class="bi bi-arrow-left me-2"></i>Back to Orders
              </button>
              <div>
                <h1 class="mb-1">
                  <i class="bi bi-receipt text-warning me-2"></i>
                  Order Details
                </h1>
                <p class="text-muted mb-0">View and manage order information</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-warning" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-3 text-muted">Loading order details...</p>
      </div>

      <!-- Order Details -->
      <div v-else-if="order">
        <!-- Order Header Card -->
        <div class="card shadow-sm mb-4">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-md-8">
                <div class="d-flex align-items-center">
                  <div class="bg-warning bg-opacity-10 rounded-circle p-3 me-3">
                    <i class="bi bi-receipt text-warning fs-2"></i>
                  </div>
                  <div>
                    <h2 class="mb-1">Order #{{ order.order_id }}</h2>
                    <p class="text-muted mb-0">
                      <i class="bi bi-calendar me-1"></i>
                      {{ formatDate(order.date) }} at {{ formatTime(order.date) }}
                    </p>
                  </div>
                </div>
              </div>
              <div class="col-md-4 text-md-end">
                <span :class="getStatusBadgeClass(order.status)" class="badge fs-6 px-3 py-2">
                  <i :class="getStatusIcon(order.status)" class="me-1"></i>
                  {{ getStatusText(order.status) }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <!-- Customer Information -->
          <div class="col-lg-4 mb-4">
            <div class="card shadow-sm h-100">
              <div class="card-header bg-light">
                <h5 class="mb-0">
                  <i class="bi bi-person-circle text-primary me-2"></i>
                  Customer Information
                </h5>
              </div>
              <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                  <div class="bg-warning bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center fw-bold text-white me-3" style="width: 50px; height: 50px;">
                    {{ order.user?.name?.charAt(0).toUpperCase() || 'G' }}
                  </div>
                  <div>
                    <h6 class="mb-0">{{ order.user?.name || 'Guest' }}</h6>
                    <small class="text-muted">{{ order.user?.email || 'No email' }}</small>
                  </div>
                </div>

                <div class="mb-2">
                  <small class="text-muted">Phone</small>
                  <p class="mb-0">{{ order.user?.phone || 'No phone' }}</p>
                </div>

                <div class="mb-2" v-if="order.user?.address">
                  <small class="text-muted">Address</small>
                  <p class="mb-0">{{ order.user.address }}</p>
                </div>

                <div class="mb-2" v-if="order.notes">
                  <small class="text-muted">Notes</small>
                  <div class="bg-light rounded p-2 mt-1">
                    <p class="mb-0 small">{{ order.notes }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Order Summary -->
          <div class="col-lg-8 mb-4">
            <div class="card shadow-sm h-100">
              <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                  <i class="bi bi-calculator text-success me-2"></i>
                  Order Summary
                </h5>
                <button @click="showStatusModal = true" class="btn btn-outline-primary btn-sm">
                  <i class="bi bi-gear me-1"></i>
                  Update Status
                </button>
              </div>
              <div class="card-body">
                <div class="row text-center">
                  <div class="col-md-4 mb-3">
                    <div class="bg-success bg-opacity-10 rounded p-3">
                      <h4 class="text-success mb-1">{{ formatPrice(order.total) }} MMK</h4>
                      <small class="text-muted">Total Amount</small>
                    </div>
                  </div>
                  <div class="col-md-4 mb-3">
                    <div class="bg-primary bg-opacity-10 rounded p-3">
                      <h4 class="text-primary mb-1">{{ order.order_details?.length || 0 }}</h4>
                      <small class="text-muted">Items Count</small>
                    </div>
                  </div>
                  <div class="col-md-4 mb-3">
                    <div class="bg-warning bg-opacity-10 rounded p-3">
                      <span :class="getStatusBadgeClass(order.status)" class="badge fs-6">
                        <i :class="getStatusIcon(order.status)" class="me-1"></i>
                        {{ getStatusText(order.status) }}
                      </span>
                      <br>
                      <small class="text-muted">Order Status</small>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Order Items -->
        <div class="card shadow-sm">
          <div class="card-header bg-light">
            <h5 class="mb-0">
              <i class="bi bi-list-ul text-warning me-2"></i>
              Order Items
            </h5>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover">
                <thead class="table-light">
                  <tr>
                    <th>#</th>
                    <th>Product Name</th>
                    <th>Price (MMK)</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-if="!order.order_details || order.order_details.length === 0">
                    <td colspan="5" class="text-center py-4">
                      <i class="bi bi-inbox text-muted fs-1"></i>
                      <p class="text-muted mt-2">No items found</p>
                    </td>
                  </tr>
                  <tr v-else v-for="(item, index) in order.order_details" :key="item.orderDetail_id">
                    <td>{{ index + 1 }}</td>
                    <td>
                      <div class="fw-semibold">{{ item.menu?.name || 'Unknown Item' }}</div>
                      <small v-if="item.notes" class="text-muted">{{ item.notes }}</small>
                    </td>
                    <td>{{ formatPrice(item.price) }}</td>
                    <td>
                      <span class="badge bg-info">{{ item.quantity }}</span>
                    </td>
                    <td class="fw-semibold">{{ formatPrice(item.subtotal) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Status Update Modal -->
    <div class="modal fade" :class="{ show: showStatusModal }" :style="{ display: showStatusModal ? 'block' : 'none' }" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              <i class="bi bi-gear me-2"></i>
              Update Order Status
            </h5>
            <button @click="showStatusModal = false" type="button" class="btn-close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Current Status</label>
              <div>
                <span :class="getStatusBadgeClass(order?.status)" class="badge fs-6">
                  <i :class="getStatusIcon(order?.status)" class="me-1"></i>
                  {{ getStatusText(order?.status) }}
                </span>
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label">Select New Status</label>
              <div class="d-grid gap-2">
                <label class="btn btn-outline-warning text-start">
                  <input type="radio" v-model="selectedStatus" value="pending" class="me-2">
                  <i class="bi bi-clock me-2"></i>Pending
                </label>
                <label class="btn btn-outline-primary text-start">
                  <input type="radio" v-model="selectedStatus" value="confirmed" class="me-2">
                  <i class="bi bi-check-circle me-2"></i>Confirmed
                </label>
                <label class="btn btn-outline-info text-start">
                  <input type="radio" v-model="selectedStatus" value="shipped" class="me-2">
                  <i class="bi bi-truck me-2"></i>Shipped
                </label>
                <label class="btn btn-outline-success text-start">
                  <input type="radio" v-model="selectedStatus" value="delivered" class="me-2">
                  <i class="bi bi-check-circle-fill me-2"></i>Delivered
                </label>
                <label class="btn btn-outline-danger text-start">
                  <input type="radio" v-model="selectedStatus" value="cancelled" class="me-2">
                  <i class="bi bi-x-circle me-2"></i>Cancelled
                </label>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button @click="showStatusModal = false" type="button" class="btn btn-secondary">Cancel</button>
            <button @click="updateOrderStatus" type="button" class="btn btn-primary" :disabled="!selectedStatus || updatingStatus">
              <span v-if="updatingStatus" class="spinner-border spinner-border-sm me-2"></span>
              Update Status
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Backdrop -->
    <div v-if="showStatusModal" class="modal-backdrop fade show"></div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import { toast } from 'vue3-toastify'

export default {
  name: 'OrderDetails',
  setup() {
    const route = useRoute()
    const router = useRouter()
    const order = ref(null)
    const loading = ref(false)
    const showStatusModal = ref(false)
    const selectedStatus = ref('')
    const updatingStatus = ref(false)

    const fetchOrderDetails = async () => {
      const orderId = route.query.order_id
      if (!orderId) {
        toast.error('Order ID not provided')
        return
      }

      loading.value = true
      try {
        const response = await axios.get(`/api/orders/${orderId}`)

        if (response.data.success) {
          order.value = response.data.order
        } else {
          toast.error('Error fetching order details: ' + response.data.message)
        }
      } catch (error) {
        console.error('Error fetching order details:', error)
        toast.error('Error fetching order details')
      } finally {
        loading.value = false
      }
    }

    const formatPrice = (price) => {
      return parseFloat(price).toLocaleString()
    }

    const formatDate = (dateString) => {
      const date = new Date(dateString)
      return date.toLocaleDateString('en-GB')
    }

    const formatTime = (dateString) => {
      const date = new Date(dateString)
      return date.toLocaleTimeString('en-GB', {
        hour: '2-digit',
        minute: '2-digit'
      })
    }

    const goBack = () => {
      router.push('/admin/order')
    }

    const updateOrderStatus = async () => {
      if (!selectedStatus.value) {
        toast.error('Please select a status')
        return
      }

      updatingStatus.value = true
      try {
        const response = await axios.patch(`/api/orders/${order.value.order_id}/status`, {
          status: selectedStatus.value
        })

        if (response.data.success) {
          toast.success(`Order status updated to ${selectedStatus.value}!`)
          order.value.status = selectedStatus.value
          showStatusModal.value = false
          selectedStatus.value = ''
        } else {
          toast.error('Error updating order status: ' + response.data.message)
        }
      } catch (error) {
        console.error('Error updating order status:', error)
        toast.error('Error updating order status')
      } finally {
        updatingStatus.value = false
      }
    }

    const getStatusBadgeClass = (status) => {
      const statusClass = status || 'pending'
      switch (statusClass) {
        case 'pending':
          return 'badge bg-warning'
        case 'confirmed':
          return 'badge bg-primary'
        case 'shipped':
          return 'badge bg-info'
        case 'delivered':
          return 'badge bg-success'
        case 'cancelled':
          return 'badge bg-danger'
        default:
          return 'badge bg-warning'
      }
    }

    const getStatusText = (status) => {
      const statusText = status || 'pending'
      switch (statusText) {
        case 'pending':
          return 'Pending'
        case 'confirmed':
          return 'Confirmed'
        case 'shipped':
          return 'Shipped'
        case 'delivered':
          return 'Delivered'
        case 'cancelled':
          return 'Cancelled'
        default:
          return 'Pending'
      }
    }

    const getStatusIcon = (status) => {
      const statusIcon = status || 'pending'
      switch (statusIcon) {
        case 'pending':
          return 'bi bi-clock'
        case 'confirmed':
          return 'bi bi-check-circle'
        case 'shipped':
          return 'bi bi-truck'
        case 'delivered':
          return 'bi bi-check-circle-fill'
        case 'cancelled':
          return 'bi bi-x-circle'
        default:
          return 'bi bi-clock'
      }
    }

    onMounted(() => {
      fetchOrderDetails()
    })

    return {
      order,
      loading,
      showStatusModal,
      selectedStatus,
      updatingStatus,
      formatPrice,
      formatDate,
      formatTime,
      goBack,
      updateOrderStatus,
      getStatusBadgeClass,
      getStatusText,
      getStatusIcon
    }
  }
}
</script>

<style scoped>
.order-details-page {
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
</style>

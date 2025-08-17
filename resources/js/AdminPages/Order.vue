<template>
  <div class="order-management">
    <div class="d-flex align-items-center mb-4">
      <i class="bi bi-cart-check text-warning fs-1 me-3"></i>
      <div>
        <h2 class="mb-1">
          <i class="bi bi-gear me-2"></i>Order Management
        </h2>
        <p class="text-muted mb-0">
          <i class="bi bi-truck me-1"></i>Manage and track all customer orders
        </p>
      </div>
    </div>

    <!-- Order Status Cards -->
    <div class="row mb-4">
      <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="card border-warning h-100 text-center">
          <div class="card-body">
            <p class="text-warning fw-semibold mb-1">PENDING</p>
            <h3 class="fw-bold text-warning mb-2">{{ stats.pending }}</h3>
            <div class="bg-warning bg-opacity-20 rounded-circle d-inline-flex p-3">
              <i class="bi bi-clock text-dark fs-3"></i>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="card border-primary h-100 text-center">
          <div class="card-body">
            <p class="text-primary fw-semibold mb-1">CONFIRMED</p>
            <h3 class="fw-bold text-primary mb-2">{{ stats.confirmed }}</h3>
            <div class="bg-primary bg-opacity-20 rounded-circle d-inline-flex p-3">
              <i class="bi bi-check-circle text-dark fs-3"></i>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="card border-info h-100 text-center">
          <div class="card-body">
            <p class="text-info fw-semibold mb-1">SHIPPED</p>
            <h3 class="fw-bold text-info mb-2">{{ stats.shipped }}</h3>
            <div class="bg-info bg-opacity-20 rounded-circle d-inline-flex p-3">
              <i class="bi bi-truck text-dark fs-3"></i>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="card border-success h-100 text-center">
          <div class="card-body">
            <p class="text-success fw-semibold mb-1">DELIVERED</p>
            <h3 class="fw-bold text-success mb-2">{{ stats.delivered }}</h3>
            <div class="bg-success bg-opacity-20 rounded-circle d-inline-flex p-3">
              <i class="bi bi-check-circle-fill text-dark fs-3"></i>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="card border-danger h-100 text-center">
          <div class="card-body">
            <p class="text-danger fw-semibold mb-1">CANCELLED</p>
            <h3 class="fw-bold text-danger mb-2">{{ stats.cancelled }}</h3>
            <div class="bg-danger bg-opacity-20 rounded-circle d-inline-flex p-3">
              <i class="bi bi-x-circle text-dark fs-3"></i>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="card border-secondary h-100 text-center">
          <div class="card-body">
            <p class="text-secondary fw-semibold mb-1">TOTAL</p>
            <h3 class="fw-bold text-secondary mb-2">{{ stats.total }}</h3>
            <div class="bg-secondary bg-opacity-20 rounded-circle d-inline-flex p-3">
              <i class="bi bi-list-ul text-dark fs-3"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Orders Table -->
    <div class="card shadow-sm">
      <div class="card-header bg-warning bg-opacity-10 d-flex justify-content-between align-items-center">
        <h3 class="mb-0">
          <i class="bi bi-receipt me-2 text-warning"></i>
          Order Catalog
        </h3>
        <div class="d-flex gap-2">
          <button class="btn btn-outline-warning">
            <i class="bi bi-search me-1"></i>Search
          </button>
          <button class="btn btn-outline-warning">
            <i class="bi bi-funnel me-1"></i>Filter
          </button>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Customer</th>
                <th>Status</th>
                <th>Phone</th>
                <th>Total</th>
                <th>Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <!-- Loading State -->
              <tr v-if="loading">
                <td colspan="7" class="text-center py-4">
                  <div class="spinner-border text-warning" role="status">
                    <span class="visually-hidden">Loading...</span>
                  </div>
                  <p class="mt-2 text-muted">Loading orders...</p>
                </td>
              </tr>

              <!-- Empty State -->
              <tr v-else-if="orders.length === 0">
                <td colspan="7" class="text-center py-4">
                  <i class="bi bi-inbox text-muted fs-1 mb-3"></i>
                  <p class="text-muted">No orders found</p>
                </td>
              </tr>

              <!-- Dynamic Order Rows -->
              <tr v-else v-for="order in orders" :key="order.order_id">
                <td>{{ order.order_id }}</td>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="bg-warning bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center fw-bold text-white me-3" style="width: 48px; height: 48px;">
                      {{ getInitials(order.user?.name) }}
                    </div>
                    <div>
                      <p class="fw-semibold mb-0">{{ order.user?.name || 'Unknown' }}</p>
                      <small class="text-muted">{{ order.user?.email || 'No email' }}</small>
                    </div>
                  </div>
                </td>
                <td>
                  <span :class="['badge', getStatusBadge(order)]">{{ getStatusText(order) }}</span>
                </td>
                <td>
                  <p class="fw-medium mb-0">{{ order.user?.phone || 'No phone' }}</p>
                  <small class="text-muted">Order #{{ order.order_id }}</small>
                </td>
                <td>
                  <p class="fw-semibold text-success mb-0">{{ formatPrice(order.total) }} MMK</p>
                  <small class="text-muted">{{ order.order_details?.length || 0 }} items</small>
                </td>
                <td>
                  <p class="fw-medium mb-0">{{ formatDate(order.date) }}</p>
                  <small class="text-muted">
                    <i class="bi bi-clock me-1"></i>
                    {{ formatTime(order.date) }}
                  </small>
                </td>
                <td>
                  <div class="btn-group" role="group">
                    <button class="btn btn-sm btn-outline-primary mx-1" @click="viewOrderDetails(order.order_id)" title="View Details">
                      <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" @click="openStatusModal(order)" title="Update Status">
                      <i class="bi bi-gear"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Status Update Modal -->
    <div v-if="showStatusModal" class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              <i class="bi bi-gear me-2"></i>
              Update Order Status
            </h5>
            <button type="button" class="btn-close" @click="closeStatusModal"></button>
          </div>

          <div class="modal-body">
            <div class="mb-4">
              <label class="form-label fw-semibold">Order Information</label>
              <div class="card bg-light">
                <div class="card-body p-3">
                  <div class="row">
                    <div class="col-6">
                      <small class="text-muted">Order ID</small>
                      <div class="fw-semibold">#{{ selectedOrder?.order_id }}</div>
                    </div>
                    <div class="col-6">
                      <small class="text-muted">Customer</small>
                      <div class="fw-semibold">{{ selectedOrder?.user?.name }}</div>
                    </div>
                  </div>
                  <div class="row mt-2">
                    <div class="col-6">
                      <small class="text-muted">Current Status</small>
                      <div>
                        <span :class="['badge', getStatusBadge(selectedOrder)]">
                          {{ getStatusText(selectedOrder) }}
                        </span>
                      </div>
                    </div>
                    <div class="col-6">
                      <small class="text-muted">Total</small>
                      <div class="fw-semibold text-success">{{ formatPrice(selectedOrder?.total) }} MMK</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="mb-4">
              <label class="form-label fw-semibold">Select New Status</label>
              <div class="d-grid gap-2">
                <label class="btn btn-outline-secondary text-start p-3">
                  <input type="radio" v-model="selectedStatus" value="pending" class="me-3">
                  <i class="bi bi-clock text-warning me-2"></i>
                  <strong>Pending</strong>
                  <small class="d-block text-muted ms-4">Order is waiting to be processed</small>
                </label>
                <label class="btn btn-outline-secondary text-start p-3">
                  <input type="radio" v-model="selectedStatus" value="confirmed" class="me-3">
                  <i class="bi bi-check-circle text-primary me-2"></i>
                  <strong>Confirmed</strong>
                  <small class="d-block text-muted ms-4">Order has been confirmed and is being prepared</small>
                </label>
                <label class="btn btn-outline-secondary text-start p-3">
                  <input type="radio" v-model="selectedStatus" value="shipped" class="me-3">
                  <i class="bi bi-truck text-info me-2"></i>
                  <strong>Shipped</strong>
                  <small class="d-block text-muted ms-4">Order is on its way to the customer</small>
                </label>
                <label class="btn btn-outline-secondary text-start p-3">
                  <input type="radio" v-model="selectedStatus" value="delivered" class="me-3">
                  <i class="bi bi-check-circle-fill text-success me-2"></i>
                  <strong>Delivered</strong>
                  <small class="d-block text-muted ms-4">Order has been successfully delivered</small>
                </label>
                <label class="btn btn-outline-secondary text-start p-3">
                  <input type="radio" v-model="selectedStatus" value="cancelled" class="me-3">
                  <i class="bi bi-x-circle text-danger me-2"></i>
                  <strong>Cancelled</strong>
                  <small class="d-block text-muted ms-4">Order has been cancelled</small>
                </label>
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="closeStatusModal">Cancel</button>
            <button type="button" class="btn btn-primary" @click="updateOrderStatusFromModal" :disabled="!selectedStatus || updatingStatus">
              <span v-if="updatingStatus" class="spinner-border spinner-border-sm me-2"></span>
              Update Status
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, onUnmounted } from 'vue'
import axios from 'axios'
import { toast } from 'vue3-toastify'

export default {
  name: 'OrderManagement',
  setup() {
    const orders = ref([])
    const loading = ref(false)
    const showStatusModal = ref(false)
    const selectedOrder = ref(null)
    const selectedStatus = ref('')
    const updatingStatus = ref(false)
    const stats = ref({
      pending: 0,
      confirmed: 0,
      shipped: 0,
      delivered: 0,
      cancelled: 0,
      total: 0
    })

    const fetchOrders = async () => {
      loading.value = true
      try {
        const response = await axios.get('/api/orders')

        if (response.data.success) {
          orders.value = response.data.orders
          calculateStats()
        } else {
          toast.error('Error fetching orders: ' + response.data.message)
        }
      } catch (error) {
        console.error('Error fetching orders:', error)
        toast.error('Error fetching orders')
      } finally {
        loading.value = false
      }
    }

    const calculateStats = () => {
      // Reset stats
      stats.value = {
        pending: 0,
        confirmed: 0,
        shipped: 0,
        delivered: 0,
        cancelled: 0,
        total: orders.value.length
      }

      // Calculate stats based on order status
      orders.value.forEach(order => {
        const status = order.status || 'pending'
        switch (status) {
          case 'pending':
            stats.value.pending++
            break
          case 'confirmed':
            stats.value.confirmed++
            break
          case 'shipped':
            stats.value.shipped++
            break
          case 'delivered':
            stats.value.delivered++
            break
          case 'cancelled':
            stats.value.cancelled++
            break
          default:
            stats.value.pending++
        }
      })
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

    const getInitials = (name) => {
      return name ? name.split(' ').map(n => n[0]).join('').toUpperCase() : 'U'
    }

    const getStatusBadge = (order) => {
      const status = order.status || 'pending'
      switch (status) {
        case 'pending':
          return 'bg-warning'
        case 'confirmed':
          return 'bg-primary'
        case 'shipped':
          return 'bg-info'
        case 'delivered':
          return 'bg-success'
        case 'cancelled':
          return 'bg-danger'
        default:
          return 'bg-warning'
      }
    }

    const getStatusText = (order) => {
      const status = order.status || 'pending'
      switch (status) {
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

    const viewOrderDetails = (orderId) => {
      // Navigate to order details page
      window.location.href = `/admin/order-details?order_id=${orderId}`
    }

    const confirmOrder = async (orderId) => {
      if (confirm(`Are you sure you want to confirm order #${orderId}?`)) {
        try {
          const response = await axios.post(`/api/orders/${orderId}/confirm`)

          if (response.data.success) {
            toast.success(`Order #${orderId} confirmed successfully!`)
            await fetchOrders() // Refresh the orders list
          } else {
            toast.error('Error confirming order: ' + response.data.message)
          }
        } catch (error) {
          console.error('Error confirming order:', error)
          toast.error('Error confirming order')
        }
      }
    }

            const cancelOrder = async (orderId) => {
      if (confirm(`Are you sure you want to cancel order #${orderId}? This action cannot be undone.`)) {
        try {
          const response = await axios.post(`/api/orders/${orderId}/cancel`)

          if (response.data.success) {
            toast.success(`Order #${orderId} cancelled successfully!`)
            openDropdown.value = null // Close dropdown
            await fetchOrders() // Refresh the orders list
          } else {
            toast.error('Error cancelling order: ' + response.data.message)
          }
        } catch (error) {
          console.error('Error cancelling order:', error)
          toast.error('Error cancelling order')
        }
      }
    }

            const openStatusModal = (order) => {
      selectedOrder.value = order
      selectedStatus.value = order.status || 'pending'
      showStatusModal.value = true
    }

    const closeStatusModal = () => {
      showStatusModal.value = false
      selectedOrder.value = null
      selectedStatus.value = ''
    }

    const updateOrderStatusFromModal = async () => {
      if (!selectedStatus.value || !selectedOrder.value) {
        toast.error('Please select a status')
        return
      }

      updatingStatus.value = true
      try {
        const response = await axios.patch(`/api/orders/${selectedOrder.value.order_id}/status`, {
          status: selectedStatus.value
        })

        if (response.data.success) {
          toast.success(`Order #${selectedOrder.value.order_id} status updated to ${selectedStatus.value}!`)
          closeStatusModal()
          await fetchOrders() // Refresh the orders list
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

    onMounted(async () => {
      await fetchOrders()
    })

    return {
      orders,
      loading,
      showStatusModal,
      selectedOrder,
      selectedStatus,
      updatingStatus,
      stats,
      formatPrice,
      formatDate,
      formatTime,
      getInitials,
      getStatusBadge,
      getStatusText,
      viewOrderDetails,
      confirmOrder,
      cancelOrder,
      openStatusModal,
      closeStatusModal,
      updateOrderStatusFromModal
    }
  }
}
</script>

<style scoped>
.order-management {
  padding: 20px;
}
</style>

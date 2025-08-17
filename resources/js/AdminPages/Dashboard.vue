<template>
  <div class="dashboard">
    <!-- Loading State -->
    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <p class="mt-3 text-muted">Loading dashboard data...</p>
    </div>

    <!-- Dashboard Content -->
    <div v-else>
      <!-- Stats Cards -->
      <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
              <div class="rounded-circle p-3 me-3">
                <i class="bi bi-cash-coin text-pink-500 fs-3"></i>
              </div>
              <div>
                <p class="text-muted small mb-1">Total Revenue</p>
                <p class="text-muted small mb-1">{{ today }}</p>
                <h4 class="fw-bold mb-0">{{ stats.total_revenue }}</h4>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
              <div class="rounded-circle p-3 me-3">
                <i class="bi bi-graph-up text-black fs-3"></i>
              </div>
              <div>
                <p class="text-muted small mb-1">Today's Revenue</p>
                <p class="text-muted small mb-1">{{ today }}</p>
                <h4 class="fw-bold mb-0">{{ stats.today_revenue }}</h4>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
              <div class="rounded-circle p-3 me-3">
                <i class="bi bi-bag text-black fs-3"></i>
              </div>
              <div>
                <p class="text-muted small mb-1">Total Orders</p>
                <p class="text-muted small mb-1">{{ today }}</p>
                <h4 class="fw-bold mb-0">{{ stats.total_orders }} orders</h4>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
              <div class="rounded-circle p-3 me-3">
                <i class="bi bi-people text-black fs-3"></i>
              </div>
              <div>
                <p class="text-muted small mb-1">Total Users</p>
                <p class="text-muted small mb-1">{{ today }}</p>
                <h4 class="fw-bold mb-0">{{ stats.total_users }} users</h4>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Additional Stats Row -->
      <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
              <div class="rounded-circle p-3 me-3">
                <i class="bi bi-calendar-check text-black fs-3"></i>
              </div>
              <div>
                <p class="text-muted small mb-1">Today's Orders</p>
                <p class="text-muted small mb-1">{{ today }}</p>
                <h4 class="fw-bold mb-0">{{ stats.today_orders }} orders</h4>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
              <div class="rounded-circle p-3 me-3">
                <i class="bi bi-grid-3x3-gap text-black fs-3"></i>
              </div>
              <div>
                <p class="text-muted small mb-1">Total Tables</p>
                <p class="text-muted small mb-1">{{ today }}</p>
                <h4 class="fw-bold mb-0">{{ stats.total_tables }} tables</h4>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
              <div class="rounded-circle p-3 me-3">
                <i class="bi bi-bookmark-star text-black fs-3"></i>
              </div>
              <div>
                <p class="text-muted small mb-1">Reservations</p>
                <p class="text-muted small mb-1">{{ today }}</p>
                <h4 class="fw-bold mb-0">{{ stats.total_reservations }} bookings</h4>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
              <div class="rounded-circle p-3 me-3">
                <i class="bi bi-chat-dots text-black fs-3"></i>
              </div>
              <div>
                <p class="text-muted small mb-1">Messages</p>
                <p class="text-muted small mb-1">{{ today }}</p>
                <h4 class="fw-bold mb-0">{{ stats.total_messages }} messages</h4>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Tables Section -->
      <div class="card shadow-sm mb-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
          <h3 class="card-title mb-0">
            <i class="bi bi-grid-3x3-gap-fill me-2 text-warning"></i>
            Restaurant Tables Overview
          </h3>
          <div class="d-flex gap-2">
            <span class="badge bg-success">{{ tableStatus.available }} Available</span>
            <span class="badge bg-danger">{{ tableStatus.occupied }} Occupied</span>
            <span class="badge bg-warning">{{ tableStatus.reserved }} Reserved</span>
            <span class="badge bg-secondary">{{ tableStatus.maintenance || 0 }} Maintenance</span>
          </div>
        </div>
        <div class="card-body">
          <div class="row g-3 mb-4">
            <div class="col-lg-2 col-md-3 col-sm-4 col-6" v-for="table in tables" :key="table.id">
              <div class="card table-card" :class="getTableCardClass(table.status)">
                <div class="card-body text-center p-2">
                  <h6 class="card-title mb-1">{{ table.name }}</h6>
                  <p class="card-text small mb-1">
                    <i class="bi bi-people me-1"></i>{{ table.seats }} seats
                  </p>
                  <p class="card-text small mb-2">
                    <i class="bi bi-geo-alt me-1"></i>{{ table.location }}
                  </p>
                  <span :class="`badge bg-${getTableStatusColor(table.status)}`">
                    {{ table.status }}
                  </span>
                  <div class="mt-2">
                    <button
                      @click="selectTable(table.id)"
                      class="btn btn-sm btn-outline-primary"
                      title="View Details"
                    >
                      <i class="bi bi-eye"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Activity Row -->
      <div class="row">
        <!-- Recent Orders -->
        <div class="col-lg-8 mb-4">
          <div class="card shadow-sm h-100">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
              <h3 class="card-title mb-0">
                <i class="bi bi-clock-history me-2 text-warning"></i>
                Recent Orders
              </h3>
              <router-link to="/admin/order" class="btn btn-sm btn-outline-primary">
                View All
              </router-link>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead class="table-light">
                    <tr>
                      <th>#</th>
                      <th>Customer</th>
                      <th>Items</th>
                      <th>Total</th>
                      <th>Status</th>
                      <th>Date</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="order in recentOrders" :key="order.id">
                      <td>{{ order.id }}</td>
                      <td>
                        <div class="d-flex align-items-center">
                          <div class="bg-warning bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center fw-bold text-white me-2" style="width: 32px; height: 32px; font-size: 14px;">
                            {{ order.customer_name.charAt(0).toUpperCase() }}
                          </div>
                          {{ order.customer_name }}
                        </div>
                      </td>
                      <td>{{ order.items }} items</td>
                      <td>{{ order.total }}</td>
                      <td>
                        <span :class="getStatusBadgeClass(order.status)">
                          {{ order.status }}
                        </span>
                      </td>
                      <td>{{ order.date }}</td>
                      <td>
                        <router-link :to="`/admin/order/${order.id}`" class="btn btn-sm btn-outline-primary">
                          <i class="bi bi-eye"></i>
                        </router-link>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Reservations & Messages -->
        <div class="col-lg-4 mb-4">
          <div class="card shadow-sm h-100">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
              <h3 class="card-title mb-0">
                <i class="bi bi-calendar-event me-2 text-info"></i>
                Recent Reservations
              </h3>
              <router-link to="/admin/reservation-management" class="btn btn-sm btn-outline-info">
                View All
              </router-link>
            </div>
            <div class="card-body">
              <div v-for="reservation in recentReservations" :key="reservation.id" class="border-bottom pb-2 mb-2">
                <div class="d-flex justify-content-between align-items-start">
                  <div>
                    <h6 class="mb-1">{{ reservation.customer_name }}</h6>
                    <p class="text-muted small mb-1">{{ reservation.table_name }} - {{ reservation.guests }} guests</p>
                    <p class="text-muted small mb-0">{{ formatDate(reservation.date) }} at {{ formatTime(reservation.time) }}</p>
                  </div>
                  <span :class="getReservationStatusBadgeClass(reservation.status)">
                    {{ reservation.status }}
                  </span>
                </div>
              </div>
              <div v-if="recentReservations.length === 0" class="text-center text-muted py-3">
                <i class="bi bi-calendar-x fs-1"></i>
                <p class="mt-2">No recent reservations</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Messages -->
      <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
          <h3 class="card-title mb-0">
            <i class="bi bi-chat-dots me-2 text-success"></i>
            Recent Messages
          </h3>
          <router-link to="/admin/message" class="btn btn-sm btn-outline-success">
            View All
          </router-link>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover">
              <thead class="table-light">
                <tr>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Subject</th>
                  <th>Status</th>
                  <th>Date</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="message in recentMessages" :key="message.id">
                  <td>{{ message.name }}</td>
                  <td>{{ message.email }}</td>
                  <td>{{ message.subject }}</td>
                  <td>
                    <span :class="message.status === 'Read' ? 'badge bg-success' : 'badge bg-warning'">
                      {{ message.status }}
                    </span>
                  </td>
                  <td>{{ message.date }}</td>
                  <td>
                    <router-link :to="`/admin/message/${message.id}`" class="btn btn-sm btn-outline-success">
                      <i class="bi bi-eye"></i>
                    </router-link>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Dashboard',
  data() {
    return {
      loading: true,
      stats: {
        total_users: 0,
        total_orders: 0,
        total_tables: 0,
        total_reservations: 0,
        total_messages: 0,
        total_revenue: '0 mmk',
        today_revenue: '0 mmk',
        today_orders: 0
      },
      tableStatus: {
        total: 0,
        available: 0,
        occupied: 0,
        reserved: 0,
        maintenance: 0
      },
      tables: [],
      recentOrders: [],
      recentReservations: [],
      recentMessages: [],
      today: new Date().toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    }
  },
  async mounted() {
    await this.fetchDashboardData();
  },
  methods: {
    async fetchDashboardData() {
      try {
        this.loading = true;
        const response = await fetch('/api/dashboard/stats');
        const data = await response.json();

        if (data.success) {
          this.stats = data.data.stats;
          this.tableStatus = data.data.table_status;
          this.tables = data.data.tables || [];
          this.recentOrders = data.data.recent_orders;
          this.recentReservations = data.data.recent_reservations;
          this.recentMessages = data.data.recent_messages;
        }
      } catch (error) {
        console.error('Error fetching dashboard data:', error);
      } finally {
        this.loading = false;
      }
    },
    getTableStatusClass(tableNumber) {
      // Simulate table status based on table number for now
      // In a real app, this would come from the API
      if ([1, 2, 4, 6, 7, 10, 12, 13, 18, 19, 21, 24, 27, 28, 30].includes(tableNumber)) {
        return 'btn-danger';
      } else if ([8, 14, 20, 26, 29].includes(tableNumber)) {
        return 'btn-warning';
      } else {
        return 'btn-success';
      }
    },
    getTableCardClass(status) {
      const classes = {
        available: 'border-success',
        occupied: 'border-danger',
        reserved: 'border-warning',
        maintenance: 'border-secondary'
      }
      return classes[status] || 'border-info';
    },
    getTableStatusColor(status) {
      const colors = {
        available: 'success',
        occupied: 'danger',
        reserved: 'warning',
        maintenance: 'secondary'
      }
      return colors[status] || 'info';
    },
    getStatusBadgeClass(status) {
      switch (status) {
        case 'completed':
          return 'badge bg-success';
        case 'pending':
          return 'badge bg-warning';
        case 'processing':
        case 'shipped':
          return 'badge bg-info';
        case 'cancelled':
          return 'badge bg-danger';
        default:
          return 'badge bg-secondary';
      }
    },
    getReservationStatusBadgeClass(status) {
      switch (status) {
        case 'confirmed':
          return 'badge bg-success';
        case 'pending':
          return 'badge bg-warning';
        case 'cancelled':
          return 'badge bg-danger';
        case 'completed':
          return 'badge bg-info';
        default:
          return 'badge bg-secondary';
      }
    },
    selectTable(tableNumber) {
      this.$router.push(`/admin/table-management?table=${tableNumber}`);
    },
    formatDate(date) {
      return new Date(date).toLocaleDateString()
    },
    formatTime(time) {
      return new Date(`2000-01-01T${time}`).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
    }
  }
}
</script>

<style scoped>
.dashboard {
  padding: 20px;
}

.bg-pink-100 {
  background-color: #fce7f3;
}

.text-pink-500 {
  color: #ec4899;
}

.bg-purple {
  background-color: #6f42c1;
}

.text-purple {
  color: #6f42c1;
}

.bg-teal {
  background-color: #20c997;
}

.text-teal {
  color: #20c997;
}

.bg-opacity-10 {
  opacity: 0.1;
}

.card {
  transition: transform 0.2s ease-in-out;
}

.card:hover {
  transform: translateY(-2px);
}

.table-responsive {
  max-height: 400px;
  overflow-y: auto;
}

.table-card {
  transition: transform 0.2s, box-shadow 0.2s;
  cursor: pointer;
}

.table-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
</style>

<template>
  <div class="manage-table">
    <!-- Stats Cards -->
    <div class="row mb-4">
      <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-success h-100 shadow-sm">
          <div class="card-body d-flex align-items-center">
            <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3">
              <i class="bi bi-check-circle-fill text-success fs-3"></i>
            </div>
            <div>
              <p class="text-muted small mb-1">
                <i class="bi bi-table me-1"></i>Available
              </p>
              <h4 class="fw-bold mb-0">7</h4>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-danger h-100 shadow-sm">
          <div class="card-body d-flex align-items-center">
            <div class="bg-danger bg-opacity-10 rounded-circle p-3 me-3">
              <i class="bi bi-x-circle-fill text-danger fs-3"></i>
            </div>
            <div>
              <p class="text-muted small mb-1">
                <i class="bi bi-table me-1"></i>Unavailable
              </p>
              <h4 class="fw-bold mb-0">20</h4>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-warning h-100 shadow-sm">
          <div class="card-body d-flex align-items-center">
            <div class="bg-warning bg-opacity-10 rounded-circle p-3 me-3">
              <i class="bi bi-clock-history text-warning fs-3"></i>
            </div>
            <div>
              <p class="text-muted small mb-1">
                <i class="bi bi-calendar-check me-1"></i>Booking
              </p>
              <h4 class="fw-bold mb-0">3</h4>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-info h-100 shadow-sm">
          <div class="card-body d-flex align-items-center">
            <div class="bg-info bg-opacity-10 rounded-circle p-3 me-3">
              <i class="bi bi-graph-up text-info fs-3"></i>
            </div>
            <div>
              <p class="text-muted small mb-1">
                <i class="bi bi-speedometer2 me-1"></i>Utilization
              </p>
              <h4 class="fw-bold mb-0">77%</h4>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Table Management Section -->
    <div class="card shadow-sm">
      <div class="card-header bg-warning text-white">
        <div class="d-flex align-items-center">
          <i class="bi bi-grid-3x3-gap-fill text-white fs-1 me-3"></i>
          <div>
            <h3 class="mb-1">
              <i class="bi bi-gear me-2"></i>All Tables
            </h3>
            <p class="mb-0 opacity-75">
              <i class="bi bi-mouse me-1"></i>Click on tables to manage their status
            </p>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <!-- Table 1-6 -->
          <div class="col-lg-2 col-md-3 col-sm-4 col-6">
            <button class="btn btn-danger w-100 py-3 rounded-3" @click="toggleTableStatus('Table 1')">
              <div class="fw-bold">
                <i class="bi bi-table me-1"></i>Table 1
              </div>
              <small class="opacity-75">
                <i class="bi bi-people me-1"></i>4 seats
              </small>
            </button>
          </div>
          <div class="col-lg-2 col-md-3 col-sm-4 col-6">
            <button class="btn btn-danger w-100 py-3 rounded-3" @click="toggleTableStatus('Table 2')">
              <div class="fw-bold">Table 2</div>
              <small class="opacity-75">4 seats</small>
            </button>
          </div>
          <div class="col-lg-2 col-md-3 col-sm-4 col-6">
            <button class="btn btn-success w-100 py-3 rounded-3" @click="toggleTableStatus('Table 3')">
              <div class="fw-bold">Table 3</div>
              <small class="opacity-75">4 seats</small>
            </button>
          </div>
          <div class="col-lg-2 col-md-3 col-sm-4 col-6">
            <button class="btn btn-danger w-100 py-3 rounded-3" @click="toggleTableStatus('Table 4')">
              <div class="fw-bold">Table 4</div>
              <small class="opacity-75">6 seats</small>
            </button>
          </div>
          <div class="col-lg-2 col-md-3 col-sm-4 col-6">
            <button class="btn btn-success w-100 py-3 rounded-3" @click="toggleTableStatus('Table 5')">
              <div class="fw-bold">Table 5</div>
              <small class="opacity-75">6 seats</small>
            </button>
          </div>
          <div class="col-lg-2 col-md-3 col-sm-4 col-6">
            <button class="btn btn-danger w-100 py-3 rounded-3" @click="toggleTableStatus('Table 6')">
              <div class="fw-bold">Table 6</div>
              <small class="opacity-75">4 seats</small>
            </button>
          </div>

          <!-- Table 7-12 -->
          <div class="col-lg-2 col-md-3 col-sm-4 col-6">
            <button class="btn btn-danger w-100 py-3 rounded-3" @click="toggleTableStatus('Table 7')">
              <div class="fw-bold">Table 7</div>
              <small class="opacity-75">4 seats</small>
            </button>
          </div>
          <div class="col-lg-2 col-md-3 col-sm-4 col-6">
            <button class="btn btn-warning w-100 py-3 rounded-3" @click="toggleTableStatus('Table 8')">
              <div class="fw-bold">Table 8</div>
              <small class="opacity-75">6 seats</small>
            </button>
          </div>
          <div class="col-lg-2 col-md-3 col-sm-4 col-6">
            <button class="btn btn-success w-100 py-3 rounded-3" @click="toggleTableStatus('Table 9')">
              <div class="fw-bold">Table 9</div>
              <small class="opacity-75">4 seats</small>
            </button>
          </div>
          <div class="col-lg-2 col-md-3 col-sm-4 col-6">
            <button class="btn btn-danger w-100 py-3 rounded-3" @click="toggleTableStatus('Table 10')">
              <div class="fw-bold">Table 10</div>
              <small class="opacity-75">4 seats</small>
            </button>
          </div>
          <div class="col-lg-2 col-md-3 col-sm-4 col-6">
            <button class="btn btn-success w-100 py-3 rounded-3" @click="toggleTableStatus('Table 11')">
              <div class="fw-bold">Table 11</div>
              <small class="opacity-75">4 seats</small>
            </button>
          </div>
          <div class="col-lg-2 col-md-3 col-sm-4 col-6">
            <button class="btn btn-danger w-100 py-3 rounded-3" @click="toggleTableStatus('Table 12')">
              <div class="fw-bold">Table 12</div>
              <small class="opacity-75">4 seats</small>
            </button>
          </div>

          <!-- Table 13-18 -->
          <div class="col-lg-2 col-md-3 col-sm-4 col-6">
            <button class="btn btn-danger w-100 py-3 rounded-3" @click="toggleTableStatus('Table 13')">
              <div class="fw-bold">Table 13</div>
              <small class="opacity-75">6 seats</small>
            </button>
          </div>
          <div class="col-lg-2 col-md-3 col-sm-4 col-6">
            <button class="btn btn-danger w-100 py-3 rounded-3" @click="toggleTableStatus('Table 14')">
              <div class="fw-bold">Table 14</div>
              <small class="opacity-75">4 seats</small>
            </button>
          </div>
          <div class="col-lg-2 col-md-3 col-sm-4 col-6">
            <button class="btn btn-danger w-100 py-3 rounded-3" @click="toggleTableStatus('Table 15')">
              <div class="fw-bold">Table 15</div>
              <small class="opacity-75">4 seats</small>
            </button>
          </div>
          <div class="col-lg-2 col-md-3 col-sm-4 col-6">
            <button class="btn btn-danger w-100 py-3 rounded-3" @click="toggleTableStatus('Table 16')">
              <div class="fw-bold">Table 16</div>
              <small class="opacity-75">6 seats</small>
            </button>
          </div>
          <div class="col-lg-2 col-md-3 col-sm-4 col-6">
            <button class="btn btn-success w-100 py-3 rounded-3" @click="toggleTableStatus('Table 17')">
              <div class="fw-bold">Table 17</div>
              <small class="opacity-75">8 seats</small>
            </button>
          </div>
          <div class="col-lg-2 col-md-3 col-sm-4 col-6">
            <button class="btn btn-danger w-100 py-3 rounded-3" @click="toggleTableStatus('Table 18')">
              <div class="fw-bold">Table 18</div>
              <small class="opacity-75">4 seats</small>
            </button>
          </div>

          <!-- Table 19-24 -->
          <div class="col-lg-2 col-md-3 col-sm-4 col-6">
            <button class="btn btn-danger w-100 py-3 rounded-3" @click="toggleTableStatus('Table 19')">
              <div class="fw-bold">Table 19</div>
              <small class="opacity-75">4 seats</small>
            </button>
          </div>
          <div class="col-lg-2 col-md-3 col-sm-4 col-6">
            <button class="btn btn-danger w-100 py-3 rounded-3" @click="toggleTableStatus('Table 20')">
              <div class="fw-bold">Table 20</div>
              <small class="opacity-75">6 seats</small>
            </button>
          </div>
          <div class="col-lg-2 col-md-3 col-sm-4 col-6">
            <button class="btn btn-danger w-100 py-3 rounded-3" @click="toggleTableStatus('Table 21')">
              <div class="fw-bold">Table 21</div>
              <small class="opacity-75">4 seats</small>
            </button>
          </div>
          <div class="col-lg-2 col-md-3 col-sm-4 col-6">
            <button class="btn btn-success w-100 py-3 rounded-3" @click="toggleTableStatus('Table 22')">
              <div class="fw-bold">Table 22</div>
              <small class="opacity-75">4 seats</small>
            </button>
          </div>
          <div class="col-lg-2 col-md-3 col-sm-4 col-6">
            <button class="btn btn-success w-100 py-3 rounded-3" @click="toggleTableStatus('Table 23')">
              <div class="fw-bold">Table 23</div>
              <small class="opacity-75">6 seats</small>
            </button>
          </div>
          <div class="col-lg-2 col-md-3 col-sm-4 col-6">
            <button class="btn btn-danger w-100 py-3 rounded-3" @click="toggleTableStatus('Table 24')">
              <div class="fw-bold">Table 24</div>
              <small class="opacity-75">6 seats</small>
            </button>
          </div>

          <!-- Table 25-30 -->
          <div class="col-lg-2 col-md-3 col-sm-4 col-6">
            <button class="btn btn-success w-100 py-3 rounded-3" @click="toggleTableStatus('Table 25')">
              <div class="fw-bold">Table 25</div>
              <small class="opacity-75">2 seats</small>
            </button>
          </div>
          <div class="col-lg-2 col-md-3 col-sm-4 col-6">
            <button class="btn btn-warning w-100 py-3 rounded-3" @click="toggleTableStatus('Table 26')">
              <div class="fw-bold">Table 26</div>
              <small class="opacity-75">6 seats</small>
            </button>
          </div>
          <div class="col-lg-2 col-md-3 col-sm-4 col-6">
            <button class="btn btn-danger w-100 py-3 rounded-3" @click="toggleTableStatus('Table 27')">
              <div class="fw-bold">Table 27</div>
              <small class="opacity-75">4 seats</small>
            </button>
          </div>
          <div class="col-lg-2 col-md-3 col-sm-4 col-6">
            <button class="btn btn-danger w-100 py-3 rounded-3" @click="toggleTableStatus('Table 28')">
              <div class="fw-bold">Table 28</div>
              <small class="opacity-75">6 seats</small>
            </button>
          </div>
          <div class="col-lg-2 col-md-3 col-sm-4 col-6">
            <button class="btn btn-warning w-100 py-3 rounded-3" @click="toggleTableStatus('Table 29')">
              <div class="fw-bold">Table 29</div>
              <small class="opacity-75">6 seats</small>
            </button>
          </div>
          <div class="col-lg-2 col-md-3 col-sm-4 col-6">
            <button class="btn btn-danger w-100 py-3 rounded-3" @click="toggleTableStatus('Table 30')">
              <div class="fw-bold">Table 30</div>
              <small class="opacity-75">4 seats</small>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Table Status Legend -->
    <div class="card shadow-sm mt-4">
      <div class="card-header">
        <h5 class="mb-0">
          <i class="bi bi-info-circle-fill me-2 text-info"></i>
          Table Status Legend
        </h5>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-3 mb-2">
            <div class="d-flex align-items-center">
              <div class="bg-success rounded me-2" style="width: 20px; height: 20px;"></div>
              <span>Available</span>
            </div>
          </div>
          <div class="col-md-3 mb-2">
            <div class="d-flex align-items-center">
              <div class="bg-danger rounded me-2" style="width: 20px; height: 20px;"></div>
              <span>Unavailable</span>
            </div>
          </div>
          <div class="col-md-3 mb-2">
            <div class="d-flex align-items-center">
              <div class="bg-warning rounded me-2" style="width: 20px; height: 20px;"></div>
              <span>Booking</span>
            </div>
          </div>
          <div class="col-md-3 mb-2">
            <div class="d-flex align-items-center">
              <div class="bg-info rounded me-2" style="width: 20px; height: 20px;"></div>
              <span>Reserved</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ManageTable',
  methods: {
    toggleTableStatus(tableName) {
      // This method can be implemented to handle table status changes
      console.log(`Toggling status for ${tableName}`);
      // You can add logic here to change table status
    }
  }
}
</script>

<style scoped>
.manage-table {
  padding: 1.5rem;
}

.btn {
  transition: all 0.3s ease;
}

.btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

/* Consistent spacing with other admin pages */
.card {
  margin-bottom: 1.5rem;
}

.card-body {
  padding: 1.5rem;
}

/* Better table button spacing */
.row.g-3 > div {
  margin-bottom: 1rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .manage-table {
    padding: 1rem;
  }

  .card-body {
    padding: 1rem;
  }
}
</style>

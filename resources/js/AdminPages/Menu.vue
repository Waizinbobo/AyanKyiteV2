<template>
  <div class="menu-management">
    <!-- Stats Cards -->
    <div class="row mb-4">
      <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body d-flex align-items-center">
            <div class="bg-warning bg-opacity-10 rounded-circle p-3 me-3">
              <i class="bi bi-list-ul text-warning fs-3"></i>
            </div>
            <div>
              <p class="text-muted small mb-1">Total Menu Items</p>
              <h4 class="fw-bold mb-0">{{ menuStats.totalItems }}</h4>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body d-flex align-items-center">
            <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3">
              <i class="bi bi-currency-dollar text-success fs-3"></i>
            </div>
            <div>
              <p class="text-muted small mb-1">Total Value</p>
              <h4 class="fw-bold mb-0">{{ menuStats.totalValue }} mmk</h4>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body d-flex align-items-center">
            <div class="bg-info bg-opacity-10 rounded-circle p-3 me-3">
              <i class="bi bi-graph-up text-info fs-3"></i>
            </div>
            <div>
              <p class="text-muted small mb-1">Active Items</p>
              <h4 class="fw-bold mb-0">{{ menuStats.activeItems }}</h4>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body d-flex align-items-center">
            <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
              <i class="bi bi-tags text-primary fs-3"></i>
            </div>
            <div>
              <p class="text-muted small mb-1">Categories</p>
              <h4 class="fw-bold mb-0">{{ menuStats.categoriesCount }}</h4>
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
            <div class="bg-danger bg-opacity-10 rounded-circle p-3 me-3">
              <i class="bi bi-x-circle text-danger fs-3"></i>
            </div>
            <div>
              <p class="text-muted small mb-1">Inactive Items</p>
              <h4 class="fw-bold mb-0">{{ menuStats.inactiveItems }}</h4>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body d-flex align-items-center">
            <div class="bg-secondary bg-opacity-10 rounded-circle p-3 me-3">
              <i class="bi bi-image text-secondary fs-3"></i>
            </div>
            <div>
              <p class="text-muted small mb-1">With Images</p>
              <h4 class="fw-bold mb-0">{{ menuStats.itemsWithImages }}</h4>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body d-flex align-items-center">
            <div class="bg-warning bg-opacity-10 rounded-circle p-3 me-3">
              <i class="bi bi-currency-exchange text-warning fs-3"></i>
            </div>
            <div>
              <p class="text-muted small mb-1">Avg Price</p>
              <h4 class="fw-bold mb-0">{{ menuStats.averagePrice }} mmk</h4>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body d-flex align-items-center">
            <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3">
              <i class="bi bi-arrow-up-circle text-success fs-3"></i>
            </div>
            <div>
              <p class="text-muted small mb-1">Highest Price</p>
              <h4 class="fw-bold mb-0">{{ menuStats.highestPrice }} mmk</h4>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Menu Table -->
    <div class="card shadow-sm">
      <div class="card-header bg-warning bg-opacity-10 d-flex justify-content-between align-items-center">
        <h3 class="mb-0">
          <i class="bi bi-journal-text me-2 text-warning"></i>
          Menu Catalog
        </h3>
        <div class="d-flex gap-2 align-items-center">
          <div class="input-group" style="max-width: 360px;">
            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
            <input type="text" class="form-control shadow-sm" placeholder="Search by name" v-model="searchQuery">
            <button v-if="searchQuery" class="btn btn-outline-secondary" @click="searchQuery = ''" type="button">
              <i class="bi bi-x"></i>
            </button>
          </div>
          <button class="btn btn-outline-warning">
            <i class="bi bi-funnel me-1"></i>Filter
          </button>
          <button @click="openAddModal" class="btn btn-warning text-white fs-6">
            <i class="bi bi-plus-circle me-2"></i>
            Add New Menu
          </button>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Image</th>
                <th>Price</th>
                <th>Name</th>
                <th>Description</th>
                <th>Category</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="loading" class="text-center">
                <td colspan="8">
                  <div class="spinner-border text-warning" role="status">
                    <span class="visually-hidden">Loading...</span>
                  </div>
                </td>
              </tr>
              <tr v-else-if="filteredMenus.length === 0" class="text-center">
                <td colspan="8">
                  <p class="text-muted">No menu items found</p>
                </td>
              </tr>
              <tr v-else v-for="menu in filteredMenus" :key="menu.id">
                <td>{{ menu.id }}</td>
                <td>
                  <img
                    v-if="menu.image && menu.image !== ''"
                    :src="getImageUrl(menu.image)"
                    :alt="menu.name"
                    class="rounded-circle"
                    style="width: 48px; height: 48px; object-fit: cover;"
                    @error="handleImageError"
                    />
                  <div v-else class="bg-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="bi bi-image text-white"></i>
                  </div>
                </td>
                <td class="text-end fw-medium">{{ formatPrice(menu.price) }} mmk</td>
                <td>{{ menu.name }}</td>
                <td class="desc-cell">
                  <div class="line-clamp-2" :title="menu.description || 'No description'">{{ menu.description || 'No description' }}</div>
                  <div v-if="menu.ingredients" class="text-muted small line-clamp-1" :title="menu.ingredients">
                    <strong>Ingredients:</strong> {{ menu.ingredients }}
                  </div>
                </td>
                <td>
                  <span class="badge bg-light text-dark border">{{ menu.category?.name || 'No category' }}</span>
                </td>
                <td>
                  <span :class="menu.status ? 'badge rounded-pill bg-success' : 'badge rounded-pill bg-secondary'">
                    {{ menu.status ? 'Active' : 'Inactive' }}
                  </span>
                </td>
                <td>
                  <div class="btn-group" role="group">
                    <button @click="viewMenu(menu)" class="btn btn-sm btn-outline-secondary mx" title="View">
                      <i class="bi bi-eye"></i>
                    </button>
                    <button @click="editMenu(menu)" class="btn btn-sm btn-outline-primary mx-1" title="Edit">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button @click="deleteMenu(menu.id)" class="btn btn-sm btn-outline-danger" title="Delete">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Add/Edit Menu Modal -->
    <div class="modal fade" :class="{ show: showAddModal || showEditModal }" :style="{ display: (showAddModal || showEditModal) ? 'block' : 'none' }" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              {{ showEditModal ? 'Edit Menu Item' : 'Add New Menu Item' }}
            </h5>
            <button @click="closeModal" type="button" class="btn-close"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveMenu">
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Category</label>
                    <select v-model="form.category_id" class="form-select" required>
                      <option value="">Select Category</option>
                      <option v-for="category in categories" :key="category.id" :value="category.id">
                        {{ category.name }}
                      </option>
                    </select>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Menu Name</label>
                    <input v-model="form.name" type="text" class="form-control" required>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Price</label>
                    <input v-model="form.price" type="number" step="0.01" class="form-control" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea v-model="form.description" class="form-control" rows="3"></textarea>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Ingredients</label>
                    <textarea v-model="form.ingredients" class="form-control" rows="3" placeholder="e.g. Chicken, garlic, ginger, soy sauce..."></textarea>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Image</label>
                    <input @change="handleImageUpload" type="file" class="form-control" accept="image/*">
                    <div v-if="form.image" class="mt-2">
                      <img :src="getImageUrl(form.image)" alt="Preview" class="img-thumbnail" style="max-width: 100px; max-height: 100px;">
                    </div>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select v-model="form.status" class="form-select">
                      <option :value="true">Active</option>
                      <option :value="false">Inactive</option>
                    </select>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button @click="closeModal" type="button" class="btn btn-secondary">Cancel</button>
            <button @click="saveMenu" type="button" class="btn btn-warning text-white">
              {{ showEditModal ? 'Update' : 'Save' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Backdrop -->
    <div v-if="showAddModal || showEditModal || showViewModal" class="modal-backdrop fade show"></div>

    <!-- View Details Modal -->
    <div class="modal fade" :class="{ show: showViewModal }" :style="{ display: showViewModal ? 'block' : 'none' }" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Menu Details</h5>
            <button @click="closeModal" type="button" class="btn-close"></button>
          </div>
          <div class="modal-body" v-if="viewingMenu">
            <div class="row g-3">
              <div class="col-md-4 text-center">
                <img v-if="viewingMenu.image" :src="getImageUrl(viewingMenu.image)" :alt="viewingMenu.name" class="img-fluid rounded" />
                <div v-else class="bg-secondary rounded d-flex align-items-center justify-content-center" style="width: 100%; height: 160px;">
                  <i class="bi bi-image text-white fs-1"></i>
                </div>
              </div>
              <div class="col-md-8">
                <p class="mb-1"><strong>Name:</strong> {{ viewingMenu.name }}</p>
                <p class="mb-1"><strong>Category:</strong> {{ viewingMenu.category?.name || 'No category' }}</p>
                <p class="mb-1"><strong>Price:</strong> {{ viewingMenu.price }} mmk</p>
                <p class="mb-1"><strong>Status:</strong>
                  <span :class="viewingMenu.status ? 'badge bg-success' : 'badge bg-secondary'">
                    {{ viewingMenu.status ? 'Active' : 'Inactive' }}
                  </span>
                </p>
                <hr />
                <p class="mb-1"><strong>Description</strong></p>
                <p class="text-muted">{{ viewingMenu.description || 'No description' }}</p>
                <p v-if="viewingMenu.ingredients" class="mb-1"><strong>Ingredients</strong></p>
                <p v-if="viewingMenu.ingredients" class="text-muted">{{ viewingMenu.ingredients }}</p>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button @click="closeModal" type="button" class="btn btn-secondary">Close</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { toast } from 'vue3-toastify';
import axios from 'axios';

export default {
  name: 'MenuManagement',
  data() {
    return {
      loading: false,
      categories: [],
      menus: [],
      searchQuery: '',
      showAddModal: false,
      showEditModal: false,
      showViewModal: false,
      viewingMenu: null,
      editingMenu: null,
      form: {
        category_id: '',
        name: '',
        description: '',
        ingredients: '',
        image: '',
        price: '',
        status: true
      }
    }
  },
  computed: {
    filteredMenus() {
      if (!this.searchQuery) return this.menus;
      const q = this.searchQuery.toLowerCase();
      return this.menus.filter(m => (m.name && m.name.toLowerCase().includes(q)));
    },
    menuStats() {
      if (this.menus.length === 0) {
        return {
          totalItems: 0,
          totalValue: '0',
          activeItems: 0,
          inactiveItems: 0,
          categoriesCount: this.categories.length,
          itemsWithImages: 0,
          averagePrice: '0',
          highestPrice: '0'
        };
      }

      const totalItems = this.menus.length;
      const activeItems = this.menus.filter(menu => menu.status).length;
      const inactiveItems = totalItems - activeItems;
      const itemsWithImages = this.menus.filter(menu => menu.image && menu.image !== '').length;

      const totalValue = this.menus.reduce((sum, menu) => sum + parseFloat(menu.price), 0);
      const averagePrice = totalItems > 0 ? (totalValue / totalItems).toFixed(0) : 0;
      const highestPrice = Math.max(...this.menus.map(menu => parseFloat(menu.price)));

      return {
        totalItems,
        totalValue: totalValue.toLocaleString(),
        activeItems,
        inactiveItems,
        categoriesCount: this.categories.length,
        itemsWithImages,
        averagePrice: averagePrice.toLocaleString(),
        highestPrice: highestPrice.toLocaleString()
      };
    }
  },
  mounted() {
    this.fetchMenus();
    this.fetchCategories();
  },
  methods: {
    formatPrice(value) {
      if (value === null || value === undefined || value === '') return '0';
      const n = Number(value);
      if (Number.isNaN(n)) return String(value);
      return n.toLocaleString();
    },
    async fetchMenus() {
      this.loading = true;
      try {
        const response = await axios.get('/admin/menus');

        if (response.data.success) {
          this.menus = response.data.data;
          console.log('Fetched menus:', this.menus);
          console.log('Menu count:', this.menus.length);
          if (this.menus.length > 0) {
            console.log('First menu:', this.menus[0]);
            console.log('First menu image:', this.menus[0].image);
            console.log('Image type:', typeof this.menus[0].image);
          }
        } else {
          toast.error('Error fetching menus: ' + response.data.message);
        }
      } catch (error) {
        console.error('Error:', error);
        toast.error('Error fetching menus');
      } finally {
        this.loading = false;
      }
    },

    async fetchCategories() {
      try {
        const response = await axios.get('/admin/menus/categories/list');

        if (response.data.success) {
          this.categories = response.data.data;
        }
      } catch (error) {
        console.error('Error fetching categories:', error);
      }
    },

    editMenu(menu) {
      this.editingMenu = menu;
      this.form = {
        category_id: menu.category_id,
        name: menu.name,
        description: menu.description || '',
        ingredients: menu.ingredients || '',
        image: menu.image || '',
        price: menu.price,
        status: menu.status
      };
      // Clear any previous file selection
      this.form.imageFile = null;
      this.showEditModal = true;
    },

    viewMenu(menu) {
      this.viewingMenu = menu;
      this.showViewModal = true;
    },

    async deleteMenu(id) {
      if (confirm('Are you sure you want to delete this menu?')) {
        try {
          const response = await axios.delete(`/admin/menus/${id}`);

          if (response.data.success) {
            this.menus = this.menus.filter(menu => menu.id !== id);
            toast.success('Menu deleted successfully!');
          } else {
            toast.error('Error deleting menu: ' + response.data.message);
          }
        } catch (error) {
          console.error('Error:', error);
          toast.error('Error deleting menu');
        }
      }
    },

    handleImageUpload(event) {
      const file = event.target.files[0];
      console.log('File selected:', file);
      if (file) {
        // Create a preview URL for the image
        this.form.image = URL.createObjectURL(file);
        // Store the actual file for upload
        this.form.imageFile = file;
        console.log('Image file stored:', this.form.imageFile);
        console.log('Preview URL:', this.form.image);
      }
    },

    handleImageError(event) {
      console.error('Image failed to load:', event.target.src);
      event.target.style.display = 'none';
    },

    openAddModal() {
      console.log('Opening add modal');
      this.showAddModal = true;
    },

        async saveMenu() {
      console.log('saveMenu called');
      console.log('Form data:', this.form);
      try {
        const url = this.showEditModal
          ? `/admin/menus/${this.editingMenu.id}`
          : '/admin/menus';

        const formData = new FormData();
        formData.append('category_id', this.form.category_id);
        formData.append('name', this.form.name);
        formData.append('description', this.form.description);
        formData.append('ingredients', this.form.ingredients);
        formData.append('price', this.form.price);
        formData.append('status', this.form.status ? '1' : '0');

        // Handle image upload
        if (this.form.imageFile) {
          formData.append('image', this.form.imageFile);
        }
        // Note: If no new image is selected during edit, the backend will keep the existing image

        // Debug: Log form data
        console.log('Form data being sent:');
        for (let [key, value] of formData.entries()) {
          console.log(key, value);
        }

        console.log('Image file:', this.form.imageFile);
        console.log('Image path:', this.form.image);
        console.log('FormData entries:');
        for (let [key, value] of formData.entries()) {
          console.log(`${key}:`, value);
        }

        // Add _method field for PUT requests
        if (this.showEditModal) {
          formData.append('_method', 'PUT');
        }

        console.log('Sending request to:', url);
        const response = await axios.post(url, formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
          }
        });
        console.log('Response status:', response.status);

        if (response.data.success) {
          if (this.showEditModal) {
            const index = this.menus.findIndex(menu => menu.id === this.editingMenu.id);
            this.menus[index] = response.data.data;
            toast.success('Menu updated successfully!');
          } else {
            this.menus.unshift(response.data.data);
            toast.success('Menu added successfully!');
          }
          this.closeModal();
          this.fetchMenus();
        } else {
          if (response.data.errors) {
            // Show specific validation errors
            const errorMessages = Object.values(response.data.errors).flat().join(', ');
            toast.error('Validation errors: ' + errorMessages);
          } else {
            toast.error('Error saving menu: ' + response.data.message);
          }
        }
      } catch (error) {
        console.error('Error:', error);
        toast.error('Error saving menu');
      }
    },

    closeModal() {
      this.showAddModal = false;
      this.showEditModal = false;
      this.showViewModal = false;
      this.editingMenu = null;
      this.viewingMenu = null;
      this.form = {
        category_id: '',
        name: '',
        description: '',
        ingredients: '',
        image: '',
        imageFile: null,
        price: '',
        status: true
      };
    },

    showToast(message, type = 'info') {
      const backgroundColor = type === 'success' ? '#28a745' :
                             type === 'error' ? '#dc3545' :
                             type === 'warning' ? '#ffc107' : '#17a2b8';

      Toastify({
        text: message,
        duration: 3000,
        gravity: "top",
        position: "right",
        backgroundColor: backgroundColor,
        color: "#fff",
        stopOnFocus: true,
        close: true
      }).showToast();
    },
         getImageUrl(image) {
         if (!image) return '';
         if (image.startsWith('http') || image.startsWith('blob:')) {
             return image;
         }
         // If image already has /storage/ prefix, return as is
         if (image.startsWith('/storage/')) {
             return image;
         }
         // If image starts with 'menus/', add /storage/ prefix
         if (image.startsWith('menus/')) {
             return `/storage/${image}`;
         }
         // Otherwise, add /storage/ prefix
         return `/storage/${image}`;
     },
  }
}
</script>

<style scoped>
.menu-management {
  padding: 20px;
}
/* Clamp long text in description/ingredients */
.desc-cell { max-width: 380px; }
.line-clamp-1 {
  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 1;
  overflow: hidden;
}
.line-clamp-2 {
  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 2;
  overflow: hidden;
}
</style>

<template>
  <div class="category-management">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h2 class="mb-1">
          <i class="bi bi-tags-fill me-2 text-warning"></i>
          Category Management
        </h2>
        <p class="text-muted mb-0">
          <i class="bi bi-gear me-1"></i>Organize your menu categories
        </p>
      </div>
      <button @click="openAddModal" class="btn btn-warning text-white">
        <i class="bi bi-plus-circle me-2"></i>
        Add Category
      </button>
    </div>



    <!-- Categories Table -->
    <div class="card shadow-sm">
      <div class="card-body">
        <!-- Loading Indicator -->
        <div v-if="loading" class="text-center py-4">
          <div class="spinner-border text-warning" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <p class="mt-2">Loading categories...</p>
        </div>

        <div v-else class="table-responsive">
          <table class="table table-hover">
            <thead class="table-light">
              <tr>
                <th>Cat ID</th>
                <th>Image</th>
                <th>Cat Name</th>
                <th>Description</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="category in categories" :key="category.cat_id">
                <td>{{ category.cat_id }}</td>
                <td>
                  <img v-if="category.image" :src="getImageUrl(category.image)" alt="Category Image" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                  <div v-else class="bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="bi bi-image text-muted"></i>
                  </div>
                </td>
                <td>{{ category.cat_name }}</td>
                <td>
                  <span v-if="category.description" class="text-truncate d-inline-block" style="max-width: 200px;" :title="category.description">
                    {{ category.description }}
                  </span>
                  <span v-else class="text-muted">No description</span>
                </td>
                <td>
                  <span :class="category.status ? 'badge bg-success' : 'badge bg-danger'">
                    {{ category.status ? 'Active' : 'Inactive' }}
                  </span>
                </td>
                <td>
                  <div class="btn-group" role="group">
                    <button @click="editCategory(category)" class="btn btn-sm btn-outline-primary mx-1">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button @click="deleteCategory(category.cat_id)" class="btn btn-sm btn-outline-danger">
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

    <!-- Add/Edit Category Modal -->
    <div class="modal fade" :class="{ show: showAddModal || showEditModal }" :style="{ display: (showAddModal || showEditModal) ? 'block' : 'none' }" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              {{ showEditModal ? 'Edit Category' : 'Add New Category' }}
            </h5>
            <button @click="closeModal" type="button" class="btn-close"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveCategory">
              <div class="mb-3">
                <label class="form-label">Category Name</label>
                <input v-model="form.cat_name" type="text" class="form-control" required>
                <small class="text-muted" v-if="!showEditModal">Existing categories: {{ existingCategoryNames.join(', ') }}</small>
                <small class="text-muted" v-else>Editing category: {{ editingCategory?.cat_name }}</small>
              </div>
              <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea v-model="form.description" class="form-control" rows="3" placeholder="Enter category description..."></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label">Category Image</label>
                <input ref="imageInput" @change="handleImageUpload" type="file" class="form-control" accept="image/*">
                <small class="text-muted">Upload JPG, PNG, GIF (max 2MB)</small>

                <!-- Image Preview -->
                <div v-if="imagePreview" class="mt-2">
                  <img :src="imagePreview" alt="Category Image Preview" class="img-thumbnail" style="max-width: 150px; height: 100px; object-fit: cover;">
                  <small class="text-muted d-block mt-1">Image preview</small>
                </div>
                <div v-else-if="form.image && !selectedImage" class="mt-2">
                  <img :src="getImageUrl(form.image)" alt="Current Category Image" class="img-thumbnail" style="max-width: 150px; height: 100px; object-fit: cover;">
                  <small class="text-muted d-block mt-1">Current image</small>
                </div>
                <div v-else class="mt-2">
                  <div class="bg-light d-flex align-items-center justify-content-center" style="width: 150px; height: 100px; border: 1px dashed #ccc;">
                    <i class="bi bi-image text-muted"></i>
                  </div>
                  <small class="text-muted d-block mt-1">No image selected</small>
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label">Status</label>
                <select v-model="form.status" class="form-select">
                  <option :value="true">Active</option>
                  <option :value="false">Inactive</option>
                </select>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button @click="closeModal" type="button" class="btn btn-secondary">Cancel</button>
            <button @click="saveCategory" type="button" class="btn btn-warning text-white">
              {{ showEditModal ? 'Update' : 'Save' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Backdrop -->
    <div v-if="showAddModal || showEditModal" class="modal-backdrop fade show"></div>
  </div>
</template>

<script>
import { toast } from 'vue3-toastify';
import axios from 'axios';

export default {
  name: 'CategoryManagement',
  data() {
    return {
      loading: false,
      showAddModal: false,
      showEditModal: false,
      editingCategory: null,
      categories: [],
      form: {
        cat_name: '',
        description: '',
        image: null,
        status: true
      },
      selectedImage: null,
      imagePreview: null
    }
  },
  mounted() {
    this.fetchCategories();
  },
  computed: {
    existingCategoryNames() {
      return this.categories.map(category => category.cat_name);
    }
  },
  methods: {
    async fetchCategories() {
      this.loading = true;
      try {
        const response = await axios.get('/admin/categories');

        if (response.data.success) {
          this.categories = response.data.data;
        } else {
          toast.error('Error fetching categories: ' + response.data.message);
        }
      } catch (error) {
        console.error('Error:', error);
        toast.error('Error fetching categories');
      } finally {
        this.loading = false;
      }
    },

    editCategory(category) {
      this.editingCategory = category;
      this.selectedImage = null; // Clear any selected image
      this.imagePreview = null; // Clear any image preview
      this.form = {
        cat_name: category.cat_name,
        description: category.description || '',
        image: category.image || null,
        status: category.status
      };
      this.showEditModal = true;
    },

    handleImageUpload(event) {
      const file = event.target.files[0];

      if (file) {
        // Validate file type
        if (!file.type.startsWith('image/')) {
          toast.error('Please select a valid image file');
          event.target.value = '';
          return;
        }

        // Validate file size (2MB)
        if (file.size > 2 * 1024 * 1024) {
          toast.error('Image size should be less than 2MB');
          event.target.value = '';
          return;
        }

        this.selectedImage = file;
        // Create preview URL for the selected file
        this.imagePreview = URL.createObjectURL(file);
      } else {
        this.selectedImage = null;
        this.imagePreview = null;
      }
    },

    async saveCategory() {
      try {
        const url = this.showEditModal
          ? `/admin/categories/${this.editingCategory.cat_id}`
          : '/admin/categories';

        const formData = new FormData();
        formData.append('cat_name', this.form.cat_name);
        formData.append('description', this.form.description);
        formData.append('status', this.form.status ? '1' : '0');

        if (this.selectedImage) {
          formData.append('image', this.selectedImage);
        }

        // Add _method field for PUT requests
        if (this.showEditModal) {
          formData.append('_method', 'PUT');
        }

        const response = await axios.post(url, formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
          }
        });

        if (response.data.success) {
          toast.success(this.showEditModal ? 'Category updated successfully!' : 'Category created successfully!');
          this.closeModal();
          this.fetchCategories();
        } else {
          toast.error('Error: ' + response.data.message);
        }
      } catch (error) {
        console.error('Error:', error);
        if (error.response?.data?.message) {
          toast.error('Error: ' + error.response.data.message);
        } else if (error.response?.data?.errors) {
          // Handle specific validation errors
          const errors = error.response.data.errors;
          if (errors.cat_name) {
            toast.error('Category name error: ' + errors.cat_name[0]);
          } else if (errors.description) {
            toast.error('Description error: ' + errors.description[0]);
          } else if (errors.image) {
            toast.error('Image error: ' + errors.image[0]);
          } else {
            const errorMessages = Object.values(errors).flat().join(', ');
            toast.error('Validation errors: ' + errorMessages);
          }
        } else {
          toast.error('Error saving category');
        }
      }
    },

    async deleteCategory(categoryId) {
      if (!confirm('Are you sure you want to delete this category?')) {
        return;
      }

      try {
        const response = await axios.delete(`/admin/categories/${categoryId}`);

        if (response.data.success) {
          toast.success('Category deleted successfully!');
          this.fetchCategories();
        } else {
          toast.error('Error: ' + response.data.message);
        }
      } catch (error) {
        console.error('Error:', error);
        toast.error('Error deleting category');
      }
    },

    openAddModal() {
      this.selectedImage = null;
      this.imagePreview = null;
      this.form = {
        cat_name: '',
        description: '',
        image: null,
        status: true
      };
      this.showAddModal = true;
    },

    getImageUrl(imagePath) {
      if (!imagePath) return '';
      if (imagePath.startsWith('data:')) return imagePath;
      if (imagePath.startsWith('http')) return imagePath;
      if (imagePath.startsWith('/')) return import.meta.env.VITE_APP_URL + imagePath;
      return import.meta.env.VITE_APP_URL + '/' + imagePath;
    },

    closeModal() {
      this.showAddModal = false;
      this.showEditModal = false;
      this.editingCategory = null;
      this.selectedImage = null;

      // Revoke object URL to prevent memory leaks
      if (this.imagePreview) {
        URL.revokeObjectURL(this.imagePreview);
        this.imagePreview = null;
      }

      // Clear file input
      if (this.$refs.imageInput) {
        this.$refs.imageInput.value = '';
      }
      this.form = {
        cat_name: '',
        description: '',
        image: null,
        status: true
      };
    }
  }
}
</script>

<style scoped>
.modal-backdrop {
  z-index: 1040;
}

.modal {
  z-index: 1050;
}
</style>



<template>
  <div class="blog-management">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h2 class="mb-1">
          <i class="bi bi-journal-text me-2 text-warning"></i>
          Blog Management
        </h2>
        <p class="text-muted mb-0">
          <i class="bi bi-gear me-1"></i>Manage your blog posts
        </p>
      </div>
      <button @click="openAddModal" class="btn btn-warning text-white">
        <i class="bi bi-plus-circle me-2"></i>
        Add Blog Post
      </button>
    </div>

    <!-- Blogs Table -->
    <div class="card shadow-sm">
      <div class="card-body">
        <div v-if="loading" class="text-center py-4">
          <div class="spinner-border text-warning" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <p class="mt-2">Loading blogs...</p>
        </div>

        <div v-else class="table-responsive">
          <table class="table table-hover">
            <thead class="table-light">
              <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Title</th>
                <th>Excerpt</th>
                <th>Author</th>
                <th>Status</th>
                <th>Published</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="blog in blogs" :key="blog.id">
                <td>{{ blog.id }}</td>
                <td>
                  <img v-if="blog.image" :src="getImageUrl(blog.image)" alt="Blog Image" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                  <div v-else class="bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="bi bi-image text-muted"></i>
                  </div>
                  <!-- <small v-if="blog.image" class="text-muted d-block mt-1" style="font-size: 10px;">{{ blog.image }}</small> -->
                </td>
                <td>
                  <strong>{{ blog.title }}</strong>
                  <br>
                  <small class="text-muted">{{ blog.slug }}</small>
                </td>
                <td>
                  <span v-if="blog.excerpt" class="text-truncate d-inline-block" style="max-width: 200px;" :title="blog.excerpt">
                    {{ blog.excerpt }}
                  </span>
                  <span v-else class="text-muted">No excerpt</span>
                </td>
                <td>{{ blog.author || 'Admin' }}</td>
                <td>
                  <span :class="blog.status ? 'badge bg-success' : 'badge bg-danger'">
                    {{ blog.status ? 'Published' : 'Draft' }}
                  </span>
                </td>
                <td>
                  <small v-if="blog.published_at">{{ formatDate(blog.published_at) }}</small>
                  <small v-else class="text-muted">Not published</small>
                </td>
                <td>
                  <div class="btn-group" role="group">
                    <button @click="editBlog(blog)" class="btn btn-sm btn-outline-primary mx-1">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button @click="deleteBlog(blog.id)" class="btn btn-sm btn-outline-danger">
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

    <!-- Add/Edit Blog Modal -->
    <div class="modal fade" :class="{ show: showAddModal || showEditModal }" :style="{ display: (showAddModal || showEditModal) ? 'block' : 'none' }" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              {{ showEditModal ? 'Edit Blog Post' : 'Add New Blog Post' }}
            </h5>
            <button @click="closeModal" type="button" class="btn-close"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveBlog">
              <div class="row">
                <div class="col-md-8">
                  <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input v-model="form.title" type="text" class="form-control" required>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Content</label>
                    <textarea v-model="form.content" class="form-control" rows="10" placeholder="Write your blog content here..." required></textarea>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Excerpt</label>
                    <textarea v-model="form.excerpt" class="form-control" rows="3" placeholder="Brief summary of the blog post..."></textarea>
                    <small class="text-muted">Maximum 500 characters</small>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="mb-3">
                    <label class="form-label">Blog Image</label>
                    <input ref="imageInput" @change="handleImageUpload" type="file" class="form-control" accept="image/*">
                    <small class="text-muted">Upload JPG, PNG, GIF (max 2MB)</small>

                    <!-- Image Preview -->
                    <div v-if="imagePreview" class="mt-2">
                      <img :src="imagePreview" alt="Blog Image Preview" class="img-thumbnail" style="max-width: 100%; height: 150px; object-fit: cover;">
                      <small class="text-muted d-block mt-1">Image preview</small>
                    </div>
                    <div v-else-if="form.image && !selectedImage" class="mt-2">
                      <img :src="getImageUrl(form.image)" alt="Current Blog Image" class="img-thumbnail" style="max-width: 100%; height: 150px; object-fit: cover;">
                      <!-- <small class="text-muted d-block mt-1">Current image (Path: {{ form.image }})</small> -->
                    </div>
                    <div v-else class="mt-2">
                      <div class="bg-light d-flex align-items-center justify-content-center" style="width: 100%; height: 150px; border: 1px dashed #ccc;">
                        <i class="bi bi-image text-muted"></i>
                      </div>
                      <small class="text-muted d-block mt-1">No image selected</small>
                    </div>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Author</label>
                    <input v-model="form.author" type="text" class="form-control" placeholder="Author name">
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select v-model="form.status" class="form-select">
                      <option :value="true">Published</option>
                      <option :value="false">Draft</option>
                    </select>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button @click="closeModal" type="button" class="btn btn-secondary">Cancel</button>
            <button @click="saveBlog" type="button" class="btn btn-warning text-white">
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
  name: 'AdminBlog',
  data() {
    return {
      loading: false,
      showAddModal: false,
      showEditModal: false,
      editingBlog: null,
      blogs: [],
      form: {
        title: '',
        content: '',
        excerpt: '',
        image: null,
        author: 'Admin',
        status: true
      },
      selectedImage: null,
      imagePreview: null
    }
  },
  mounted() {
    this.fetchBlogs();
  },
  methods: {
    async fetchBlogs() {
      this.loading = true;
      try {
        const response = await axios.get('/admin/blogs');

        if (response.data.success) {
          this.blogs = response.data.data;
          console.log('Fetched blogs:', this.blogs); // Debug log
        } else {
          toast.error('Error fetching blogs: ' + response.data.message);
        }
      } catch (error) {
        console.error('Error:', error);
        toast.error('Error fetching blogs');
      } finally {
        this.loading = false;
      }
    },

    editBlog(blog) {
      this.editingBlog = blog;
      this.selectedImage = null;
      this.imagePreview = null;
      this.form = {
        title: blog.title,
        content: blog.content,
        excerpt: blog.excerpt || '',
        image: blog.image || null,
        author: blog.author || 'Admin',
        status: blog.status
      };
      this.showEditModal = true;
    },

    handleImageUpload(event) {
      const file = event.target.files[0];

      if (file) {
        if (!file.type.startsWith('image/')) {
          toast.error('Please select a valid image file');
          event.target.value = '';
          return;
        }

        if (file.size > 2 * 1024 * 1024) {
          toast.error('Image size should be less than 2MB');
          event.target.value = '';
          return;
        }

        this.selectedImage = file;
        this.imagePreview = URL.createObjectURL(file);
      } else {
        this.selectedImage = null;
        this.imagePreview = null;
      }
    },

    async saveBlog() {
      try {
        const url = this.showEditModal
          ? `/admin/blogs/${this.editingBlog.id}`
          : '/admin/blogs';

        const formData = new FormData();
        formData.append('title', this.form.title);
        formData.append('content', this.form.content);
        formData.append('excerpt', this.form.excerpt);
        formData.append('author', this.form.author);
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
          toast.success(this.showEditModal ? 'Blog updated successfully!' : 'Blog created successfully!');
          this.closeModal();
          this.fetchBlogs();
        } else {
          toast.error('Error: ' + response.data.message);
        }
      } catch (error) {
        console.error('Error:', error);
        toast.error('Error saving blog');
      }
    },

    async deleteBlog(blogId) {
      if (confirm('Are you sure you want to delete this blog?')) {
        try {
          const response = await axios.delete(`/admin/blogs/${blogId}`);

          if (response.data.success) {
            this.blogs = this.blogs.filter(blog => blog.id !== blogId);
            toast.success('Blog deleted successfully!');
          } else {
            toast.error('Error deleting blog: ' + response.data.message);
          }
        } catch (error) {
          console.error('Error:', error);
          toast.error('Error deleting blog');
        }
      }
    },

    openAddModal() {
      this.selectedImage = null;
      this.imagePreview = null;
      this.form = {
        title: '',
        content: '',
        excerpt: '',
        image: null,
        author: 'Admin',
        status: true
      };
      this.showAddModal = true;
    },

    getImageUrl(imagePath) {
      if (!imagePath) return '';
      if (imagePath.startsWith('data:')) return imagePath;
      if (imagePath.startsWith('http')) return imagePath;
      // Handle Laravel storage paths
      if (imagePath.startsWith('blogs/')) {
        const url = '/storage/' + imagePath;
        console.log('Image URL generated:', url); // Debug log
        return url;
      }
      const url = '/' + imagePath;
      console.log('Image URL generated:', url); // Debug log
      return url;
    },

    formatDate(dateString) {
      return new Date(dateString).toLocaleDateString();
    },

    closeModal() {
      this.showAddModal = false;
      this.showEditModal = false;
      this.editingBlog = null;
      this.selectedImage = null;

      if (this.imagePreview) {
        URL.revokeObjectURL(this.imagePreview);
        this.imagePreview = null;
      }

      if (this.$refs.imageInput) {
        this.$refs.imageInput.value = '';
      }
      this.form = {
        title: '',
        content: '',
        excerpt: '',
        image: null,
        author: 'Admin',
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

<style>
@import 'toastify-js/src/toastify.css';
</style>

<template>
  <div class="menu-page">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
      <div class="container-fluid">
        <a class="navbar-brand" href="#">{{ restaurantName }}</a>
        <form class="d-flex w-75" role="search" @submit.prevent="searchItems">
          <input
            class="form-control me-2"
            type="search"
            placeholder="Search menu items..."
            aria-label="Search"
            v-model="searchQuery"
          />
          <button class="btn btn-outline-success" type="submit">
            <i class="fas fa-search"></i>
          </button>
        </form>
      </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid w-100 main-content">
      <div class="row pt-3">
        <!-- Left Column - Menu Items -->
        <div class="col-lg-9">
          <!-- Categories Section -->
          <div class="categories-section">
            <h4 class="section-title">Explore Categories</h4>
            <div v-if="loading" class="text-center py-4">
              <div class="spinner-border text-warning" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
              <p class="mt-2">Loading categories...</p>
            </div>
            <div v-else class="row d-flex justify-content-center align-items-center">
              <div
                v-for="category in categories"
                :key="category.cat_id"
                class="col-lg-3"
                @click="selectCategory(category.cat_id)"
              >
                <div class="card category-card my-2 shadow rounded" :class="{ active: selectedCategory === category.cat_id }">
                  <div class="row">
                    <div class="col-lg-4 d-flex align-items-center justify-content-center">
                      <img v-if="category.image" :src="getImageUrl(category.image)" alt="Category" class="img-fluid" style="max-width: 40px; max-height: 40px;">
                      <i v-else class="card-icon fa-solid fa-utensils fs-2 text-center"></i>
                    </div>
                    <div class="col-lg-8">
                      <div class="card-body">
                        <h6 class="card-title fs-6">{{ category.cat_name }}</h6>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Menu Items Section -->
          <div class="menu-items-section">
            <nav class="navbar navbar-expand-lg">
              <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                  <a
                    class="nav-link"
                    :class="{ active: activeTab === 'all' }"
                    @click="setActiveTab('all')"
                    href="#"
                  >
                    All Items
                  </a>
                </li>
                <li class="nav-item">
                  <a
                    class="nav-link"
                    :class="{ active: activeTab === 'popular' }"
                    @click="setActiveTab('popular')"
                    href="#"
                  >
                    Popular
                  </a>
                </li>
                <li class="nav-item">
                  <a
                    class="nav-link"
                    :class="{ active: activeTab === 'recent' }"
                    @click="setActiveTab('recent')"
                    href="#"
                  >
                    Recent
                  </a>
                </li>
              </ul>
            </nav>

            <!-- Menu Items Grid -->
            <div v-if="loading" class="text-center py-4">
              <div class="spinner-border text-warning" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
              <p class="mt-2">Loading menu items...</p>
            </div>
            <div v-else-if="filteredMenuItems.length === 0" class="text-center py-4">
              <i class="fas fa-utensils text-muted fs-1"></i>
              <p class="text-muted">No menu items found</p>
            </div>
            <div v-else class="row">
              <div
                v-for="item in filteredMenuItems"
                :key="item.id"
                class="col-lg-4 col-md-6 mb-3"
              >
                <div class="card menu-card h-100">
                  <div class="img-container d-flex justify-content-center align-items-center">
                    <img
                      :src="getImageUrl(item.image)"
                      class="card-img-top w-50 rounded-2 my-3"
                      :alt="item.name"
                      @error="handleImageError"
                      @load="handleImageLoad"
                    >
                  </div>
                  <div class="card-body d-flex flex-column">
                    <h5 class="card-title">{{ item.name }}</h5>
                    <p class="card-text">{{ item.description || 'No description available' }}</p>
                    <p class="card-text price">
                      {{ parseFloat(item.price).toLocaleString() }} <small class="text-warning">MMK</small>
                    </p>
                    <div class="btn-box mt-auto">
                      <button @click="viewDetails(item)" class="btn btn-primary me-2">
                        Details
                      </button>
                      <button @click="addToCart(item)" class="btn btn-warning">
                        <i class="fas fa-cart-plus"></i> Add to Cart
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column - Cart -->
        <div class="col-lg-3">
          <div class="cart-container shadow p-3 mb-5 bg-body-tertiary rounded">
            <h5 class="cart-title">Your Cart</h5>
            <div v-if="cartItems.length === 0" class="empty-cart">
              <i class="fas fa-shopping-cart text-muted fs-1"></i>
              <p class="text-muted">Your cart is empty</p>
            </div>
            <div v-else>
              <!-- Cart Items -->
              <div
                v-for="item in cartItems"
                :key="item.id"
                class="card my-2 cart-item"
              >
                <div class="row g-0">
                  <div class="col-md-4 d-flex justify-content-center align-items-center rounded-2 order-card">
                    <img
                      :src="getImageUrl(item.image)"
                      class="card-img-top w-50 rounded-2 my-3"
                      :alt="item.name"
                      @error="handleImageError"
                    >
                  </div>
                  <div class="col-md-8">
                    <div class="card-body">
                      <h6 class="card-title">{{ item.name }}</h6>
                      <p class="card-text">{{ parseFloat(item.price).toLocaleString() }} MMK</p>
                      <div class="quantity-controls">
                        <button @click="decreaseQuantity(item)" class="btn btn-sm btn-outline-secondary">-</button>
                        <span class="mx-2">{{ item.quantity }}</span>
                        <button @click="increaseQuantity(item)" class="btn btn-sm btn-outline-secondary">+</button>
                        <button @click="removeFromCart(item)" class="btn btn-sm btn-danger ms-2">
                          <i class="fas fa-trash"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Cart Total -->
              <div class="cart-total mt-3">
                <hr>
                <div class="d-flex justify-content-between">
                  <strong>Total:</strong>
                  <strong>{{ cartTotal.toLocaleString() }} MMK</strong>
                </div>
                <button @click="checkout" class="btn btn-success w-100 mt-2">
                  <i class="fas fa-credit-card"></i> Checkout
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { toast } from 'vue3-toastify';

export default {
  name: 'MenuPage',
  data() {
    return {
      restaurantName: 'Ayan Kyite Restaurant',
      searchQuery: '',
      selectedCategory: null,
      activeTab: 'all', // Changed to 'all' to show all items by default
      loading: false,
      categories: [],
      menuItems: [],
      cartItems: []
    }
  },
    computed: {
    filteredMenuItems() {
      let items = this.menuItems.filter(item => item.status); // Only show active items

      // Filter by category
      if (this.selectedCategory) {
        items = items.filter(item => item.category_id == this.selectedCategory);
      }

      // Filter by tab
      if (this.activeTab === 'popular') {
        // Show items with higher prices as "popular" (you can adjust this logic)
        items = items.sort((a, b) => parseFloat(b.price) - parseFloat(a.price)).slice(0, 6);
      } else if (this.activeTab === 'recent') {
        // Show newest items first (assuming they have created_at field)
        items = items.sort((a, b) => new Date(b.created_at) - new Date(a.created_at)).slice(0, 6);
      }

      // Filter by search query
      if (this.searchQuery) {
        items = items.filter(item =>
          item.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          (item.description && item.description.toLowerCase().includes(this.searchQuery.toLowerCase()))
        );
      }

      return items;
    },
    cartTotal() {
      return this.cartItems.reduce((total, item) => total + (parseFloat(item.price) * item.quantity), 0);
    }
  },
  mounted() {
    this.fetchCategories();
    this.fetchMenuItems();
  },
  methods: {
    async fetchCategories() {
      try {
        const response = await axios.get('/admin/categories');
        if (response.data.success) {
          this.categories = response.data.data.filter(cat => cat.status); // Only active categories
          console.log('Fetched categories:', this.categories);
        } else {
          toast.error('Error fetching categories: ' + response.data.message);
        }
      } catch (error) {
        console.error('Error fetching categories:', error);
        toast.error('Error fetching categories');
      }
    },

    async fetchMenuItems() {
      this.loading = true;
      try {
        const response = await axios.get('/admin/menus');
        if (response.data.success) {
          this.menuItems = response.data.data;
          console.log('Fetched menu items:', this.menuItems);
          if (this.menuItems.length > 0) {
            console.log('Sample menu item:', this.menuItems[0]);
            console.log('Sample image path:', this.menuItems[0].image);
          }
        } else {
          toast.error('Error fetching menu items: ' + response.data.message);
        }
      } catch (error) {
        console.error('Error fetching menu items:', error);
        toast.error('Error fetching menu items');
      } finally {
        this.loading = false;
      }
    },

    getImageUrl(image) {
      console.log('Processing image:', image);

      if (!image) {
        console.log('No image provided, using placeholder');
        return this.getPlaceholderImage();
      }

      if (image.startsWith('http') || image.startsWith('blob:')) {
        console.log('Image is already a full URL:', image);
        return image;
      }

      // If image already has /storage/ prefix, return as is
      if (image.startsWith('/storage/')) {
        console.log('Image already has /storage/ prefix:', image);
        return image;
      }

      // If image starts with 'menus/', add /storage/ prefix
      if (image.startsWith('menus/')) {
        const url = `/storage/${image}`;
        console.log('Menu image URL:', url);
        return url;
      }

      // If image starts with 'categories/', add /storage/ prefix
      if (image.startsWith('categories/')) {
        const url = `/storage/${image}`;
        console.log('Category image URL:', url);
        return url;
      }

      // Otherwise, add /storage/ prefix
      const url = `/storage/${image}`;
      console.log('Default image URL:', url);
      return url;
    },

    getPlaceholderImage() {
      // Create a data URL for a simple placeholder image
      const canvas = document.createElement('canvas');
      canvas.width = 200;
      canvas.height = 200;
      const ctx = canvas.getContext('2d');

      // Background
      ctx.fillStyle = '#f8f9fa';
      ctx.fillRect(0, 0, 200, 200);

      // Border
      ctx.strokeStyle = '#dee2e6';
      ctx.lineWidth = 2;
      ctx.strokeRect(1, 1, 198, 198);

      // Icon
      ctx.fillStyle = '#6c757d';
      ctx.font = '48px Arial';
      ctx.textAlign = 'center';
      ctx.textBaseline = 'middle';
      ctx.fillText('🍽️', 100, 100);

      return canvas.toDataURL();
    },
    selectCategory(categoryId) {
      this.selectedCategory = this.selectedCategory === categoryId ? null : categoryId;
    },
    setActiveTab(tab) {
      this.activeTab = tab;
    },
    searchItems() {
      // Search functionality is handled by computed property
      console.log('Searching for:', this.searchQuery);
    },
    viewDetails(item) {
      // Show item details in a modal or alert
      const details = `
Name: ${item.name}
Description: ${item.description || 'No description'}
Price: ${parseFloat(item.price).toLocaleString()} MMK
Category: ${this.getCategoryName(item.category_id)}
      `;
      alert(details);
    },
    getCategoryName(categoryId) {
      const category = this.categories.find(cat => cat.cat_id == categoryId);
      return category ? category.cat_name : 'Unknown Category';
    },

    handleImageError(event) {
      // Set a fallback image when image fails to load
      event.target.src = this.getPlaceholderImage();
      console.log('Image failed to load, using placeholder');
    },

    handleImageLoad(event) {
      console.log('Image loaded successfully:', event.target.src);
    },
    addToCart(item) {
      const existingItem = this.cartItems.find(cartItem => cartItem.id === item.id);
      if (existingItem) {
        existingItem.quantity++;
        toast.success(`${item.name} quantity increased!`);
      } else {
        this.cartItems.push({
          ...item,
          quantity: 1
        });
        toast.success(`${item.name} added to cart!`);
      }
    },
    removeFromCart(item) {
      const index = this.cartItems.findIndex(cartItem => cartItem.id === item.id);
      if (index > -1) {
        this.cartItems.splice(index, 1);
      }
    },
    increaseQuantity(item) {
      item.quantity++;
    },
    decreaseQuantity(item) {
      if (item.quantity > 1) {
        item.quantity--;
      } else {
        this.removeFromCart(item);
      }
    },
    checkout() {
      if (this.cartItems.length === 0) {
        toast.error('Your cart is empty!');
        return;
      }

      const orderDetails = {
        items: this.cartItems,
        total: this.cartTotal,
        itemCount: this.cartItems.reduce((sum, item) => sum + item.quantity, 0)
      };

      // You can implement actual checkout logic here
      // For now, just show order summary
      const summary = `
Order Summary:
Total Items: ${orderDetails.itemCount}
Total Amount: ${this.cartTotal.toLocaleString()} MMK

Items:
${this.cartItems.map(item => `- ${item.name} x${item.quantity} = ${(parseFloat(item.price) * item.quantity).toLocaleString()} MMK`).join('\n')}
      `;

      if (confirm(summary + '\n\nProceed to checkout?')) {
        toast.success('Order placed successfully!');
        this.cartItems = []; // Clear cart
      }
    }
  }
}
</script>

<style scoped>
.menu-page {
  min-height: 100vh;
}

.main-content {
  background: rgb(241, 239, 239);
  min-height: calc(100vh - 76px);
}

.section-title {
  padding-left: 20px;
  margin-bottom: 20px;
  color: #333;
  font-weight: 600;
}

/* Category Cards */
.category-card {
  height: 20%;
  width: 80%;
  cursor: pointer;
  transition: all 0.3s ease;
  border: 2px solid transparent;
}

.category-card:hover,
.category-card.active {
  background: #eed1b0;
  border-color: #EF9B3B;
  color: #EF9B3B;
  transform: translateY(-2px);
}

.card-icon {
  margin-left: 10px;
}

/* Menu Cards */
.menu-card {
  transition: all 0.3s ease;
  border: 1px solid #e0e0e0;
}

.menu-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.img-container {
  background: rgb(195, 195, 195);
  min-height: 150px;
}

.price {
  font-size: 1.1rem;
  font-weight: 600;
  color: #EF9B3B;
}

.btn-box {
  display: flex;
  gap: 8px;
}

/* Cart Styles */
.cart-container {
  background: white;
  position: sticky;
  top: 20px;
}

.cart-title {
  color: #333;
  font-weight: 600;
  margin-bottom: 20px;
}

.empty-cart {
  text-align: center;
  padding: 40px 20px;
}

.cart-item {
  border: 1px solid #e0e0e0;
}

.order-card {
  background: rgb(195, 195, 195);
}

.quantity-controls {
  display: flex;
  align-items: center;
  margin-top: 10px;
}

.cart-total {
  border-top: 2px solid #e0e0e0;
  padding-top: 15px;
}

/* Navigation Tabs */
.nav-link {
  cursor: pointer;
  color: #666;
  transition: color 0.3s ease;
}

.nav-link:hover,
.nav-link.active {
  color: #EF9B3B;
  font-weight: 600;
}

/* Responsive Design */
@media (max-width: 768px) {
  .category-card {
    width: 100%;
  }

  .btn-box {
    flex-direction: column;
  }

  .quantity-controls {
    flex-wrap: wrap;
  }
}
</style>

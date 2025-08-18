<template>
  <div class="menu-page">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg bg-body-tertiary sticky-top menu-navbar">
      <div class="container-fluid py-2">
        <a class="navbar-brand" href="#">{{ restaurantName }}</a>
        <form class="search-form d-flex w-75" role="search" @submit.prevent="searchItems">
          <input
            class="form-control me-2"
            type="search"
            placeholder="Search menu items..."
            aria-label="Search"
            v-model="searchQuery"
          />
          <button class="btn btn-warning" type="submit">
            <i class="fas fa-search"></i>
          </button>
        </form>
        <div class="ms-auto d-none d-lg-block">
          <button type="button" class="btn btn-warning position-relative" @click.prevent="scrollToCart">
            <i class="fas fa-shopping-bag"></i>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">{{ cartItems.length }}</span>
          </button>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid w-100 main-content">
      <!-- Full-width Categories Row -->
      <div class="row pt-3">
        <div class="col-12">
          <div class="categories-section">
            <h4 class="section-title">Explore Categories</h4>
            <div v-if="loading" class="categories-skeleton categories-row">
              <div v-for="n in 8" :key="'cat-skel-' + n" class="skeleton skeleton-chip"></div>
            </div>
            <div v-else class="row d-flex justify-content-start align-items-center categories-row">
              <div
                v-for="category in categories"
                :key="category.cat_id"
                class="col-6 col-md-4 col-lg-3"
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
        </div>
      </div>

      <!-- Two-column Content Row -->
      <div class="row">
        <!-- Left Column - Menu Items -->
        <div class="col-lg-9">
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
            <div v-if="loading" class="row">
              <div v-for="n in 6" :key="'item-skel-' + n" class="col-lg-4 col-md-6 mb-3">
                <div class="card menu-card h-100">
                  <div class="img-container d-flex justify-content-center align-items-center">
                    <div class="skeleton skeleton-img w-100"></div>
                  </div>
                  <div class="card-body d-flex flex-column">
                    <div class="skeleton skeleton-line w-75"></div>
                    <div class="skeleton skeleton-line w-100"></div>
                    <div class="skeleton skeleton-line w-50"></div>
                    <div class="btn-box mt-auto">
                      <div class="skeleton skeleton-btn"></div>
                      <div class="skeleton skeleton-btn"></div>
                    </div>
                  </div>
                </div>
              </div>
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
                        <i class="fas fa-eye"></i> Details
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
          <div id="cart" class="cart-container shadow p-3 mb-5 bg-body-tertiary rounded">
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

    <!-- Details Modal -->
    <teleport to="body">
      <div v-if="showDetails" class="modal-overlay" style="position:fixed;inset:0;z-index:2147483647;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.45);" @click.self="closeDetails" role="dialog" aria-modal="true">
        <div class="modal-dialog-custom">
          <div class="modal-header-custom">
          <h5 class="m-0">{{ selectedItem ? selectedItem.name : '' }}</h5>
          <button type="button" class="btn-close" aria-label="Close" @click="closeDetails"></button>
          </div>
          <div class="modal-body-custom">
          <div class="row g-3">
            <div class="col-md-5 d-flex justify-content-center align-items-center">
              <div class="modal-img-container">
                <img v-if="selectedItem" :src="getImageUrl(selectedItem.image)" :alt="selectedItem.name" @error="handleImageError"/>
              </div>
            </div>
            <div class="col-md-7">
              <p class="text-muted mb-2">{{ getCategoryName(selectedItem ? selectedItem.category_id : null) }}</p>
              <p class="mb-3">{{ selectedItem && selectedItem.description ? selectedItem.description : 'No description available' }}</p>

              <!-- Variant selection (sizes/variants) -->
              <div v-if="(selectedItem && selectedItem.variants && selectedItem.variants.length) || (selectedItem && selectedItem.sizes && selectedItem.sizes.length)" class="mb-3">
                <label class="form-label fw-semibold">Select a variant</label>
                <div class="d-flex flex-wrap align-items-center" style="gap: 12px;">
                  <div
                    v-for="v in (selectedItem && selectedItem.variants ? selectedItem.variants : (selectedItem && selectedItem.sizes ? selectedItem.sizes : []))"
                    :key="v.id || v.name || v.label"
                    class="form-check me-2"
                  >
                    <input class="form-check-input" type="radio" :id="'var-' + (v.id || v.name || v.label)" name="variant" :value="v" v-model="selectedVariant">
                    <label class="form-check-label" :for="'var-' + (v.id || v.name || v.label)">
                      {{ v.name || v.label }}
                      <small v-if="v.price">(+{{ parseFloat(v.price).toLocaleString() }} MMK)</small>
                    </label>
                  </div>
                </div>
              </div>

              <!-- Extras selection -->
              <div v-if="selectedItem && selectedItem.extras && selectedItem.extras.length" class="mb-3">
                <label class="form-label fw-semibold">Extras</label>
                <div class="row">
                  <div class="col-6" v-for="e in selectedItem.extras" :key="e.id || e.name">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" :id="'ex-' + (e.id || e.name)" :value="e" v-model="selectedExtras">
                      <label class="form-check-label" :for="'ex-' + (e.id || e.name)">
                        {{ e.name }} <small v-if="e.price">(+{{ parseFloat(e.price).toLocaleString() }} MMK)</small>
                      </label>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Ingredients selection -->
              <div v-if="selectedItem && selectedItem.ingredients && selectedItem.ingredients.length" class="mb-3">
                <div class="d-flex align-items-center justify-content-between">
                  <label class="form-label fw-semibold mb-0">Ingredients</label>
                  <div class="small">
                    <button type="button" class="btn btn-link p-0 me-2" @click="selectAllIngredients">Select all</button>
                    <button type="button" class="btn btn-link p-0 text-danger" @click="clearAllIngredients">Clear</button>
                  </div>
                </div>
                <div class="row">
                  <div class="col-6" v-for="ing in selectedItem.ingredients" :key="ing.id || ing.name">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" :id="'ing-' + (ing.id || ing.name)" :value="ing" v-model="selectedIngredients">
                      <label class="form-check-label" :for="'ing-' + (ing.id || ing.name)">
                        {{ ing.name }}
                        <small v-if="ing.price">(+{{ parseFloat(ing.price).toLocaleString() }} MMK)</small>
                      </label>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Notes -->
              <div class="mb-3">
                <label class="form-label fw-semibold">Notes</label>
                <textarea class="form-control" rows="2" v-model="specialInstructions" placeholder="E.g., less spicy, no onions"></textarea>
              </div>

              <!-- Quantity and total -->
              <div class="d-flex align-items-center gap-2">
                <div class="input-group" style="width: 150px;">
                  <button class="btn btn-outline-secondary" type="button" @click="decrementModalQty">-</button>
                  <input type="text" class="form-control text-center" :value="modalQuantity" readonly>
                  <button class="btn btn-outline-secondary" type="button" @click="incrementModalQty">+</button>
                </div>
                <div class="ms-auto h5 m-0">
                  <span class="price">{{ modalTotalPrice.toLocaleString() }}</span>
                  <small class="text-warning"> MMK</small>
                </div>
              </div>
            </div>
          </div>
          </div>
          <div class="modal-footer-custom">
          <button class="btn btn-warning" @click.stop="addConfiguredToCart()"><i class="fas fa-cart-plus"></i> Add to Cart</button>
          <button class="btn btn-outline-secondary" @click="closeDetails">Close</button>
          </div>
        </div>
      </div>
    </teleport>
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
      cartItems: [],
      showDetails: false,
      selectedItem: null,
      selectedVariant: null,
      selectedExtras: [],
      selectedIngredients: [],
      specialInstructions: '',
      modalQuantity: 1
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
    modalTotalPrice() {
      if (!this.selectedItem) return 0;
      const basePrice = parseFloat(this.selectedItem.price) || 0;
      const variantPrice = this.selectedVariant ? parseFloat(this.selectedVariant.price || 0) : 0;
      const extrasPrice = (this.selectedExtras || []).reduce((sum, ex) => sum + (parseFloat(ex.price || 0)), 0);
      const ingredientsPrice = (this.selectedIngredients || []).reduce((sum, ig) => sum + (parseFloat(ig.price || 0)), 0);
      return (basePrice + variantPrice + extrasPrice + ingredientsPrice) * this.modalQuantity;
    },
    cartTotal() {
      return this.cartItems.reduce((total, item) => total + (parseFloat(item.price) * item.quantity), 0);
    }
  },
  mounted() {
    this.fetchCategories();
    this.fetchMenuItems();
    document.addEventListener('keydown', this.onEscClose);
    // Debug mount
    console.log('MenuPage mounted');
  },
  beforeUnmount() {
    document.removeEventListener('keydown', this.onEscClose);
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
      console.log('Opening details for item:', item);
      this.selectedItem = item;
      this.selectedVariant = (item && item.variants && item.variants[0]) || (item && item.sizes && item.sizes[0]) || null;
      // Preselect base ingredients (exclude those with default === false)
      const baseIngredients = (item && item.ingredients) ? item.ingredients : [];
      this.selectedIngredients = baseIngredients.filter(ing => ing && ing.default !== false);
      this.selectedExtras = [];
      this.specialInstructions = '';
      this.modalQuantity = 1;
      this.showDetails = true;
      this.$nextTick(() => {
        console.log('Modal mounted:', this.showDetails, this.selectedItem && this.selectedItem.name);
      });
    },
    closeDetails() {
      this.showDetails = false;
    },
    onEscClose(e) {
      if (e.key === 'Escape') {
        this.closeDetails();
      }
    },
    incrementModalQty() {
      this.modalQuantity += 1;
    },
    decrementModalQty() {
      if (this.modalQuantity > 1) this.modalQuantity -= 1;
    },
    selectAllIngredients() {
      if (this.selectedItem && this.selectedItem.ingredients) {
        this.selectedIngredients = [...this.selectedItem.ingredients];
      }
    },
    clearAllIngredients() {
      this.selectedIngredients = [];
    },
    addConfiguredToCart() {
      if (!this.selectedItem) return;
      const basePrice = parseFloat(this.selectedItem.price) || 0;
      const variantPrice = this.selectedVariant ? parseFloat(this.selectedVariant.price || 0) : 0;
      const extrasPrice = (this.selectedExtras || []).reduce((sum, ex) => sum + (parseFloat(ex.price || 0)), 0);
      const ingredientsPrice = (this.selectedIngredients || []).reduce((sum, ig) => sum + (parseFloat(ig.price || 0)), 0);
      const unitPrice = basePrice + variantPrice + extrasPrice + ingredientsPrice;

      const configuredKey = `${this.selectedItem.id}-${this.selectedVariant ? (this.selectedVariant.id || this.selectedVariant.name) : 'base'}-${(this.selectedExtras || []).map(e => e.id || e.name).sort().join('|')}-${(this.selectedIngredients || []).map(i => i.id || i.name).sort().join('|')}`;
      const existing = this.cartItems.find(ci => (ci.configuredKey || ci.id) === configuredKey);
      if (existing) {
        existing.quantity += this.modalQuantity;
        toast.success(`${this.selectedItem.name} quantity updated!`);
      } else {
        this.cartItems.push({
          ...this.selectedItem,
          configuredKey,
          variant: this.selectedVariant || null,
          extras: [...this.selectedExtras],
          ingredients: [...this.selectedIngredients],
          specialInstructions: this.specialInstructions,
          price: unitPrice,
          quantity: this.modalQuantity
        });
        toast.success(`${this.selectedItem.name} added to cart!`);
      }
      this.closeDetails();
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
    scrollToCart() {
      const el = this.$el.querySelector('#cart');
      if (el) {
        el.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
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
      const key = item.configuredKey || item.id;
      const index = this.cartItems.findIndex(cartItem => (cartItem.configuredKey || cartItem.id) === key);
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
  --brand: #EF9B3B;
  --brand-100: #fff3e6;
  --brand-200: #ffe0c2;
}

.main-content {
  background: linear-gradient(180deg, #fafafa, #f1efef);
  min-height: calc(100vh - 76px);
}

.menu-navbar {
  backdrop-filter: saturate(180%) blur(6px);
  background: rgba(255,255,255,0.8) !important;
  border-bottom: 1px solid #eee;
}

.search-form .form-control {
  border-radius: 9999px;
  padding-left: 16px;
}

.search-form .btn {
  border-radius: 9999px;
}

.section-title {
  padding-left: 20px;
  margin-bottom: 20px;
  color: #333;
  font-weight: 600;
}

/* Category Cards */
.category-card {
  cursor: pointer;
  transition: all 0.25s ease;
  border: 1px solid #eee;
  background: #fff;
  padding: 8px 12px;
  border-radius: 9999px;
}

.category-card:hover,
.category-card.active {
  background: var(--brand-100);
  border-color: var(--brand);
  color: var(--brand);
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(0,0,0,0.06);
}

.card-icon {
  margin-left: 10px;
}

.categories-row {
  flex-wrap: wrap;
  gap: 12px 0;
  padding: 0 16px 8px 16px;
}

/* Menu Cards */
.menu-card {
  transition: all 0.3s ease;
  border: 1px solid #ececec;
  border-radius: 16px;
  overflow: hidden;
}

.menu-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.img-container {
  background: linear-gradient(135deg, #f0f0f0 0%, #e7e7e7 100%);
  min-height: 180px;
}

.img-container img {
  width: 70% !important;
  height: auto;
  object-fit: contain;
}

.price {
  font-size: 1.1rem;
  font-weight: 700;
  color: var(--brand);
  display: inline-block;
  background: rgba(239, 155, 59, 0.1);
  padding: 6px 10px;
  border-radius: 10px;
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
  color: var(--brand);
  font-weight: 600;
}

/* Skeletons */
.skeleton {
  position: relative;
  overflow: hidden;
  background: #e9ecef;
}

.skeleton::after {
  content: "";
  position: absolute;
  inset: 0;
  transform: translateX(-100%);
  background: linear-gradient(90deg, rgba(255,255,255,0), rgba(255,255,255,0.5), rgba(255,255,255,0));
  animation: shimmer 1.25s infinite;
}

@keyframes shimmer {
  100% { transform: translateX(100%); }
}

.skeleton-chip {
  height: 44px;
  width: 160px;
  border-radius: 9999px;
}

.skeleton-img {
  height: 160px;
  border-radius: 12px;
  margin: 16px;
}

.skeleton-line {
  height: 12px;
  border-radius: 6px;
  margin: 8px 0;
}

.skeleton-btn {
  height: 36px;
  width: 110px;
  border-radius: 8px;
}

/* Modal Styles */
:deep(.modal-overlay) {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.45);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  padding: 16px;
}

:deep(.modal-dialog-custom) {
  width: 100%;
  max-width: 760px;
  background: #fff;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 20px 50px rgba(0,0,0,0.2);
  animation: modalIn 180ms ease-out;
}

@keyframes modalIn {
  from { transform: translateY(10px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}

:deep(.modal-header-custom),
:deep(.modal-footer-custom) {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 16px;
  background: #fafafa;
  border-bottom: 1px solid #eee;
}

:deep(.modal-footer-custom) {
  border-top: 1px solid #eee;
  border-bottom: 0;
}

:deep(.modal-body-custom) {
  padding: 16px;
}

:deep(.modal-img-container) {
  background: #f2f2f2;
  border-radius: 12px;
  width: 100%;
  aspect-ratio: 1 / 1;
  display: flex;
  align-items: center;
  justify-content: center;
}

:deep(.modal-img-container img) {
  max-width: 80%;
  height: auto;
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

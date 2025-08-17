<template>
  <div class="message-management">
    <div class="d-flex align-items-center mb-4">
      <i class="bi bi-envelope-fill text-warning fs-1 me-3"></i>
      <div>
        <h2 class="mb-1">
          <i class="bi bi-gear me-2"></i>Message Management
        </h2>
        <p class="text-muted mb-0">
          <i class="bi bi-chat-dots me-1"></i>Manage all customer messages
        </p>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
      <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body d-flex align-items-center">
            <div class="bg-warning bg-opacity-10 rounded-circle p-3 me-3">
              <i class="bi bi-envelope text-warning fs-3"></i>
            </div>
            <div>
              <p class="text-muted small mb-1">Total Messages</p>
              <h4 class="fw-bold mb-0">{{ stats.total }}</h4>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body d-flex align-items-center">
            <div class="bg-danger bg-opacity-10 rounded-circle p-3 me-3">
              <i class="bi bi-envelope-x text-danger fs-3"></i>
            </div>
            <div>
              <p class="text-muted small mb-1">Unread</p>
              <h4 class="fw-bold mb-0">{{ stats.unread }}</h4>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body d-flex align-items-center">
            <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3">
              <i class="bi bi-envelope-check text-success fs-3"></i>
            </div>
            <div>
              <p class="text-muted small mb-1">Read Messages</p>
              <h4 class="fw-bold mb-0">{{ stats.read }}</h4>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body d-flex align-items-center">
            <div class="bg-info bg-opacity-10 rounded-circle p-3 me-3">
              <i class="bi bi-calendar-plus text-info fs-3"></i>
            </div>
            <div>
              <p class="text-muted small mb-1">New this month</p>
              <h4 class="fw-bold mb-0">{{ stats.thisMonth }}</h4>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Messages Table -->
    <div class="card shadow-sm">
      <div class="card-header bg-warning bg-opacity-10 d-flex justify-content-between align-items-center">
        <h3 class="mb-0">
          <i class="bi bi-chat-square-text me-2 text-warning"></i>
          Message Catalog
        </h3>
        <div class="d-flex gap-2">
          <button @click="fetchMessages" class="btn btn-outline-warning" :disabled="loading">
            <i class="bi bi-arrow-clockwise me-1"></i>
            {{ loading ? 'Refreshing...' : 'Refresh' }}
          </button>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Sender</th>
                <th>Status</th>
                <th>Message Preview</th>
                <th>Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="loading" class="text-center">
                <td colspan="6" class="py-4">
                  <div class="spinner-border text-warning" role="status">
                    <span class="visually-hidden">Loading...</span>
                  </div>
                  <p class="mt-2 text-muted">Loading messages...</p>
                </td>
              </tr>
              <tr v-else-if="messages.length === 0" class="text-center">
                <td colspan="6" class="py-4">
                  <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                  <p class="mt-2 text-muted">No messages found</p>
                </td>
              </tr>
              <tr v-else v-for="(message, index) in messages" :key="message.id">
                <td>{{ index + 1 }}</td>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="bg-warning bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center fw-bold text-white me-3" style="width: 48px; height: 48px;">
                      {{ getInitials(message.name) }}
                    </div>
                    <div>
                      <p class="fw-semibold mb-0">{{ message.name }}</p>
                      <small class="text-muted">{{ message.email }}</small>
                    </div>
                  </div>
                </td>
                <td>
                  <span :class="getStatusBadge(message.status)">
                    <i :class="getStatusIcon(message.status)" class="me-1"></i>
                    {{ getStatusText(message.status) }}
                  </span>
                </td>
                <td>
                  <div>
                    <p class="fw-semibold mb-0">{{ message.subject }}</p>
                    <small class="text-muted">{{ truncateText(message.message) }}</small>
                    <br>
                    <small class="text-muted">
                      <i class="bi bi-clock me-1"></i>
                      {{ getTimeAgo(message.created_at) }}
                    </small>
                  </div>
                </td>
                <td>
                  <div>
                    <p class="fw-semibold mb-0">{{ formatDate(message.created_at) }}</p>
                    <small class="text-muted">
                      <i class="bi bi-clock me-1"></i>
                      {{ formatTime(message.created_at) }}
                    </small>
                  </div>
                </td>
                <td>
                  <div class="btn-group" role="group">
                    <button @click="viewMessage(message)" class="btn btn-sm btn-outline-primary mx-1" title="View Message">
                      <i class="bi bi-eye"></i>
                    </button>
                    <button @click="markAsRead(message.id)" v-if="!message.status" class="btn btn-sm btn-outline-success" title="Mark as Read">
                      <i class="bi bi-check"></i>
                    </button>
                    <button @click="markAsTestimonial(message.id)" v-if="message.status && !message.is_testimonial" class="btn btn-sm btn-outline-warning" title="Mark as Testimonial">
                      <i class="bi bi-star"></i>
                    </button>
                    <button @click="deleteMessage(message.id)" class="btn btn-sm btn-outline-danger mx-1" title="Delete Message">
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

    <!-- Message Detail Modal -->
    <div v-if="showMessageModal" class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              <i class="bi bi-envelope me-2"></i>
              Message Details
            </h5>
            <button @click="showMessageModal = false" type="button" class="btn-close"></button>
          </div>
          <div class="modal-body" v-if="selectedMessage">
            <!-- Sender Information -->
            <div class="row mb-4">
              <div class="col-md-8">
                <div class="d-flex align-items-center">
                  <div class="bg-warning bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center fw-bold text-white me-3" style="width: 60px; height: 60px;">
                    {{ getInitials(selectedMessage.name) }}
                  </div>
                  <div>
                    <h6 class="mb-1">{{ selectedMessage.name }}</h6>
                    <p class="text-muted mb-1">{{ selectedMessage.email }}</p>
                    <small class="text-muted">
                      <i class="bi bi-clock me-1"></i>
                      {{ formatDate(selectedMessage.created_at) }} at {{ formatTime(selectedMessage.created_at) }}
                    </small>
                  </div>
                </div>
              </div>
              <div class="col-md-4 text-end">
                <span :class="getStatusBadge(selectedMessage.status)" class="badge fs-6">
                  <i :class="getStatusIcon(selectedMessage.status)" class="me-1"></i>
                  {{ getStatusText(selectedMessage.status) }}
                </span>
              </div>
            </div>

            <!-- Subject -->
            <div class="mb-4">
              <h6 class="text-muted mb-2">Subject</h6>
              <p class="fw-semibold fs-5">{{ selectedMessage.subject }}</p>
            </div>

            <!-- Message Content -->
            <div class="mb-4">
              <h6 class="text-muted mb-2">Message</h6>
              <div class="bg-light p-3 rounded">
                <p class="mb-0" style="white-space: pre-wrap;">{{ selectedMessage.message }}</p>
              </div>
            </div>

            <!-- Message Info -->
            <div class="row">
              <div class="col-md-6">
                <small class="text-muted">
                  <i class="bi bi-calendar me-1"></i>
                  Sent: {{ formatDate(selectedMessage.created_at) }}
                </small>
              </div>
              <div class="col-md-6 text-end">
                <small class="text-muted">
                  <i class="bi bi-clock me-1"></i>
                  {{ getTimeAgo(selectedMessage.created_at) }}
                </small>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button @click="showMessageModal = false" type="button" class="btn btn-secondary">Close</button>
            <button @click="markAsRead(selectedMessage.id)" v-if="!selectedMessage?.status" type="button" class="btn btn-success">
              <i class="bi bi-check me-1"></i>
              Mark as Read
            </button>
            <button @click="markAsTestimonial(selectedMessage.id)" v-if="selectedMessage?.status && !selectedMessage?.is_testimonial" type="button" class="btn btn-warning">
              <i class="bi bi-star me-1"></i>
              Mark as Testimonial
            </button>
            <button @click="deleteMessage(selectedMessage.id)" type="button" class="btn btn-danger">
              <i class="bi bi-trash me-1"></i>
              Delete Message
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import { toast } from 'vue3-toastify'

const messages = ref([])
const loading = ref(false)
const selectedMessage = ref(null)
const showMessageModal = ref(false)

// Computed properties for stats
const stats = computed(() => {
  const total = messages.value.length
  const unread = messages.value.filter(msg => !msg.status).length
  const read = messages.value.filter(msg => msg.status).length
  const thisMonth = messages.value.filter(msg => {
    const messageDate = new Date(msg.created_at)
    const now = new Date()
    return messageDate.getMonth() === now.getMonth() &&
           messageDate.getFullYear() === now.getFullYear()
  }).length

  return { total, unread, read, thisMonth }
})

// Fetch messages from API
const fetchMessages = async () => {
  loading.value = true
  try {
    const response = await axios.get('/admin/messages')
    if (response.data.success) {
      messages.value = response.data.messages
    } else {
      toast.error('Error fetching messages')
    }
  } catch (error) {
    console.error('Error fetching messages:', error)
    toast.error('Error fetching messages')
  } finally {
    loading.value = false
  }
}

// View message details
const viewMessage = (message) => {
  selectedMessage.value = message
  showMessageModal.value = true

  // Mark as read if unread
  if (!message.status) {
    markAsRead(message.id)
  }
}

// Mark message as read
const markAsRead = async (messageId) => {
  try {
    const response = await axios.patch(`/admin/messages/${messageId}/read`)
    if (response.data.success) {
      // Update local message status
      const message = messages.value.find(msg => msg.id === messageId)
      if (message) {
        message.status = true
      }
      toast.success('Message marked as read')
    }
  } catch (error) {
    console.error('Error marking message as read:', error)
    toast.error('Error marking message as read')
  }
}

// Mark message as testimonial
const markAsTestimonial = async (messageId) => {
  try {
    const response = await axios.patch(`/admin/messages/${messageId}/testimonial`)
    if (response.data.success) {
      // Update local message status
      const message = messages.value.find(msg => msg.id === messageId)
      if (message) {
        message.is_testimonial = true
      }
      toast.success('Message marked as testimonial')
    }
  } catch (error) {
    console.error('Error marking message as testimonial:', error)
    toast.error('Error marking message as testimonial')
  }
}

// Delete message
const deleteMessage = async (messageId) => {
  if (!confirm('Are you sure you want to delete this message?')) {
    return
  }

  try {
    const response = await axios.delete(`/admin/messages/${messageId}`)
    if (response.data.success) {
      // Remove from local array
      messages.value = messages.value.filter(msg => msg.id !== messageId)
      toast.success('Message deleted successfully')
    }
  } catch (error) {
    console.error('Error deleting message:', error)
    toast.error('Error deleting message')
  }
}

// Helper functions
const getInitials = (name) => {
  return name.split(' ').map(n => n[0]).join('').toUpperCase()
}

const getStatusBadge = (status) => {
  return status ? 'badge bg-success' : 'badge bg-danger'
}

const getStatusText = (status) => {
  return status ? 'Read' : 'Unread'
}

const getStatusIcon = (status) => {
  return status ? 'bi bi-check-circle' : 'bi bi-exclamation-circle'
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('en-GB')
}

const formatTime = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleTimeString('en-US', {
    hour: '2-digit',
    minute: '2-digit',
    hour12: true
  })
}

const getTimeAgo = (dateString) => {
  const date = new Date(dateString)
  const now = new Date()
  const diffInMs = now - date
  const diffInHours = Math.floor(diffInMs / (1000 * 60 * 60))
  const diffInDays = Math.floor(diffInMs / (1000 * 60 * 60 * 24))

  if (diffInDays > 0) {
    return `${diffInDays} day${diffInDays > 1 ? 's' : ''} ago`
  } else if (diffInHours > 0) {
    return `${diffInHours} hour${diffInHours > 1 ? 's' : ''} ago`
  } else {
    return 'Just now'
  }
}

const truncateText = (text, maxLength = 50) => {
  if (text.length <= maxLength) return text
  return text.substring(0, maxLength) + '...'
}

onMounted(() => {
  fetchMessages()
})
</script>

<style scoped>
.message-management {
  padding: 20px;
}
</style>

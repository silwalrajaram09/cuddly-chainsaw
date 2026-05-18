<template>
  <div class="modal-overlay" @mousedown.self="$emit('cancel')">
    <div class="modal" style="max-width:420px">
      <div class="modal-header">
        <h3>{{ title }}</h3>
        <button class="modal-close" @click="$emit('cancel')">✕</button>
      </div>
      <div class="modal-body">
        <p style="color:var(--gray-600);font-size:15px">{{ message }}</p>
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline" @click="$emit('cancel')">Cancel</button>
        <button class="btn btn-danger" :disabled="loading" @click="$emit('confirm')">
          <span v-if="loading" class="spinner" style="width:14px;height:14px;border-width:2px"></span>
          {{ loading ? 'Deleting…' : 'Delete' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  title:   { type: String, default: 'Confirm Delete' },
  message: { type: String, default: 'Are you sure? This action cannot be undone.' },
  loading: { type: Boolean, default: false },
})
defineEmits(['confirm', 'cancel'])
</script>

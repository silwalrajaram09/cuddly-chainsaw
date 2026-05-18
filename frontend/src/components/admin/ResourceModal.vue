<template>
  <div class="modal-overlay" @mousedown.self="$emit('close')">
    <div class="modal">
      <div class="modal-header">
        <h3>{{ title }}</h3>
        <button class="modal-close" @click="$emit('close')">✕</button>
      </div>
      <form @submit.prevent="$emit('submit', form)">
        <div class="modal-body">
          <div v-if="error" class="alert alert-error">{{ error }}</div>
          <slot :form="form" />
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline" @click="$emit('close')">Cancel</button>
          <button type="submit" class="btn btn-primary" :disabled="loading">
            <span v-if="loading" class="spinner" style="width:14px;height:14px;border-width:2px"></span>
            {{ loading ? 'Saving…' : submitLabel }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, watch } from 'vue'

const props = defineProps({
  title:       { type: String, default: 'Form' },
  submitLabel: { type: String, default: 'Save' },
  initial:     { type: Object, default: () => ({}) },
  loading:     { type: Boolean, default: false },
  error:       { type: String, default: null },
})

defineEmits(['close', 'submit'])

const form = reactive({ ...props.initial })

// Sync when initial changes (editing different record)
watch(() => props.initial, (val) => {
  Object.keys(form).forEach(k => delete form[k])
  Object.assign(form, val)
}, { deep: true })
</script>

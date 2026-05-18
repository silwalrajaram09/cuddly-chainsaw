<template>
  <div>
    <div class="page-header">
      <div>
        <h1>Pages</h1>
        <p>Manage static pages shown on the public site.</p>
      </div>
      <button class="btn btn-primary" @click="openCreate">+ New Page</button>
    </div>

    <div class="card">
      <div class="card-header">
        <h2>All Pages ({{ pages.length }})</h2>
      </div>
      <div v-if="loading" class="loading-center"><div class="spinner"></div></div>
      <div v-else-if="!pages.length" class="empty-state"><p>No pages yet.</p></div>
      <div v-else class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Title</th>
              <th>Slug</th>
              <th>Updated</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(p, i) in pages" :key="p.id">
              <td>{{ i + 1 }}</td>
              <td><strong>{{ p.name }}</strong></td>
              <td><code class="slug">{{ p.slug }}</code></td>
              <td>{{ formatDate(p.updated_at) }}</td>
              <td>
                <div class="actions">
                  <button class="btn btn-outline btn-sm" @click="openEdit(p)">Edit</button>
                  <button class="btn btn-danger btn-sm"  @click="openDelete(p)">Delete</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal -->
    <ResourceModal
      v-if="showModal"
      :title="editing ? 'Edit Page' : 'New Page'"
      :submit-label="editing ? 'Update' : 'Create'"
      :initial="formInitial"
      :loading="saving"
      :error="saveError"
      @close="showModal = false"
      @submit="handleSave"
    >
      <template #default="{ form }">
        <div class="form-group">
          <label>Title <span class="req">*</span></label>
          <input v-model="form.title" class="form-control" required placeholder="About Us" />
        </div>
        <div class="form-group">
          <label>Slug <span class="req">*</span></label>
          <input v-model="form.slug" class="form-control" required placeholder="about-us" />
        </div>
        <div class="form-group">
          <label>Content</label>
          <textarea v-model="form.content" class="form-control" style="min-height:160px" placeholder="Page content (HTML or plain text)…" />
        </div>
      </template>
    </ResourceModal>

    <ConfirmDialog
      v-if="showDelete"
      :message="`Delete page '${target?.title}'?`"
      :loading="deleting"
      @confirm="handleDelete"
      @cancel="showDelete = false"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import ResourceModal from '@/components/admin/ResourceModal.vue'
import ConfirmDialog from '@/components/admin/ConfirmDialog.vue'
import { getPages, createPage, updatePage, deletePage } from '@/api/pages'

const pages       = ref([])
const loading     = ref(true)
const saving      = ref(false)
const deleting    = ref(false)
const saveError   = ref(null)
const showModal   = ref(false)
const showDelete  = ref(false)
const editing     = ref(null)
const target      = ref(null)
const formInitial = ref({})

function formatDate(d) {
  if (!d) return '—'
  return new Date(d).toLocaleDateString('en-US', { year:'numeric', month:'short', day:'numeric' })
}

async function load() {
  loading.value = true
  try {
    const { data } = await getPages()
    pages.value = data.data ?? data
  } finally { loading.value = false }
}

function openCreate() {
  editing.value     = null
  formInitial.value = { title: '', slug: '', content: '' }
  saveError.value   = null
  showModal.value   = true
}

function openEdit(p) {
  editing.value     = p
  formInitial.value = { title: p.title, slug: p.slug ?? '', content: p.content ?? '' }
  saveError.value   = null
  showModal.value   = true
}

function openDelete(p) {
  target.value     = p
  showDelete.value = true
}

async function handleSave(form) {
  saving.value    = true
  saveError.value = null
  try {
    editing.value ? await updatePage(editing.value.id, form) : await createPage(form)
    showModal.value = false
    await load()
  } catch (e) {
    saveError.value = e.response?.data?.message ?? 'Something went wrong.'
  } finally { saving.value = false }
}

async function handleDelete() {
  deleting.value = true
  try {
    await deletePage(target.value.id)
    showDelete.value = false
    await load()
  } catch (e) {
    alert(e.response?.data?.message ?? 'Delete failed.')
  } finally { deleting.value = false }
}

onMounted(load)
</script>

<style scoped>
.page-header { display:flex; align-items:flex-start; justify-content:space-between; gap:16px; margin-bottom:24px; }
.page-header h1 { font-size:24px; font-weight:700; color:var(--navy); }
.page-header p  { color:var(--gray-500); font-size:14px; margin-top:2px; }
.actions { display:flex; gap:8px; }
.slug { background:var(--gray-100); color:var(--navy); padding:2px 7px; border-radius:4px; font-size:12px; }
.req { color:var(--danger); }
</style>

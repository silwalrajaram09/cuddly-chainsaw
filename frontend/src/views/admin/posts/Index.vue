<template>
  <div>
    <div class="page-header">
      <div>
        <h1>Posts</h1>
        <p>Publish and manage news, updates and announcements.</p>
      </div>
      <button class="btn btn-primary" @click="openCreate">+ New Post</button>
    </div>

    <div class="card">
      <div class="card-header">
        <h2>All Posts ({{ posts.length }})</h2>
        <input
          v-model="search"
          class="form-control"
          style="width:220px;padding:6px 10px;font-size:13px"
          placeholder="Search posts…"
        />
      </div>
      <div v-if="loading" class="loading-center"><div class="spinner"></div></div>
      <div v-else-if="!filtered.length" class="empty-state"><p>No posts found.</p></div>
      <div v-else class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Title</th>
              <th>Excerpt</th>
              <th>Published</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(p, i) in filtered" :key="p.id">
              <td>{{ i + 1 }}</td>
              <td><strong>{{ p.title }}</strong></td>
              <td>{{ truncate(p.excerpt ?? p.content, 72) }}</td>
              <td>{{ formatDate(p.created_at) }}</td>
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
      :title="editing ? 'Edit Post' : 'New Post'"
      :submit-label="editing ? 'Update' : 'Publish'"
      :initial="formInitial"
      :loading="saving"
      :error="saveError"
      @close="showModal = false"
      @submit="handleSave"
    >
      <template #default="{ form }">
        <div class="form-group">
          <label>Title <span class="req">*</span></label>
          <input v-model="form.title" class="form-control" required placeholder="Post title…" />
        </div>
        <div class="form-group">
          <label>Excerpt</label>
          <input v-model="form.excerpt" class="form-control" placeholder="Short summary shown in listings…" />
        </div>
        <div class="form-group">
          <label>Content <span class="req">*</span></label>
          <textarea v-model="form.content" class="form-control" style="min-height:180px" required placeholder="Full post content…" />
        </div>
      </template>
    </ResourceModal>

    <ConfirmDialog
      v-if="showDelete"
      :message="`Delete post '${target?.title}'?`"
      :loading="deleting"
      @confirm="handleDelete"
      @cancel="showDelete = false"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import ResourceModal from '@/components/admin/ResourceModal.vue'
import ConfirmDialog from '@/components/admin/ConfirmDialog.vue'
import { getPosts, createPost, updatePost, deletePost } from '@/api/posts'

const posts       = ref([])
const loading     = ref(true)
const saving      = ref(false)
const deleting    = ref(false)
const saveError   = ref(null)
const showModal   = ref(false)
const showDelete  = ref(false)
const editing     = ref(null)
const target      = ref(null)
const formInitial = ref({})
const search      = ref('')

const filtered = computed(() => {
  if (!search.value) return posts.value
  const q = search.value.toLowerCase()
  return posts.value.filter(p => p.title?.toLowerCase().includes(q))
})

function truncate(s, l) { return !s ? '' : s.length > l ? s.slice(0,l)+'…' : s }
function formatDate(d) {
  if (!d) return '—'
  return new Date(d).toLocaleDateString('en-US', { year:'numeric', month:'short', day:'numeric' })
}

async function load() {
  loading.value = true
  try {
    const { data } = await getPosts()
    posts.value = data.data ?? data
  } finally { loading.value = false }
}

function openCreate() {
  editing.value     = null
  formInitial.value = { title: '', excerpt: '', content: '' }
  saveError.value   = null
  showModal.value   = true
}

function openEdit(p) {
  editing.value     = p
  formInitial.value = { title: p.title, excerpt: p.excerpt ?? '', content: p.content ?? '' }
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
    editing.value ? await updatePost(editing.value.id, form) : await createPost(form)
    showModal.value = false
    await load()
  } catch (e) {
    saveError.value = e.response?.data?.message ?? 'Something went wrong.'
  } finally { saving.value = false }
}

async function handleDelete() {
  deleting.value = true
  try {
    await deletePost(target.value.id)
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
.card-header { align-items:center; gap:12px; }
.actions { display:flex; gap:8px; }
.req { color:var(--danger); }
</style>

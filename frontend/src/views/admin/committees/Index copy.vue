<template>
  <div>
    <!-- Header -->
    <div class="page-header">
      <div>
        <h1>Committees</h1>
        <p>Manage hierarchical committees — parent, child, and sub-child levels.</p>
      </div>
      <button class="btn btn-primary" @click="openCreate(null)">+ New Committee</button>
    </div>

    <!-- Legend -->
    <div class="legend card mb-6">
      <div class="card-body legend-inner">
        <span class="legend-item"><span class="dot dot-root"></span> Root</span>
        <span class="legend-item"><span class="dot dot-child"></span> Child</span>
        <span class="legend-item"><span class="dot dot-sub"></span> Sub-child</span>
        <span class="legend-item ml-auto text-sm text-gray">{{ totalCount }} total committees</span>
      </div>
    </div>

    <!-- Tree -->
    <div v-if="loading" class="loading-center"><div class="spinner"></div></div>
    <div v-else-if="!committees.length" class="empty-state card">
      <div class="card-body"><p>No committees yet. Create one to get started.</p></div>
    </div>
    <div v-else class="tree-root">
      <CommitteeNode
        v-for="node in committees"
        :key="node.id"
        :node="node"
        :depth="0"
        @create-child="openCreate"
        @edit="openEdit"
        @delete="openDelete"
      />
    </div>

    <!-- Create / Edit Modal -->
    <div class="modal-overlay" v-if="showModal" @mousedown.self="showModal = false">
      <div class="modal">
        <div class="modal-header">
          <h3>
            {{ editing
              ? 'Edit Committee'
              : parentTarget
                ? `Add Child to "${parentTarget.name}"`
                : 'New Root Committee' }}
          </h3>
          <button class="modal-close" @click="showModal = false">✕</button>
        </div>
        <form @submit.prevent="handleSave">
          <div class="modal-body">
            <div v-if="saveError" class="alert alert-error">{{ saveError }}</div>

            <!-- Parent badge (create child mode) -->
            <div v-if="parentTarget && !editing" class="parent-badge">
              <span class="parent-badge-label">Parent</span>
              <span class="parent-badge-name">{{ parentTarget.name }}</span>
            </div>

            <!-- Parent selector (edit mode) -->
            <div class="form-group" v-if="editing">
              <label>Parent Committee</label>
              <select v-model="form.parent_id" class="form-control">
                <option :value="null">— None (Root) —</option>
                <option
                  v-for="flat in flatList.filter(c => c.id !== editing.id)"
                  :key="flat.id"
                  :value="flat.id"
                >{{ flat._indent }}{{ flat.name }}</option>
              </select>
            </div>

            <div class="form-group">
              <label>Committee Name <span class="req">*</span></label>
              <input
                v-model="form.name"
                class="form-control"
                required
                placeholder="e.g. Finance, Bagmati, IT…"
              />
            </div>
            <div class="form-group">
              <label>Description</label>
              <textarea v-model="form.description" class="form-control" placeholder="Optional description…" />
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline" @click="showModal = false">Cancel</button>
            <button type="submit" class="btn btn-primary" :disabled="saving">
              <span v-if="saving" class="spinner" style="width:14px;height:14px;border-width:2px"></span>
              {{ saving ? 'Saving…' : editing ? 'Update' : 'Create' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Delete Confirm -->
    <div class="modal-overlay" v-if="showDelete" @mousedown.self="showDelete = false">
      <div class="modal" style="max-width:420px">
        <div class="modal-header">
          <h3>Delete Committee</h3>
          <button class="modal-close" @click="showDelete = false">✕</button>
        </div>
        <div class="modal-body">
          <div class="alert alert-error" v-if="deleteTarget?.children?.length">
            ⚠️ This committee has <strong>{{ deleteTarget.children.length }} child(ren)</strong>.
            Deleting it may also remove all nested children.
          </div>
          <p style="color:var(--gray-600);font-size:15px">
            Delete <strong>"{{ deleteTarget?.name }}"</strong>? This cannot be undone.
          </p>
        </div>
        <div class="modal-footer">
          <button class="btn btn-outline" @click="showDelete = false">Cancel</button>
          <button class="btn btn-danger" :disabled="deleting" @click="handleDelete">
            <span v-if="deleting" class="spinner" style="width:14px;height:14px;border-width:2px"></span>
            {{ deleting ? 'Deleting…' : 'Delete' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import CommitteeNode from './CommitteeNode.vue'
import { getCommittees, createCommittee, updateCommittee, deleteCommittee } from '@/api/committees'

const committees   = ref([])
const loading      = ref(true)
const saving       = ref(false)
const deleting     = ref(false)
const saveError    = ref(null)
const showModal    = ref(false)
const showDelete   = ref(false)
const editing      = ref(null)
const parentTarget = ref(null)
const deleteTarget = ref(null)

const form = ref({ name: '', description: '', parent_id: null })

// Build a flat list from the nested tree for the parent selector dropdown
const flatList = computed(() => {
  const result = []
  function walk(nodes, depth) {
    for (const n of nodes) {
      result.push({ ...n, _indent: '— '.repeat(depth) })
      if (n.children?.length) walk(n.children, depth + 1)
    }
  }
  walk(committees.value, 0)
  return result
})

const totalCount = computed(() => flatList.value.length)

async function load() {
  loading.value = true
  try {
    const { data } = await getCommittees()
    committees.value = data.data ?? data
  } finally { loading.value = false }
}

function openCreate(parentNode) {
  editing.value      = null
  parentTarget.value = parentNode
  form.value         = { name: '', description: '', parent_id: parentNode?.id ?? null }
  saveError.value    = null
  showModal.value    = true
}

function openEdit(node) {
  editing.value      = node
  parentTarget.value = null
  form.value         = { name: node.name, description: node.description ?? '', parent_id: node.parent_id ?? null }
  saveError.value    = null
  showModal.value    = true
}

function openDelete(node) {
  deleteTarget.value = node
  showDelete.value   = true
}

async function handleSave() {
  saving.value    = true
  saveError.value = null
  try {
    const payload = {
      name:        form.value.name,
      description: form.value.description || undefined,
      parent_id:   form.value.parent_id   || null,
    }
    editing.value
      ? await updateCommittee(editing.value.id, payload)
      : await createCommittee(payload)
    showModal.value = false
    await load()
  } catch (e) {
    saveError.value = e.response?.data?.message ?? 'Something went wrong.'
  } finally { saving.value = false }
}

async function handleDelete() {
  deleting.value = true
  try {
    await deleteCommittee(deleteTarget.value.id)
    showDelete.value = false
    await load()
  } catch (e) {
    alert(e.response?.data?.message ?? 'Delete failed.')
  } finally { deleting.value = false }
}

onMounted(load)
</script>

<style scoped>
.page-header {
  display: flex; align-items: flex-start;
  justify-content: space-between; gap: 16px;
  margin-bottom: 24px;
}
.page-header h1 { font-size: 24px; font-weight: 700; color: var(--navy); }
.page-header p  { color: var(--gray-500); font-size: 14px; margin-top: 2px; }

.legend-inner {
  display: flex; align-items: center;
  gap: 20px; flex-wrap: wrap;
  padding: 10px 20px;
}
.legend-item { display: flex; align-items: center; gap: 7px; font-size: 13px; color: var(--gray-600); }
.dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
.dot-root  { background: var(--navy); }
.dot-child { background: var(--gold); }
.dot-sub   { background: #94a3b8; }
.ml-auto   { margin-left: auto; }

.tree-root { display: flex; flex-direction: column; gap: 8px; }

.parent-badge {
  display: flex; align-items: center; gap: 10px;
  background: #eef3ff; border: 1px solid #c7d7fa;
  border-radius: var(--radius); padding: 10px 14px;
  margin-bottom: 16px;
}
.parent-badge-label {
  font-size: 11px; font-weight: 700; letter-spacing: .06em;
  text-transform: uppercase; color: #4361b5;
  background: #dce8ff; padding: 2px 8px; border-radius: 99px;
}
.parent-badge-name { font-size: 14px; font-weight: 600; color: var(--navy); }

.req { color: var(--danger); }
</style>

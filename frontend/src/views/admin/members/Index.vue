<template>
  <div>
    <div class="page-header">
      <div>
        <h1>Members</h1>
        <p>Manage all members, assign programs and committees.</p>
      </div>
      <button class="btn btn-primary" @click="openCreate">+ New Member</button>
    </div>

    <div class="card">
      <div class="card-header">
        <h2>All Members ({{ members.length }})</h2>
        <input
          v-model="search"
          class="form-control"
          style="width:220px;padding:6px 10px;font-size:13px"
          placeholder="Search by name…"
        />
      </div>
      <div v-if="loading" class="loading-center"><div class="spinner"></div></div>
      <div v-else-if="!filtered.length" class="empty-state"><p>No members found.</p></div>
      <div v-else class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Name</th>
              <th>Email</th>
              <th>Programs</th>
              <th>Committees</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(m, i) in filtered" :key="m.id">
              <td>{{ i + 1 }}</td>
              <td>
                <div class="member-cell">
                  <div class="avatar-sm">{{ initials(m.full_name) }}</div>
                  <strong>{{ m.full_name }}</strong>
                </div>
              </td>
              <td>{{ m.email }}</td>
              <td>
                <span class="badge badge-gold">{{ m.programs_count ?? m.programs?.length ?? '—' }}</span>
              </td>
              <td>
                <span class="badge badge-blue">{{ m.committees_count ?? m.committees?.length ?? '—' }}</span>
              </td>
              <td>
                <div class="actions">
                  <RouterLink :to="`/admin/members/${m.id}`" class="btn btn-outline btn-sm">Assign</RouterLink>
                  <button class="btn btn-outline btn-sm" @click="openEdit(m)">Edit</button>
                  <button class="btn btn-danger btn-sm"  @click="openDelete(m)">Delete</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Create / Edit Modal -->
    <ResourceModal
      v-if="showModal"
      :title="editing ? 'Edit Member' : 'New Member'"
      :submit-label="editing ? 'Update' : 'Create'"
      :initial="formInitial"
      :loading="saving"
      :error="saveError"
      @close="showModal = false"
      @submit="handleSave"
    >
      <template #default="{ form }">
        <div class="form-group">
          <label>Full Name <span class="req">*</span></label>
          <input v-model="form.name" class="form-control" required placeholder="e.g. Aarav Sharma" />
        </div>
        <div class="form-group">
          <label>Email <span class="req">*</span></label>
          <input v-model="form.email" type="email" class="form-control" required placeholder="aarav@example.com" />
        </div>
        <div class="form-group" v-if="!editing">
          <label>Password <span class="req">*</span></label>
          <input v-model="form.password" type="password" class="form-control" required placeholder="Min. 8 characters" />
        </div>
        <div class="form-group" v-if="!editing">
          <label>Confirm Password <span class="req">*</span></label>
          <input v-model="form.password_confirmation" type="password" class="form-control" required />
        </div>
      </template>
    </ResourceModal>

    <ConfirmDialog
      v-if="showDelete"
      :message="`Delete member '${target?.name}'? This cannot be undone.`"
      :loading="deleting"
      @confirm="handleDelete"
      @cancel="showDelete = false"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import ResourceModal from '@/components/admin/ResourceModal.vue'
import ConfirmDialog from '@/components/admin/ConfirmDialog.vue'
import { getMembers, createMember, updateMember, deleteMember } from '@/api/members'

const members     = ref([])
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
  if (!search.value) return members.value
  const q = search.value.toLowerCase()
  return members.value.filter(m =>
    m.name?.toLowerCase().includes(q) || m.email?.toLowerCase().includes(q)
  )
})

function initials(name) {
  if (!name) return '?'
  return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
}

async function load() {
  loading.value = true
  try {
    const { data } = await getMembers()
    members.value = data.data ?? data
  } finally { loading.value = false }
}

function openCreate() {
  editing.value     = null
  formInitial.value = { name: '', email: '', password: '', password_confirmation: '' }
  saveError.value   = null
  showModal.value   = true
}

function openEdit(m) {
  editing.value     = m
  formInitial.value = { name: m.name, email: m.email }
  saveError.value   = null
  showModal.value   = true
}

function openDelete(m) {
  target.value     = m
  showDelete.value = true
}

async function handleSave(form) {
  saving.value    = true
  saveError.value = null
  try {
    if (editing.value) {
      await updateMember(editing.value.id, form)
    } else {
      await createMember(form)
    }
    showModal.value = false
    await load()
  } catch (e) {
    const errors = e.response?.data?.errors
    if (errors) {
      saveError.value = Object.values(errors).flat().join(' ')
    } else {
      saveError.value = e.response?.data?.message ?? 'Something went wrong.'
    }
  } finally { saving.value = false }
}

async function handleDelete() {
  deleting.value = true
  try {
    await deleteMember(target.value.id)
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
.card-header    { align-items: center; gap: 12px; }
.actions { display: flex; gap: 6px; flex-wrap: wrap; }
.member-cell { display: flex; align-items: center; gap: 10px; }
.avatar-sm {
  width: 32px; height: 32px; border-radius: 50%;
  background: var(--navy); color: var(--white);
  display: flex; align-items: center; justify-content: center;
  font-size: 12px; font-weight: 600; flex-shrink: 0;
}
.req { color: var(--danger); }
</style>

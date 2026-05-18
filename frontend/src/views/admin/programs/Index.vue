<template>
  <div>
    <div class="page-header">
      <div>
        <h1>Programs</h1>
        <p>Manage all investment programs.</p>
      </div>
      <button class="btn btn-primary" @click="openCreate">+ New Program</button>
    </div>

    <div class="card">
      <div class="card-header">
        <h2>All Programs ({{ programs.length }})</h2>
      </div>
      <div v-if="loading" class="loading-center">
        <div class="spinner"></div>
      </div>
      <div v-else-if="!programs.length" class="empty-state">
        <p>No programs yet. Create one to get started.</p>
      </div>
      <div v-else class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Name</th>
              <th>Description</th>
              <th>Members</th>
               <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(p, i) in programs" :key="p.id">
              <td>{{ i + 1 }}</td>
              <td>
                <strong>{{ p.name }}</strong>
              </td>
              <td>{{ truncate(p.description, 80) }}</td>
              
              <td>
                {{ p.members_count }}
              </td>
              <td>
                {{ p.status }}
              </td>
              <td>
                <div class="actions">
                  <button class="btn btn-outline btn-sm" @click="openEdit(p)">
                    Edit
                  </button>
                  <button class="btn btn-danger btn-sm" @click="openDelete(p)">
                    Delete
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <ResourceModal
      v-if="showModal"
      :title="editing ? 'Edit Program' : 'New Program'"
      :submit-label="editing ? 'Update' : 'Create'"
      :initial="formInitial"
      :loading="saving"
      :error="saveError"
      @close="showModal = false"
      @submit="handleSave"
    >
      <template #default="{ form }">
        <div class="form-group">
          <label>Name <span class="req">*</span></label>
          <input
            v-model="form.name"
            class="form-control"
            required
            placeholder="e.g. Growth Fund Alpha"
          />
        </div>
        <div class="form-group">
          <label>Description</label>
          <textarea
            v-model="form.description"
            class="form-control"
            placeholder="Program overview..."
          />
        </div>
      </template>
    </ResourceModal>

    <ConfirmDialog
      v-if="showDelete"
      :message="`Delete program '${target?.name}'? This cannot be undone.`"
      :loading="deleting"
      @confirm="handleDelete"
      @cancel="showDelete = false"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import ResourceModal from "@/components/admin/ResourceModal.vue";
import ConfirmDialog from "@/components/admin/ConfirmDialog.vue";
import {
  getPrograms,
  createProgram,
  updateProgram,
  deleteProgram,
} from "@/api/programs";

const programs = ref([]);
const loading = ref(true);
const saving = ref(false);
const deleting = ref(false);
const saveError = ref(null);
const showModal = ref(false);
const showDelete = ref(false);
const editing = ref(null);
const target = ref(null);
const formInitial = ref({});

function truncate(s, l) {
  return !s ? "" : s.length > l ? s.slice(0, l) + "…" : s;
}

async function load() {
  loading.value = true;
  try {
    const { data } = await getPrograms();
    programs.value = data.data ?? data;
  } finally {
    loading.value = false;
  }
}

function openCreate() {
  editing.value = null;
  formInitial.value = { name: "", description: "" };
  saveError.value = null;
  showModal.value = true;
}

function openEdit(p) {
  editing.value = p;
  formInitial.value = { name: p.name, description: p.description ?? "" };
  saveError.value = null;
  showModal.value = true;
}

function openDelete(p) {
  target.value = p;
  showDelete.value = true;
}

async function handleSave(form) {
  saving.value = true;
  saveError.value = null;
  try {
    if (editing.value) {
      await updateProgram(editing.value.id, form);
    } else {
      await createProgram(form);
    }
    showModal.value = false;
    await load();
  } catch (e) {
    saveError.value = e.response?.data?.message ?? "Something went wrong.";
  } finally {
    saving.value = false;
  }
}

async function handleDelete() {
  deleting.value = true;
  try {
    await deleteProgram(target.value.id);
    showDelete.value = false;
    await load();
  } catch (e) {
    alert(e.response?.data?.message ?? "Delete failed.");
  } finally {
    deleting.value = false;
  }
}

onMounted(load);
</script>

<style scoped>
.page-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 24px;
}
.page-header h1 {
  font-size: 24px;
  font-weight: 700;
  color: var(--navy);
}
.page-header p {
  color: var(--gray-500);
  font-size: 14px;
  margin-top: 2px;
}
.actions {
  display: flex;
  gap: 8px;
}
.req {
  color: var(--danger);
}
</style>

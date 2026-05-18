<template>
  <div>
    <!-- Header -->
    <div class="page-header">
      <div>
        <h1>Committees Management</h1>
        <p>Organize your committee structure with hierarchical relationships</p>
      </div>
      <button class="btn btn-primary" @click="openCreate(null)">
        <svg
          width="16"
          height="16"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
        >
          <path d="M12 5v14M5 12h14" />
        </svg>
        New Committee
      </button>
    </div>


    <div v-if="loading" class="loading-center">
      <div class="spinner"></div>
      <p>Loading committees...</p>
    </div>

    <!-- Empty -->
    <div v-else-if="!filteredCommittees.length" class="empty-state">
      <div class="empty-icon"></div>
      <h3>No Committees Found</h3>
      <p>
        {{
          searchQuery
            ? "No matching committees found."
            : "Get started by creating your first committee."
        }}
      </p>
      <button
        v-if="!searchQuery"
        class="btn btn-primary"
        @click="openCreate(null)"
      >
        Create First Committee
      </button>
    </div>

    <!-- Tree -->
    <div v-else class="tree-container">
      <CommitteeNode
        v-for="node in filteredCommittees"
        :key="node.id"
        :node="node"
        :depth="0"
        @create-child="openCreate"
        @edit="openEdit"
        @delete="openDelete"
      />
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal">
        <div class="modal-header">
          <h3>
            {{
              editing
                ? "Edit Committee"
                : parentTarget
                  ? `Add Child to "${parentTarget.name}"`
                  : "Create Root Committee"
            }}
          </h3>
          <button class="modal-close" @click="closeModal">✕</button>
        </div>

        <form @submit.prevent="handleSave">
          <div class="modal-body">
            <div v-if="error" class="alert alert-error">
              {{ error }}
            </div>

            <div class="form-group">
              <label>Committee Name <span class="required">*</span></label>
              <input
                v-model="form.name"
                class="form-control"
                :class="{ 'is-invalid': errors.name }"
                required
                autofocus
                placeholder="e.g., Finance Committee, Audit Board"
              />
              <span v-if="errors.name" class="error-text">{{
                errors.name
              }}</span>
            </div>

            <div class="form-group">
              <label>Description</label>
              <textarea
                v-model="form.description"
                class="form-control"
                rows="3"
                placeholder="Optional description of the committee's purpose"
              />
            </div>

            <div class="form-group" v-if="editing">
              <label>Parent Committee</label>
              <select v-model="form.parent_id" class="form-control">
                <option :value="null">— None (Root Level) —</option>
                <option
                  v-for="committee in availableParents"
                  :key="committee.id"
                  :value="committee.id"
                  :disabled="committee.id === editing?.id"
                >
                  {{ "— ".repeat(committee.depth || 0) }}{{ committee.name }}
                </option>
              </select>
              <small class="form-hint"
                >Moving a committee will also move all its children</small
              >
            </div>

            <div v-if="parentTarget && !editing" class="parent-info">
              <span class="parent-badge">Parent</span>
              <span class="parent-name">{{ parentTarget.name }}</span>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-outline" @click="closeModal">
              Cancel
            </button>
            <button type="submit" class="btn btn-primary" :disabled="saving">
              <span v-if="saving" class="spinner small"></span>
              {{
                saving
                  ? "Saving..."
                  : editing
                    ? "Update Committee"
                    : "Create Committee"
              }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div
      v-if="showDelete"
      class="modal-overlay"
      @click.self="showDelete = false"
    >
      <div class="modal modal-sm">
        <div class="modal-header">
          <h3>Delete Committee</h3>
          <button class="modal-close" @click="showDelete = false">✕</button>
        </div>

        <div class="modal-body">
          <div
            v-if="deleteTarget?.children?.length"
            class="alert alert-warning"
          >
            <strong>!!Warning:</strong> This committee has
            <strong
              >{{ deleteTarget.children.length }} child subcommittee(s)</strong
            >. Deleting it will also remove all nested subcommittees and member
            associations.
          </div>

          <p>
            Are you sure you want to delete
            <strong>"{{ deleteTarget?.name }}"</strong>?
          </p>
          <p class="delete-warning">This action cannot be undone.</p>
        </div>

        <div class="modal-footer">
          <button class="btn btn-outline" @click="showDelete = false">
            Cancel
          </button>
          <button
            class="btn btn-danger"
            :disabled="deleting"
            @click="handleDelete"
          >
            <span v-if="deleting" class="spinner small"></span>
            {{ deleting ? "Deleting..." : "Delete Permanently" }}
          </button>
        </div>
      </div>
    </div>

    <!-- Toast Notification -->
    <Transition name="toast">
      <div v-if="toast.message" class="toast" :class="toast.type">
        {{ toast.message }}
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import CommitteeNode from "./CommitteeNode.vue";
import {
  getCommittees,
  createCommittee,
  updateCommittee,
  deleteCommittee,
} from "@/api/committees";

// State
const committees = ref([]);
const loading = ref(true);
const saving = ref(false);
const deleting = ref(false);
const showModal = ref(false);
const showDelete = ref(false);
const editing = ref(null);
const parentTarget = ref(null);
const deleteTarget = ref(null);
const searchQuery = ref("");
const error = ref(null);
const errors = ref({});
const toast = ref({ message: "", type: "" });

const form = ref({
  name: "",
  description: "",
  parent_id: null,
});

// Computed
const totalCommittees = computed(() => {
  const count = (nodes) => {
    let total = nodes.length;
    for (const node of nodes) {
      if (node.children) {
        total += count(node.children);
      }
    }
    return total;
  };
  return count(committees.value);
});

const rootCommittees = computed(() => committees.value.length);

const filteredCommittees = computed(() => {
  if (!searchQuery.value) return committees.value;

  const query = searchQuery.value.toLowerCase();

  const filterTree = (nodes) => {
    return nodes
      .map((node) => {
        const matches = node.name.toLowerCase().includes(query);
        const filteredChildren = node.children ? filterTree(node.children) : [];

        if (matches || filteredChildren.length) {
          return { ...node, children: filteredChildren };
        }
        return null;
      })
      .filter(Boolean);
  };

  return filterTree(committees.value);
});

const availableParents = computed(() => {
  const flatten = (nodes, depth = 0, arr = []) => {
    for (const node of nodes) {
      arr.push({ ...node, depth });
      if (node.children) {
        flatten(node.children, depth + 1, arr);
      }
    }
    return arr;
  };
  return flatten(committees.value);
});

// Helper Functions
function showToast(message, type = "success") {
  toast.value = { message, type };
  setTimeout(() => {
    toast.value = { message: "", type: "" };
  }, 3000);
}

function validateForm() {
  errors.value = {};
  if (!form.value.name.trim()) {
    errors.value.name = "Committee name is required";
    return false;
  }
  return true;
}

// API Calls
async function load() {
  loading.value = true;
  try {
    const { data } = await getCommittees();
    committees.value = data.data ?? data ?? [];
  } catch (err) {
    console.error("Failed to load committees:", err);
    showToast("Failed to load committees", "error");
    committees.value = [];
  } finally {
    loading.value = false;
  }
}

// Modal Actions
function openCreate(parentNode) {
  editing.value = null;
  parentTarget.value = parentNode;
  form.value = {
    name: "",
    description: "",
    parent_id: parentNode?.id ?? null,
  };
  errors.value = {};
  error.value = null;
  showModal.value = true;
}

function openEdit(node) {
  editing.value = node;
  parentTarget.value = null;
  form.value = {
    name: node.name,
    description: node.description || "",
    parent_id: node.parent_id ?? null,
  };
  errors.value = {};
  error.value = null;
  showModal.value = true;
}

function openDelete(node) {
  deleteTarget.value = node;
  showDelete.value = true;
}

function closeModal() {
  showModal.value = false;
  editing.value = null;
  parentTarget.value = null;
  form.value = { name: "", description: "", parent_id: null };
  errors.value = {};
  error.value = null;
}

// Save Handler
async function handleSave() {
  if (!validateForm()) return;

  saving.value = true;
  error.value = null;

  try {
    const payload = {
      name: form.value.name,
      description: form.value.description || undefined,
      parent_id: form.value.parent_id || null,
    };

    if (editing.value) {
      await updateCommittee(editing.value.id, payload);
      showToast("Committee updated successfully");
    } else {
      await createCommittee(payload);
      showToast("Committee created successfully");
    }

    closeModal();
    await load();
  } catch (err) {
    console.error("Save failed:", err);
    error.value = err.response?.data?.message || "Failed to save committee";
    showToast(error.value, "error");
  } finally {
    saving.value = false;
  }
}

// Delete Handler
async function handleDelete() {
  deleting.value = true;

  try {
    await deleteCommittee(deleteTarget.value.id);
    showDelete.value = false;
    showToast("Committee deleted successfully");
    await load();
  } catch (err) {
    console.error("Delete failed:", err);
    showToast(
      err.response?.data?.message || "Failed to delete committee",
      "error",
    );
  } finally {
    deleting.value = false;
  }
}

onMounted(load);
</script>

<style scoped>
/* Layout */
.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.page-header h1 {
  margin: 0;
  font-size: 24px;
  font-weight: 600;
}

.page-header p {
  margin-top: 4px;
  color: #666;
  font-size: 14px;
}

/* Stats */
.stats {
  display: flex;
  gap: 12px;
  margin-bottom: 20px;
}

.stat-card {
  flex: 1;
  background: #fff;
  border: 1px solid #e5e5e5;
  padding: 16px;
  border-radius: 8px;
}

.stat-value {
  display: block;
  font-size: 24px;
  font-weight: bold;
}

.stat-label {
  font-size: 13px;
  color: #666;
}

/* Search */
.search-bar {
  margin-bottom: 20px;
}

.search-input {
  width: 100%;
  max-width: 350px;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 6px;
}

/* Tree */
.tree-container {
  background: white;
  border: 1px solid #e5e5e5;
  border-radius: 8px;
  padding: 16px;
}

/* Buttons */
.btn {
  padding: 8px 14px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
}

.btn-primary {
  background: #1e3a5f;
  color: white;
}

.btn-outline {
  background: white;
  border: 1px solid #ccc;
}

.btn-danger {
  background: #dc2626;
  color: white;
}

.btn:disabled {
  opacity: 0.6;
}

/* Modal */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.4);
  display: flex;
  align-items: center;
  justify-content: center;
}

.modal {
  width: 100%;
  max-width: 450px;
  background: white;
  border-radius: 8px;
  overflow: hidden;
}

.modal-sm {
  max-width: 380px;
}

.modal-header,
.modal-footer {
  padding: 16px;
  border-bottom: 1px solid #eee;
}

.modal-footer {
  border-top: 1px solid #eee;
  border-bottom: none;
  display: flex;
  justify-content: flex-end;
  gap: 10px;
}

.modal-body {
  padding: 16px;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-close {
  border: none;
  background: none;
  font-size: 20px;
  cursor: pointer;
}

/* Forms */
.form-group {
  margin-bottom: 16px;
}

.form-group label {
  display: block;
  margin-bottom: 6px;
  font-size: 14px;
  font-weight: 500;
}

.form-control {
  width: 100%;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 6px;
}

textarea.form-control {
  resize: vertical;
}

.error-text {
  font-size: 12px;
  color: red;
}

.required {
  color: red;
}

/* Alerts */
.alert {
  padding: 10px;
  border-radius: 6px;
  margin-bottom: 16px;
  font-size: 14px;
}

.alert-error {
  background: #fee2e2;
  color: #b91c1c;
}

.alert-warning {
  background: #fef3c7;
  color: #92400e;
}

/* Empty */
.empty-state {
  text-align: center;
  padding: 40px;
  border: 1px dashed #ddd;
  border-radius: 8px;
  background: white;
}

.empty-icon {
  font-size: 40px;
  margin-bottom: 10px;
}

/* Loading */
.loading-center {
  text-align: center;
  padding: 40px;
}

.spinner {
  width: 28px;
  height: 28px;
  border: 3px solid #ddd;
  border-top-color: #1e3a5f;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  margin: auto;
}

.spinner.small {
  width: 14px;
  height: 14px;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

/* Toast */
.toast {
  position: fixed;
  bottom: 20px;
  right: 20px;
  background: #111827;
  color: white;
  padding: 10px 16px;
  border-radius: 6px;
}

.toast.success {
  background: #16a34a;
}

.toast.error {
  background: #dc2626;
}

/* Mobile */
@media (max-width: 768px) {
  .page-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
  }

  .stats {
    flex-direction: column;
  }

  .modal {
    margin: 16px;
  }
}
</style>

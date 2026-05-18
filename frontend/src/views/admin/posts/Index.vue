<template>
  <div>
    <!-- Header -->
    <div class="page-header">
      <div>
        <h1>Posts</h1>
        <p>Publish and manage news, updates and announcements.</p>
      </div>
      <button class="btn btn-primary" @click="openCreate">+ New Post</button>
    </div>

    <!-- Table -->
    <div class="card">
      <div class="card-header">
        <h2>All Posts ({{ filtered.length }})</h2>
        <input
          v-model="search"
          class="form-control"
          style="width: 250px"
          placeholder="Search posts..."
        />
      </div>

      <div v-if="loading" class="loading-center">
        <div class="spinner"></div>
      </div>

      <div v-else-if="!filtered.length" class="empty-state">
        <p>No posts found.</p>
      </div>

      <div v-else class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Title</th>
              <th>Description</th>
              <th>Image</th>
              <th>Date</th>
              <th style="width: 120px">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(post, index) in filtered" :key="post.id">
              <td>{{ index + 1 }}</td>
              <td>
                <strong>{{ post.name }}</strong>
              </td>
              <td>{{ truncate(post.description, 60) }}</td>
              <td>
                <img
                  v-if="post.photo_url"
                  :src="post.photo_url"
                  style="
                    width: 40px;
                    height: 40px;
                    object-fit: cover;
                    border-radius: 6px;
                  "
                />
                <span v-else class="text-gray">No image</span>
              </td>
              <td>{{ formatDate(post.created_at) }}</td>
              <td>
                <div class="actions">
                  <button
                    class="btn btn-outline btn-sm"
                    @click="openEdit(post)"
                  >
                    Edit
                  </button>
                  <button
                    class="btn btn-danger btn-sm"
                    @click="openDelete(post)"
                  >
                    Delete
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal">
        <div class="modal-header">
          <h3>{{ editing ? "Edit Post" : "Create Post" }}</h3>
          <button class="modal-close" @click="closeModal">✕</button>
        </div>

        <form @submit.prevent="handleSave">
          <div class="modal-body">
            <div class="form-group">
              <label>Title <span class="req">*</span></label>
              <input
                v-model="form.name"
                class="form-control"
                required
                placeholder="Enter post title"
              />
            </div>

            <div class="form-group">
              <label>Description <span class="req">*</span></label>
              <textarea
                v-model="form.description"
                class="form-control"
                rows="5"
                required
                placeholder="Enter post content"
              />
            </div>

            <div class="form-group">
              <label>Image</label>
              <input
                type="file"
                accept="image/*"
                @change="handleFile"
                class="form-control"
                style="padding: 6px"
              />
              <div v-if="preview" class="preview">
                <img :src="preview" />
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-outline" @click="closeModal">
              Cancel
            </button>
            <button type="submit" class="btn btn-primary" :disabled="saving">
              {{ saving ? "Saving..." : "Save" }}
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
      <div class="modal" style="max-width: 400px">
        <div class="modal-header">
          <h3>Delete Post</h3>
          <button class="modal-close" @click="showDelete = false">✕</button>
        </div>
        <div class="modal-body">
          <p>
            Are you sure you want to delete
            <strong>{{ deleteTarget?.name }}</strong
            >?
          </p>
          <p style="color: var(--danger); font-size: 13px; margin-top: 8px">
            This action cannot be undone.
          </p>
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
            {{ deleting ? "Deleting..." : "Delete" }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { getPosts, createPost, updatePost, deletePost } from "@/api/posts";

// State
const posts = ref([]);
const loading = ref(false);
const saving = ref(false);
const deleting = ref(false);
const showModal = ref(false);
const showDelete = ref(false);
const editing = ref(null);
const deleteTarget = ref(null);
const search = ref("");

const form = ref({
  name: "",
  description: "",
  default_photo: null,
});
const preview = ref(null);

// Computed
const filtered = computed(() => {
  if (!search.value) return posts.value;
  const query = search.value.toLowerCase();
  return posts.value.filter((post) => post.name.toLowerCase().includes(query));
});

// Helper functions
function truncate(text, length) {
  if (!text) return "—";
  return text.length > length ? text.substring(0, length) + "..." : text;
}

function formatDate(dateString) {
  if (!dateString) return "—";
  const date = new Date(dateString);
  return date.toLocaleDateString();
}

// Load posts
async function load() {
  loading.value = true;
  try {
    const { data } = await getPosts();
    posts.value = data.data || data;
  } catch (error) {
    console.error("Failed to load posts:", error);
  } finally {
    loading.value = false;
  }
}

// Open create modal
function openCreate() {
  editing.value = null;
  form.value = { name: "", description: "", default_photo: null };
  preview.value = null;
  showModal.value = true;
}

// Open edit modal
function openEdit(post) {
  editing.value = post;
  form.value = {
    name: post.name,
    description: post.description,
    default_photo: null,
  };
  preview.value = post.photo_url;
  showModal.value = true;
}

// Open delete modal
function openDelete(post) {
  deleteTarget.value = post;
  showDelete.value = true;
}

// Close modal
function closeModal() {
  showModal.value = false;
  form.value = { name: "", description: "", default_photo: null };
  preview.value = null;
  editing.value = null;
}

// Handle file selection
function handleFile(e) {
  const file = e.target.files[0];
  if (file) {
    form.value.default_photo = file;
    preview.value = URL.createObjectURL(file);
  }
}

// Save post
async function handleSave() {
  if (!form.value.name || !form.value.description) {
    alert("Please fill in all required fields");
    return;
  }

  saving.value = true;

  try {
    const formData = new FormData();
    formData.append("name", form.value.name);
    formData.append("description", form.value.description);

    if (form.value.default_photo instanceof File) {
      formData.append("default_photo", form.value.default_photo);
    }

    if (editing.value) {
      // For update, we need to use POST with _method PUT for file uploads
      formData.append("_method", "PUT");
      await updatePost(editing.value.id, formData);
    } else {
      await createPost(formData);
    }

    closeModal();
    await load();
  } catch (error) {
    console.error("Failed to save post:", error);
    alert(error.response?.data?.message || "Failed to save post");
  } finally {
    saving.value = false;
  }
}

// Delete post
async function handleDelete() {
  deleting.value = true;
  try {
    await deletePost(deleteTarget.value.id);
    showDelete.value = false;
    await load();
  } catch (error) {
    console.error("Failed to delete post:", error);
    alert("Failed to delete post");
  } finally {
    deleting.value = false;
  }
}

// Lifecycle
onMounted(() => {
  load();
});
</script>

<style scoped>
/* Page Header */
.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
  flex-wrap: wrap;
  gap: 16px;
}

.page-header h1 {
  font-size: 24px;
  font-weight: 700;
  color: var(--navy);
  margin: 0 0 4px 0;
}

.page-header p {
  color: var(--gray-500);
  font-size: 14px;
  margin: 0;
}

/* Card Header with Search */
.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 16px;
  margin-bottom: 20px;
}

.card-header h2 {
  margin: 0;
  font-size: 18px;
}

/* Actions */
.actions {
  display: flex;
  gap: 8px;
}

/* Table */
.table-wrap {
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
}

th {
  text-align: left;
  padding: 12px;
  background: var(--gray-50);
  font-weight: 600;
  font-size: 13px;
  border-bottom: 1px solid var(--gray-200);
}

td {
  padding: 12px;
  border-bottom: 1px solid var(--gray-100);
}

/* Modal */
.modal {
  max-width: 600px;
  width: 90%;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 24px;
  border-bottom: 1px solid var(--gray-200);
}

.modal-header h3 {
  margin: 0;
}

.modal-close {
  background: none;
  border: none;
  font-size: 24px;
  cursor: pointer;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 6px;
}

.modal-close:hover {
  background: var(--gray-100);
}

.modal-body {
  padding: 24px;
}

.modal-footer {
  padding: 16px 24px;
  border-top: 1px solid var(--gray-200);
  display: flex;
  justify-content: flex-end;
  gap: 12px;
}

/* Preview Image */
.preview {
  margin-top: 12px;
}

.preview img {
  max-width: 200px;
  max-height: 150px;
  object-fit: cover;
  border-radius: 8px;
  border: 1px solid var(--gray-200);
}

/* Required field */
.req {
  color: var(--danger);
}

/* Loading & Empty States */
.loading-center {
  display: flex;
  justify-content: center;
  padding: 40px;
}

.empty-state {
  text-align: center;
  padding: 40px;
  color: var(--gray-500);
}

/* Responsive */
@media (max-width: 768px) {
  .card-header {
    flex-direction: column;
    align-items: stretch;
  }

  .card-header input {
    width: 100% !important;
  }

  .actions {
    flex-direction: column;
  }

  .btn-sm {
    width: 100%;
    text-align: center;
  }
}
</style>

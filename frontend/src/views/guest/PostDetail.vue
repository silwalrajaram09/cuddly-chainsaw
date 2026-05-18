<template>
  <div>
    <div v-if="loading" class="loading-center" style="min-height: 50vh">
      <div class="spinner"></div>
    </div>

    <div v-else-if="post" class="page-wrap">
      <div class="page-hero">
        <div class="container">
          <RouterLink to="/posts" class="breadcrumb"
            >← Back to Posts</RouterLink
          >
          <h1>{{ post.name }}</h1>
          <div class="post-meta">
            <span class="post-date">{{ formatDate(post.created_at) }}</span>
            <span
              v-if="post.updated_at !== post.created_at"
              class="post-updated"
            >
              Updated: {{ formatDate(post.updated_at) }}
            </span>
          </div>
        </div>
      </div>

      <section class="page-section">
        <div class="container">
          <div class="card">
            <div v-if="post.photo_url" class="post-image">
              <img :src="post.photo_url" :alt="post.name" />
            </div>
            <div class="card-body">
              <div class="prose" v-html="formattedDescription"></div>
            </div>
          </div>
        </div>
      </section>
    </div>

    <div v-else class="not-found">
      <div class="container">
        <div class="not-found-content">
          <h2>Post Not Found</h2>
          <p>The post you're looking for doesn't exist or has been removed.</p>
          <RouterLink to="/posts" class="btn btn-primary"
            >View All Posts</RouterLink
          >
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRoute, RouterLink } from "vue-router";
import { getPost } from "@/api/posts";

const route = useRoute();
const post = ref(null);
const loading = ref(true);

// Format description as HTML (convert line breaks to paragraphs)
const formattedDescription = computed(() => {
  if (!post.value?.description) return "";
  // Convert line breaks to <p> tags
  const paragraphs = post.value.description.split("\n").filter((p) => p.trim());
  return paragraphs.map((p) => `<p>${escapeHtml(p)}</p>`).join("");
});

// Helper to escape HTML
function escapeHtml(text) {
  const div = document.createElement("div");
  div.textContent = text;
  return div.innerHTML;
}

function formatDate(dateString) {
  if (!dateString) return "";
  const date = new Date(dateString);
  return date.toLocaleDateString("en-US", {
    year: "numeric",
    month: "long",
    day: "numeric",
  });
}

async function loadPost() {
  loading.value = true;
  try {
    const response = await getPost(route.params.id);
    // Handle response structure
    post.value = response.data?.data || response.data;
    console.log("Loaded post:", post.value);
  } catch (error) {
    console.error("Failed to load post:", error);
    post.value = null;
  } finally {
    loading.value = false;
  }
}

onMounted(() => {
  loadPost();
});
</script>

<style scoped>
.post-meta {
  display: flex;
  gap: 16px;
  align-items: center;
  flex-wrap: wrap;
}

.post-date {
  color: var(--gold);
  font-weight: 600;
  font-size: 14px;
  text-transform: uppercase;
}

.post-updated {
  color: var(--gray-500);
  font-size: 13px;
}

.post-image {
  width: 100%;
  max-height: 400px;
  overflow: hidden;
}

.post-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.card-body {
  padding: 32px;
}

.prose {
  font-size: 16px;
  line-height: 1.8;
  color: var(--gray-700);
}

.prose :deep(p) {
  margin-bottom: 20px;
}

.prose :deep(p:last-child) {
  margin-bottom: 0;
}

.prose :deep(strong) {
  color: var(--navy);
  font-weight: 700;
}

.prose :deep(a) {
  color: var(--gold);
  text-decoration: underline;
}

.prose :deep(a:hover) {
  color: var(--gold-dark);
}

/* Not Found */
.not-found {
  min-height: 60vh;
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
}

.not-found-content {
  padding: 40px;
}

.not-found h2 {
  font-size: 28px;
  color: var(--gray-600);
  margin: 0 0 12px 0;
}

.not-found p {
  color: var(--gray-500);
  margin-bottom: 24px;
}

/* Responsive */
@media (max-width: 768px) {
  .card-body {
    padding: 20px;
  }

  .prose {
    font-size: 14px;
  }
}

@media (max-width: 480px) {
  .post-meta {
    flex-direction: column;
    align-items: flex-start;
    gap: 8px;
  }
}
</style>

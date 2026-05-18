<template>
  <div>
    <div class="page-hero">
      <div class="container">
        <h1>News &amp; Posts</h1>
        <p>Latest updates from Info Tech.</p>
      </div>
    </div>

    <section class="page-section">
      <div class="container">
        <div v-if="loading" class="loading-center">
          <div class="spinner"></div>
        </div>

        <div v-else-if="!posts.length" class="empty-state">
          <p>No posts available.</p>
        </div>

        <div v-else class="grid grid-3">
          <RouterLink
            v-for="post in posts"
            :key="post.id"
            :to="`/posts/${post.id}`"
            class="post-card card"
          >
            <div class="card-body">
              <p class="post-date">{{ formatDate(post.created_at) }}</p>
              <h3>{{ post.name }}</h3>
              <p>{{ truncate(post.description, 120) }}</p>
              <div v-if="post.photo_url" class="post-image">
                <img :src="post.photo_url" :alt="post.name" />
              </div>
            </div>
          </RouterLink>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { RouterLink } from "vue-router";
import { getPosts } from "@/api/posts";

const posts = ref([]);
const loading = ref(true);

function truncate(str, len) {
  if (!str) return "";
  return str.length > len ? str.slice(0, len) + "…" : str;
}

function formatDate(dateString) {
  if (!dateString) return "";
  const date = new Date(dateString);
  return date.toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });
}

async function loadPosts() {
  loading.value = true;
  try {
    const response = await getPosts();
    // Handle paginated response
    posts.value = response.data?.data || response.data || [];
    console.log("Loaded posts:", posts.value);
  } catch (error) {
    console.error("Failed to load posts:", error);
    posts.value = [];
  } finally {
    loading.value = false;
  }
}

onMounted(() => {
  loadPosts();
});
</script>

<style scoped>
.page-hero {
  background: var(--navy);
  padding: 56px 0 40px;
}

.page-hero h1 {
  color: var(--white);
  font-size: 36px;
  font-weight: 800;
  margin-bottom: 8px;
}

.page-hero p {
  color: rgba(255, 255, 255, 0.65);
}

.post-card {
  cursor: pointer;
  transition:
    transform 0.2s,
    box-shadow 0.2s;
  text-decoration: none;
  display: block;
}

.post-card:hover {
  transform: translateY(-3px);
  box-shadow: var(--shadow-lg);
}

.post-date {
  font-size: 12px;
  color: var(--gold);
  font-weight: 600;
  margin-bottom: 8px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.post-card h3 {
  font-size: 18px;
  font-weight: 800;
  color: var(--navy);
  margin-bottom: 12px;
  line-height: 1.3;
}

.post-card p {
  font-size: 14px;
  color: var(--gray-500);
  line-height: 1.65;
  margin-bottom: 12px;
}

.post-image {
  margin-top: 12px;
}

.post-image img {
  width: 100%;
  height: 160px;
  object-fit: cover;
  border-radius: 8px;
}

/* Responsive */
@media (max-width: 768px) {
  .post-card h3 {
    font-size: 16px;
  }

  .post-card p {
    font-size: 13px;
  }
}
</style>

<template>
  <div>
    <div class="page-hero">
      <div class="container">
        <h1>News &amp; Posts</h1>
        <p>Latest updates from DSP Holdings.</p>
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
              <h3>{{ post.title }}</h3>
              <p>{{ truncate(post.excerpt || post.content, 120) }}</p>
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

function formatDate(d) {
  if (!d) return "";
  return new Date(d).toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });
}

onMounted(async () => {
  try {
    const { data } = await getPosts();
    posts.value = data.data || data;
  } catch {
    posts.value = [];
  } finally {
    loading.value = false;
  }
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
}
.post-card:hover {
  transform: translateY(-3px);
  box-shadow: var(--shadow-lg);
}
.post-date {
  font-size: 12px;
  color: var(--gold);
  font-weight: 600;
  margin-bottom: 6px;
}
.post-card h3 {
  font-size: 16px;
  font-weight: 800;
  color: var(--navy);
  margin-bottom: 8px;
}
.post-card p {
  font-size: 13px;
  color: var(--gray-500);
  line-height: 1.65;
}
</style>

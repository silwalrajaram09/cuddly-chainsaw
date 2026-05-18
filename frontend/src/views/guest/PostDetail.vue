<template>
  <div>
    <div v-if="loading" class="loading-center" style="min-height: 50vh">
      <div class="spinner"></div>
    </div>

    <div v-else-if="post" class="page-wrap">
      <div class="page-hero">
        <div class="container">
          <RouterLink to="/posts" class="breadcrumb">← Posts</RouterLink>
          <h1>{{ post.title }}</h1>
          <p class="post-date">{{ formatDate(post.created_at) }}</p>
        </div>
      </div>

      <section class="page-section">
        <div class="container">
          <div class="card">
            <div class="card-body">
              <div class="prose" v-html="post.content"></div>
            </div>
          </div>
        </div>
      </section>
    </div>

    <div v-else class="loading-center" style="min-height: 50vh">
      <p style="color: var(--gray-400)">Post not found.</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute, RouterLink } from "vue-router";
import { getPost } from "@/api/posts";

const route = useRoute();
const post = ref(null);
const loading = ref(true);

function formatDate(d) {
  if (!d) return "";
  return new Date(d).toLocaleDateString("en-US", {
    year: "numeric",
    month: "long",
    day: "numeric",
  });
}

onMounted(async () => {
  try {
    const { data } = await getPost(route.params.id);
    post.value = data;
  } catch {
    post.value = null;
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
.breadcrumb {
  color: rgba(255, 255, 255, 0.5);
  font-size: 13px;
  display: block;
  margin-bottom: 12px;
}
.breadcrumb:hover {
  color: var(--gold);
}
.page-hero h1 {
  color: var(--white);
  font-size: 34px;
  font-weight: 900;
  margin-bottom: 10px;
}
.post-date {
  color: var(--gold);
  font-weight: 700;
}
.prose :deep(p) {
  margin: 0 0 12px;
}
</style>

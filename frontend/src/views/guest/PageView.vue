<template>
  <div>
    <div v-if="loading" class="loading-center" style="min-height: 50vh">
      <div class="spinner"></div>
    </div>

    <div v-else-if="page" class="page-wrap">
      <div class="page-hero">
        <div class="container">
          <h1>{{ page.title }}</h1>
          <p v-if="page.subtitle">{{ page.subtitle }}</p>
        </div>
      </div>

      <section class="page-section">
        <div class="container">
          <div class="card">
            <div class="card-body">
              <div class="prose" v-html="page.content"></div>
            </div>
          </div>
        </div>
      </section>
    </div>

    <div v-else class="loading-center" style="min-height: 50vh">
      <p style="color: var(--gray-400)">Page not found.</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import { getPage } from "@/api/pages";

const route = useRoute();
const page = ref(null);
const loading = ref(true);

onMounted(async () => {
  try {
    const { data } = await getPage(route.params.id);
    page.value = data;
  } catch {
    page.value = null;
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
  font-size: 34px;
  font-weight: 800;
}
.page-hero p {
  color: rgba(255, 255, 255, 0.65);
  font-size: 15px;
  margin-top: 8px;
}
.prose :deep(p) {
  margin: 0 0 12px;
}
.prose :deep(a) {
  color: var(--gold);
  text-decoration: underline;
}
</style>

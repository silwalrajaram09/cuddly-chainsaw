<template>
  <div>
    <div class="page-hero">
      <div class="container">
        <h1>Committees</h1>
        <p>
          Our specialized committees drive strategic oversight and governance.
        </p>
      </div>
    </div>

    <section class="page-section">
      <div class="container">
        <div v-if="loading" class="loading-center">
          <div class="spinner"></div>
        </div>
        <div v-else-if="!committees.length" class="empty-state">
          <p>No committees found.</p>
        </div>
        <div v-else class="grid grid-3">
          <RouterLink
            v-for="c in committees"
            :key="c.id"
            :to="`/committees/${c.id}`"
            class="c-card card"
          >
            <div class="card-body">
              <div class="c-icon">🏛</div>
              <h3>{{ c.name }}</h3>
              <p>{{ truncate(c.description, 120) }}</p>
              <span class="read-more">View details →</span>
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
import { getCommittees } from "@/api/committees";

const committees = ref([]);
const loading = ref(true);

function truncate(str, len) {
  if (!str) return "";
  return str.length > len ? str.slice(0, len) + "…" : str;
}

onMounted(async () => {
  try {
    const { data } = await getCommittees();
    committees.value = data.data || data;
  } catch {
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
  font-weight: 700;
  margin-bottom: 8px;
}
.page-hero p {
  color: rgba(255, 255, 255, 0.65);
  font-size: 16px;
}
.c-card {
  cursor: pointer;
  transition:
    box-shadow 0.2s,
    transform 0.2s;
}
.c-card:hover {
  box-shadow: var(--shadow-lg);
  transform: translateY(-3px);
}
.c-icon {
  font-size: 32px;
  margin-bottom: 12px;
}
.c-card h3 {
  font-size: 17px;
  font-weight: 600;
  color: var(--navy);
  margin-bottom: 8px;
}
.c-card p {
  font-size: 13px;
  color: var(--gray-500);
  line-height: 1.65;
  margin-bottom: 14px;
}
.read-more {
  font-size: 13px;
  color: var(--gold);
  font-weight: 500;
}
</style>

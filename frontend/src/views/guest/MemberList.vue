<template>
  <div>
    <div class="page-hero">
      <div class="container">
        <h1>Members</h1>
        <p>Meet the people behind our committees and programs.</p>
      </div>
    </div>

    <section class="page-section">
      <div class="container">
        <div v-if="loading" class="loading-center">
          <div class="spinner"></div>
        </div>
        <div v-else-if="!members.length" class="empty-state">
          <p>No members found.</p>
        </div>
        <div v-else class="grid grid-3">
          <RouterLink
            v-for="m in members"
            :key="m.id"
            :to="`/members/${m.id}`"
            class="m-card card"
          >
            <div class="card-body">
              <div class="avatar">{{ initials(m.name) }}</div>
              <h3>{{ m.name }}</h3>
              <p>{{ m.email }}</p>
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
import { getMembers } from "@/api/members";

const members = ref([]);
const loading = ref(true);

function initials(name) {
  if (!name) return "?";
  return name
    .split(" ")
    .map((n) => n[0])
    .join("")
    .toUpperCase()
    .slice(0, 2);
}

onMounted(async () => {
  try {
    const { data } = await getMembers();
    members.value = data.data || data;
  } catch {
    members.value = [];
  } finally {
    loading.value = false;
  }
});
</script>

<style scoped>
.m-card {
  cursor: pointer;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.m-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-lg);
}
.m-card h3 {
  font-size: 15px;
  color: var(--navy);
  font-weight: 700;
  text-align: center;
  margin-bottom: 6px;
}
.m-card p {
  font-size: 13px;
  color: var(--gray-500);
  text-align: center;
  word-break: break-word;
}
</style>

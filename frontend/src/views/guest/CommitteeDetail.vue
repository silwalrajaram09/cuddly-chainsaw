<template>
  <div>
    <div v-if="loading" class="loading-center" style="min-height: 50vh">
      <div class="spinner"></div>
    </div>

    <div v-else-if="committee">
      <div class="page-hero">
        <div class="container">
          <RouterLink to="/committees" class="breadcrumb"
            >← Committees</RouterLink
          >
          <h1>{{ committee.name }}</h1>
          <p>{{ committee.description }}</p>
        </div>
      </div>

      <section class="page-section">
        <div class="container">
          <!-- Members in this committee -->
          <div v-if="committee.members?.length">
            <h2 class="section-title">Committee Members</h2>
            <div class="grid grid-4">
              <div
                v-for="m in committee.members"
                :key="m.id"
                class="member-card card"
              >
                <div class="card-body" style="text-align: center">
                  <div class="avatar">{{ initials(m.name) }}</div>
                  <h4>{{ m.name }}</h4>
                  <p>{{ m.pivot?.role || m.email }}</p>
                </div>
              </div>
            </div>
          </div>
          <div v-else class="empty-state">
            <p>No members listed for this committee.</p>
          </div>
        </div>
      </section>
    </div>

    <div v-else class="loading-center" style="min-height: 50vh">
      <p style="color: var(--gray-400)">Committee not found.</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute, RouterLink } from "vue-router";
import { getCommittee } from "@/api/committees";

const route = useRoute();
const committee = ref(null);
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
    const { data } = await getCommittee(route.params.id);
    committee.value = data;
    console.data;
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
  font-size: 32px;
  font-weight: 700;
  margin-bottom: 8px;
}
.page-hero p {
  color: rgba(255, 255, 255, 0.65);
  font-size: 15px;
  max-width: 600px;
}
.avatar {
  width: 56px;
  height: 56px;
  border-radius: 50%;
  background: var(--navy);
  color: var(--white);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  font-weight: 600;
  margin: 0 auto 12px;
}
.member-card h4 {
  font-size: 14px;
  font-weight: 600;
  color: var(--navy);
  margin-bottom: 4px;
}
.member-card p {
  font-size: 12px;
  color: var(--gray-500);
}
</style>

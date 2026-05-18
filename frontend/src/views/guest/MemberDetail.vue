<template>
  <div>
    <div v-if="loading" class="loading-center" style="min-height: 50vh">
      <div class="spinner"></div>
    </div>

    <div v-else-if="member" class="page-wrap">
      <div class="page-hero">
        <div class="container">
          <RouterLink to="/members" class="breadcrumb">← Members</RouterLink>
          <h1>{{ member.name }}</h1>
          <p>{{ member.email }}</p>
        </div>
      </div>

      <section class="page-section">
        <div class="container">
          <div v-if="member.committees?.length">
            <h2 class="section-title">Committees</h2>
            <div class="grid grid-3">
              <RouterLink
                v-for="c in member.committees"
                :key="c.id || c.committee_id"
                :to="`/committees/${c.id || c.committee_id}`"
                class="card p-0 c-card"
              >
                <div class="card-body">{{ c.name || c.committee?.name }}</div>
              </RouterLink>
            </div>
          </div>

          <div v-else class="empty-state">
            <p>No committee assignments.</p>
          </div>

          <div style="height: 24px"></div>

          <div v-if="member.programs?.length">
            <h2 class="section-title">Programs</h2>
            <div class="grid grid-3">
              <RouterLink
                v-for="p in member.programs"
                :key="p.id || p.program_id"
                :to="`/programs/${p.id || p.program_id}`"
                class="card p-0 c-card"
              >
                <div class="card-body">{{ p.name || p.program?.name }}</div>
              </RouterLink>
            </div>
          </div>
          <div v-else class="empty-state">
            <p>No program assignments.</p>
          </div>
        </div>
      </section>
    </div>

    <div v-else class="loading-center" style="min-height: 50vh">
      <p style="color: var(--gray-400)">Member not found.</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute, RouterLink } from "vue-router";
import { getMember } from "@/api/members";

const route = useRoute();
const member = ref(null);
const loading = ref(true);

onMounted(async () => {
  try {
    const { data } = await getMember(route.params.id);
    member.value = data;
  } catch {
    member.value = null;
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
  font-weight: 800;
  margin-bottom: 8px;
}
.page-hero p {
  color: rgba(255, 255, 255, 0.65);
  font-size: 15px;
}
.c-card {
  text-align: center;
  cursor: pointer;
  transition:
    transform 0.2s,
    box-shadow 0.2s;
}
.c-card:hover {
  transform: translateY(-3px);
  box-shadow: var(--shadow-lg);
}
</style>

<template>
  <div>
    <div class="page-hero">
      <div class="container">
        <h1>Programs</h1>
        <p>Investment programs designed for long-term growth.</p>
      </div>
    </div>

    <section class="page-section">
      <div class="container">
        <div v-if="loading" class="loading-center" style="min-height: 50vh">
          <div class="spinner"></div>
        </div>

        <div v-else-if="!programs.length" class="empty-state">
          <div class="empty-icon">📌</div>
          <h3>No Programs Found</h3>
          <p>Programs will appear here once they are added.</p>
        </div>

        <div v-else class="grid grid-3">
          <RouterLink
            v-for="p in programs"
            :key="p.id"
            :to="`/programs/${p.id}`"
            class="program-card card"
          >
            <div class="card-body">
              <span class="badge badge-gold" style="margin-bottom: 10px">
                Program
              </span>

              <div class="program-top">
                <span class="program-badge" :class="p.status">
                  {{ p.status || "unknown" }}
                </span>
              </div>

              <h3>{{ p.name }}</h3>
              <p class="desc">{{ truncate(p.description, 120) }}</p>

              <div class="meta">
                <div class="meta-item">
                  <span class="meta-label">Members</span>
                  <span class="meta-value">{{ p.members_count || 0 }}</span>
                </div>
                <div class="meta-item" v-if="p.duration != null">
                  <span class="meta-label">Duration</span>
                  <span class="meta-value">{{ p.duration }} days</span>
                </div>
                <div class="meta-item" v-if="p.type">
                  <span class="meta-label">Type</span>
                  <span class="meta-value">{{ p.type }}</span>
                </div>
              </div>

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
import { getPrograms } from "@/api/programs";

const programs = ref([]);
const loading = ref(true);

function truncate(str, len) {
  if (!str) return "";
  return str.length > len ? str.slice(0, len) + "…" : str;
}

onMounted(async () => {
  try {
    const { data } = await getPrograms();
    programs.value = data.data ?? data;
  } catch (e) {
    console.error("Failed to load programs:", e);
    programs.value = [];
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

/* Loading */
.loading-center {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 50vh;
}
.spinner {
  width: 48px;
  height: 48px;
  border: 3px solid var(--gray-200);
  border-top-color: var(--gold);
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
}
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

/* Grid */
.page-section {
  padding: 48px 0;
}
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}
.grid-3 {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 24px;
}

.program-card {
  cursor: pointer;
  transition:
    box-shadow 0.2s,
    transform 0.2s;
}
.program-card:hover {
  box-shadow: var(--shadow-lg);
  transform: translateY(-3px);
}

.program-top {
  display: flex;
  justify-content: flex-start;
  margin-bottom: 10px;
}

.program-badge {
  display: inline-block;
  padding: 4px 12px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.4px;
}

/* status colors (best-effort) */
.program-badge.upcoming {
  background: rgba(59, 130, 246, 0.2);
  color: #60a5fa;
}
.program-badge.ongoing {
  background: rgba(34, 197, 94, 0.2);
  color: #4ade80;
}
.program-badge.completed {
  background: rgba(156, 163, 175, 0.2);
  color: #9ca3af;
}
.program-badge.unknown,
.program-badge {
  background: rgba(255, 255, 255, 0.08);
  color: rgba(255, 255, 255, 0.9);
}

.card-body {
  padding: 18px;
}

.program-card h3 {
  font-size: 17px;
  font-weight: 600;
  color: var(--navy);
  margin-bottom: 8px;
}

.desc {
  font-size: 13px;
  color: var(--gray-500);
  line-height: 1.65;
  min-height: 44px;
}

.meta {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 12px;
  margin-top: 16px;
}
.meta-item {
  padding: 10px 12px;
  border-radius: 12px;
  background: var(--gray-50);
}
.meta-label {
  display: block;
  font-size: 11px;
  color: var(--gray-500);
  text-transform: uppercase;
  letter-spacing: 0.3px;
  margin-bottom: 6px;
}
.meta-value {
  font-size: 14px;
  font-weight: 700;
  color: var(--navy);
}

.read-more {
  display: inline-block;
  margin-top: 14px;
  font-size: 13px;
  color: var(--gold);
  font-weight: 600;
}

/* Empty */
.empty-state {
  text-align: center;
  padding: 60px 20px;
  background: var(--white);
  border-radius: 16px;
}
.empty-icon {
  font-size: 64px;
  margin-bottom: 16px;
  opacity: 0.5;
}
.empty-state h3 {
  font-size: 20px;
  color: var(--gray-600);
  margin: 0 0 8px 0;
}
.empty-state p {
  color: var(--gray-500);
  margin: 0;
}

@media (max-width: 768px) {
  .meta {
    grid-template-columns: 1fr;
  }
}
</style>

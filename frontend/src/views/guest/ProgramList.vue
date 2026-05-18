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
.grid-3 {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 24px;
}

.program-card {
  cursor: pointer;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.program-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-lg);
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
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

/* status colors */
.program-badge.upcoming {
  background: #dbeafe;
  color: #1e40af;
}
.program-badge.ongoing {
  background: #dcfce7;
  color: #15803d;
}
.program-badge.completed {
  background: #f1f5f9;
  color: #475569;
}
.program-badge.unknown,
.program-badge {
  background: var(--gray-100);
  color: var(--gray-600);
}

.card-body {
  padding: 24px;
}

.program-card h3 {
  font-size: 17px;
  font-weight: 700;
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
  border-radius: 8px;
  background: var(--gray-50);
  border: 1px solid var(--gray-200);
}
.meta-label {
  display: block;
  font-size: 10px;
  color: var(--gray-500);
  text-transform: uppercase;
  letter-spacing: 0.3px;
  margin-bottom: 4px;
  font-weight: 600;
}
.meta-value {
  font-size: 13px;
  font-weight: 700;
  color: var(--navy);
}

/* Empty */
.empty-state {
  text-align: center;
  padding: 60px 20px;
  background: var(--white);
  border-radius: 16px;
  border: 1px dashed var(--gray-300);
}
.empty-icon {
  font-size: 48px;
  margin-bottom: 16px;
  opacity: 0.6;
}
.empty-state h3 {
  font-size: 18px;
  color: var(--gray-700);
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

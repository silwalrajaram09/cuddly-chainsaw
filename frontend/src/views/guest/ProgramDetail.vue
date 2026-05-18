<template>
  <div>
    <div v-if="loading" class="loading-center" style="min-height: 50vh">
      <div class="spinner"></div>
    </div>

    <div v-else-if="program" class="page-wrap">
      <!-- Hero Section -->
      <div class="page-hero">
        <div class="container">
          <RouterLink to="/programs" class="breadcrumb"
            >← Back to Programs</RouterLink
          >
          <div class="hero-content">
            <div>
              <span class="program-badge" :class="program.status">
                {{ program.status }}
              </span>
              <h1>{{ program.name }}</h1>
              <p>{{ program.description }}</p>
            </div>
            <div class="program-stats">
              <div class="stat-item">
                <span class="stat-label">Type</span>
                <span class="stat-value">{{ program.type }}</span>
              </div>
              <div class="stat-item">
                <span class="stat-label">Duration</span>
                <span class="stat-value">{{ program.duration }} days</span>
              </div>
              <div class="stat-item">
                <span class="stat-label">Members</span>
                <span class="stat-value">{{ program.members_count || 0 }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Program Details Section -->
      <section class="page-section">
        <div class="container">
          <div class="details-grid">
            <div class="details-main">
              <div class="info-card">
                <h3>Program Information</h3>
                <div class="info-list">
                  <div class="info-item">
                    <span class="info-label">📍 Location</span>
                    <span class="info-value">{{
                      program.location || "Not specified"
                    }}</span>
                  </div>
                  <div class="info-item">
                    <span class="info-label">📅 Start Date</span>
                    <span class="info-value">{{
                      formatDate(program.start_date)
                    }}</span>
                  </div>
                  <div class="info-item">
                    <span class="info-label">🏁 End Date</span>
                    <span class="info-value">{{
                      program.end_date
                        ? formatDate(program.end_date)
                        : "Ongoing"
                    }}</span>
                  </div>
                  <div class="info-item">
                    <span class="info-label">🌍 Country</span>
                    <span class="info-value">{{
                      program.country?.name || "Not specified"
                    }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Members Section -->
      <section class="page-section" v-if="program.members?.length">
        <div class="container">
          <div class="section-header">
            <h2 class="section-title">Program Members</h2>
            <span class="member-count"
              >{{ program.members.length }} Members</span
            >
          </div>

          <div class="grid grid-4">
            <div
              v-for="member in program.members"
              :key="member.id"
              class="member-card"
            >
              <div
                class="member-avatar"
                :style="{ backgroundColor: getAvatarColor(member.id) }"
              >
                <span>{{ getInitials(member.full_name) }}</span>
              </div>
              <div class="member-info">
                <h4>{{ member.full_name }}</h4>
                <p class="member-email">{{ member.email }}</p>
                <span class="member-role" v-if="member.pivot?.role">
                  {{ member.pivot.role }}
                </span>
                <div class="member-meta">
                  <span class="member-type" :class="member.member_type">
                    {{ member.member_type }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Empty State for Members -->
      <section class="page-section" v-else>
        <div class="container">
          <div class="empty-state">
            <div class="empty-icon">👥</div>
            <h3>No Members Yet</h3>
            <p>This program doesn't have any members assigned yet.</p>
          </div>
        </div>
      </section>
    </div>

    <!-- Not Found State -->
    <div v-else class="not-found">
      <div class="container">
        <div class="not-found-content">
          <div class="not-found-icon">🔍</div>
          <h2>Program Not Found</h2>
          <p>
            The program you're looking for doesn't exist or has been removed.
          </p>
          <RouterLink to="/programs" class="btn btn-primary"
            >View All Programs</RouterLink
          >
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute, RouterLink } from "vue-router";
import { getProgram } from "@/api/programs";

const route = useRoute();
const program = ref(null);
const loading = ref(true);

// Helper function to get initials from full name
function getInitials(fullName) {
  if (!fullName) return "?";
  return fullName
    .split(" ")
    .map((name) => name.charAt(0))
    .join("")
    .toUpperCase()
    .slice(0, 2);
}

// Helper function to format date
function formatDate(dateString) {
  if (!dateString) return "N/A";
  const date = new Date(dateString);
  return date.toLocaleDateString("en-US", {
    year: "numeric",
    month: "long",
    day: "numeric",
  });
}

// Helper function to generate consistent avatar colors
function getAvatarColor(id) {
  const colors = [
    "#1e3a5f", // navy
    "#c9a227", // gold
    "#2c7da0", // blue
    "#2e8b57", // green
    "#8b4513", // brown
    "#4a148c", // purple
    "#c62828", // red
  ];

  let hash = 0;
  for (let i = 0; i < id.length; i++) {
    hash = (hash << 5) - hash + id.charCodeAt(i);
    hash |= 0;
  }
  return colors[Math.abs(hash) % colors.length];
}

onMounted(async () => {
  try {
    const { data } = await getProgram(route.params.id);
    program.value = data.data || data;
    console.log("Program data:", program.value);
  } catch (error) {
    console.error("Failed to load program:", error);
    program.value = null;
  } finally {
    loading.value = false;
  }
});
</script>

<style scoped>
/* Hero Section */
.page-hero {
  background: linear-gradient(135deg, var(--navy) 0%, #1e3a5f 100%);
  padding: 48px 0;
  position: relative;
  overflow: hidden;
}

.page-hero::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200"><path fill="rgba(255,255,255,0.03)" d="M0 0h200v200H0z"/></svg>');
  opacity: 0.1;
}

.breadcrumb {
  display: inline-flex;
  align-items: center;
  color: rgba(255, 255, 255, 0.6);
  font-size: 14px;
  text-decoration: none;
  margin-bottom: 20px;
  transition: color 0.2s;
}

.breadcrumb:hover {
  color: var(--gold);
}

.hero-content {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 32px;
  flex-wrap: wrap;
}

.hero-content > div:first-child {
  flex: 1;
}

.program-badge {
  display: inline-block;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 16px;
}

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

.page-hero h1 {
  color: var(--white);
  font-size: 42px;
  font-weight: 800;
  margin: 0 0 16px 0;
  line-height: 1.2;
}

.page-hero p {
  color: rgba(255, 255, 255, 0.7);
  font-size: 16px;
  line-height: 1.6;
  max-width: 600px;
}

.program-stats {
  display: flex;
  gap: 32px;
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  padding: 20px 32px;
  border-radius: 16px;
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.stat-item {
  text-align: center;
}

.stat-label {
  display: block;
  font-size: 12px;
  color: rgba(255, 255, 255, 0.6);
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 8px;
}

.stat-value {
  display: block;
  font-size: 28px;
  font-weight: 700;
  color: var(--white);
}

/* Page Section */
.page-section {
  padding: 48px 0;
}

.page-section:first-of-type {
  padding-top: 48px;
}

/* Details Grid */
.details-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 32px;
}

.info-card {
  background: var(--white);
  border-radius: 16px;
  padding: 24px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.info-card h3 {
  font-size: 18px;
  font-weight: 600;
  color: var(--navy);
  margin: 0 0 20px 0;
}

.info-list {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
}

.info-item {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.info-label {
  font-size: 12px;
  font-weight: 600;
  color: var(--gray-500);
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.info-value {
  font-size: 16px;
  font-weight: 500;
  color: var(--gray-800);
}

/* Members Section */
.section-header {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  margin-bottom: 32px;
  flex-wrap: wrap;
  gap: 16px;
}

.section-title {
  font-size: 28px;
  font-weight: 700;
  color: var(--navy);
  margin: 0;
}

.member-count {
  font-size: 14px;
  color: var(--gray-500);
  background: var(--gray-100);
  padding: 4px 12px;
  border-radius: 20px;
}

.grid-4 {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 24px;
}

.member-card {
  background: var(--white);
  border-radius: 16px;
  overflow: hidden;
  transition: all 0.3s ease;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  cursor: pointer;
  text-align: center;
  padding: 24px;
}

.member-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
}

.member-avatar {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 16px;
  background: var(--navy);
}

.member-avatar span {
  font-size: 28px;
  font-weight: 700;
  color: white;
}

.member-info h4 {
  font-size: 16px;
  font-weight: 700;
  color: var(--navy);
  margin: 0 0 4px 0;
}

.member-email {
  font-size: 13px;
  color: var(--gray-500);
  margin: 0 0 8px 0;
}

.member-role {
  display: inline-block;
  padding: 2px 8px;
  background: var(--gold);
  color: white;
  font-size: 11px;
  font-weight: 600;
  border-radius: 12px;
  margin-bottom: 8px;
}

.member-meta {
  margin-top: 8px;
}

.member-type {
  display: inline-block;
  padding: 2px 8px;
  font-size: 10px;
  font-weight: 600;
  border-radius: 4px;
}

.member-type.yearly {
  background: #dbeafe;
  color: #1e40af;
}

.member-type.lifetime {
  background: #fef3c7;
  color: #92400e;
}

/* Empty State */
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

/* Not Found */
.not-found {
  min-height: 60vh;
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
}

.not-found-content {
  padding: 40px;
}

.not-found-icon {
  font-size: 80px;
  margin-bottom: 24px;
  opacity: 0.5;
}

.not-found h2 {
  font-size: 28px;
  color: var(--gray-600);
  margin: 0 0 12px 0;
}

.not-found p {
  color: var(--gray-500);
  margin-bottom: 24px;
}

/* Loading State */
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

/* Container */
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

/* Responsive */
@media (max-width: 768px) {
  .hero-content {
    flex-direction: column;
  }

  .program-stats {
    width: 100%;
    justify-content: space-around;
  }

  .page-hero h1 {
    font-size: 32px;
  }

  .section-title {
    font-size: 24px;
  }

  .grid-4 {
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 16px;
  }
}

@media (max-width: 480px) {
  .program-stats {
    flex-direction: column;
    gap: 16px;
    align-items: center;
  }

  .info-list {
    grid-template-columns: 1fr;
  }
}
</style>

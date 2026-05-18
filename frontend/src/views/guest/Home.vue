<template>
  <div>
    <!-- Hero -->
    <section class="hero">
      <div class="container">
        <h1 class="hero-title">
          DSP Holdings <span class="gold">Pvt. Ltd.</span>
        </h1>
        <p class="hero-sub">
          A Kathmandu-based investment holding company committed to sustainable
          growth, wealth management, and long-term value creation across Nepal
          and beyond.
        </p>
        <div class="hero-actions">
          <RouterLink to="/committees" class="btn btn-gold"
            >Our Committees</RouterLink
          >
          <RouterLink
            to="/programs"
            class="btn btn-outline"
            style="color: #fff; border-color: rgba(255, 255, 255, 0.4)"
            >View Programs</RouterLink
          >
        </div>
      </div>
    </section>

    <!-- Stats -->
    <section class="stats-bar">
      <div class="container">
        <div class="stats-grid">
          <div class="stat">
            <p class="stat-num">{{ stats.committees }}</p>
            <p class="stat-label">Committees</p>
          </div>
          <div class="stat">
            <p class="stat-num">{{ stats.programs }}</p>
            <p class="stat-label">Programs</p>
          </div>
          <div class="stat">
            <p class="stat-num">{{ stats.members }}</p>
            <p class="stat-label">Members</p>
          </div>
          <div class="stat">
            <p class="stat-num">{{ stats.posts }}</p>
            <p class="stat-label">Publications</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Committees preview -->
    <section class="page-section">
      <div class="container">
        <h2 class="section-title">Our Committees</h2>
        <p class="section-subtitle">
          Specialized leadership groups driving strategic direction.
        </p>

        <div v-if="loadingCommittees" class="loading-center">
          <div class="spinner"></div>
        </div>
        <div v-else class="grid grid-3">
          <RouterLink
            v-for="c in committees"
            :key="c.id"
            :to="`/committees/${c.id}`"
            class="committee-card card"
          >
            <div class="card-body">
              <div class="committee-icon">🏛</div>
              <h3>{{ c.name }}</h3>
              <p>{{ truncate(c.description, 100) }}</p>
            </div>
          </RouterLink>
        </div>
        <div style="text-align: center; margin-top: 28px">
          <RouterLink to="/committees" class="btn btn-outline"
            >View All Committees</RouterLink
          >
        </div>
      </div>
    </section>

    <!-- Programs preview -->
    <section class="page-section" style="background: var(--gray-100)">
      <div class="container">
        <h2 class="section-title">Programs</h2>
        <p class="section-subtitle">
          Investment programs designed for long-term growth.
        </p>

        <div v-if="loadingPrograms" class="loading-center">
          <div class="spinner"></div>
        </div>
        <div v-else class="grid grid-3">
          <RouterLink
            v-for="p in programs"
            :key="p.id"
            :to="`/programs/${p.id}`"
            class="program-card card"
          >
            <div class="card-body">
              <span class="badge badge-gold" style="margin-bottom: 10px"
                >Program</span
              >
              <h3>{{ p.name }}</h3>
              <p>{{ truncate(p.description, 90) }}</p>
            </div>
          </RouterLink>
        </div>
        <div style="text-align: center; margin-top: 28px">
          <RouterLink to="/programs" class="btn btn-outline"
            >View All Programs</RouterLink
          >
        </div>
      </div>
    </section>

    <!-- Latest posts -->
    <section class="page-section">
      <div class="container">
        <h2 class="section-title">Latest News</h2>
        <p class="section-subtitle">Updates and insights from DSP Holdings.</p>

        <div v-if="loadingPosts" class="loading-center">
          <div class="spinner"></div>
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
              <p>{{ truncate(post.excerpt || post.content, 90) }}</p>
            </div>
          </RouterLink>
        </div>
        <div style="text-align: center; margin-top: 28px">
          <RouterLink to="/posts" class="btn btn-outline">All News</RouterLink>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { RouterLink } from "vue-router";
import { getCommittees } from "@/api/committees";
import { getPrograms } from "@/api/programs";
import { getPosts } from "@/api/posts";
import { getMembers } from "@/api/members";

const committees = ref([]);
const programs = ref([]);
const posts = ref([]);
const stats = ref({ committees: 0, programs: 0, members: 0, posts: 0 });

const loadingCommittees = ref(true);
const loadingPrograms = ref(true);
const loadingPosts = ref(true);

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
  const [c, p, po, m] = await Promise.allSettled([
    getCommittees(),
    getPrograms(),
    getPosts(),
    getMembers(),
  ]);

  if (c.status === "fulfilled") {
    committees.value =
      c.value.data.data?.slice(0, 6) || c.value.data.slice(0, 6);
    stats.value.committees = c.value.data.total || committees.value.length;
  }
  if (p.status === "fulfilled") {
    programs.value = p.value.data.data?.slice(0, 6) || p.value.data.slice(0, 6);
    stats.value.programs = p.value.data.total || programs.value.length;
  }
  if (po.status === "fulfilled") {
    posts.value = po.value.data.data?.slice(0, 3) || po.value.data.slice(0, 3);
    stats.value.posts = po.value.data.total || posts.value.length;
  }
  if (m.status === "fulfilled") {
    stats.value.members =
      m.value.data.total || (m.value.data.data || m.value.data).length;
  }

  loadingCommittees.value = false;
  loadingPrograms.value = false;
  loadingPosts.value = false;
});
</script>

<style scoped>
/* Hero */
.hero {
  background: linear-gradient(135deg, var(--navy) 0%, #1a3a6e 100%);
  padding: 96px 0 80px;
  text-align: center;
}
.hero-title {
  font-size: clamp(32px, 5vw, 56px);
  font-weight: 700;
  color: var(--white);
  margin-bottom: 16px;
  line-height: 1.15;
}
.gold {
  color: var(--gold);
}
.hero-sub {
  font-size: 18px;
  color: rgba(255, 255, 255, 0.72);
  max-width: 600px;
  margin: 0 auto 32px;
  line-height: 1.7;
}
.hero-actions {
  display: flex;
  gap: 14px;
  justify-content: center;
  flex-wrap: wrap;
}

/* Stats */
.stats-bar {
  background: var(--white);
  border-bottom: 1px solid var(--gray-200);
}
.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  text-align: center;
  padding: 28px 0;
}
.stat {
  padding: 16px;
}
.stat-num {
  font-size: 32px;
  font-weight: 700;
  color: var(--navy);
  line-height: 1;
}
.stat-label {
  font-size: 13px;
  color: var(--gray-500);
  margin-top: 4px;
}

/* Cards */
.committee-card,
.program-card,
.post-card {
  transition:
    box-shadow 0.2s,
    transform 0.2s;
  cursor: pointer;
}
.committee-card:hover,
.program-card:hover,
.post-card:hover {
  box-shadow: var(--shadow-lg);
  transform: translateY(-3px);
}
.committee-icon {
  font-size: 28px;
  margin-bottom: 10px;
}
.committee-card h3,
.program-card h3,
.post-card h3 {
  font-size: 16px;
  font-weight: 600;
  color: var(--navy);
  margin-bottom: 8px;
}
.committee-card p,
.program-card p,
.post-card p {
  font-size: 13px;
  color: var(--gray-500);
  line-height: 1.6;
}
.post-date {
  font-size: 12px;
  color: var(--gold);
  font-weight: 500;
  margin-bottom: 6px;
}

@media (max-width: 600px) {
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}
</style>

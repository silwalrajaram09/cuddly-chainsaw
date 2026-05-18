<template>
  <div class="admin-shell">
    <!-- ── Sidebar ── -->
    <aside class="sidebar" :class="{ open: sidebarOpen }">
      <div class="sidebar-brand">
        <span class="brand-text">Admin</span>
      </div>

      <nav class="sidebar-nav">
        <p class="nav-section">Main</p>
        <RouterLink to="/admin" exact-active-class="active">
          <span class="icon">⬛</span> Dashboard
        </RouterLink>

        <p class="nav-section">Manage</p>
        <RouterLink to="/admin/committees" active-class="active">
          <span class="icon">🏛</span> Committees
        </RouterLink>
        <RouterLink to="/admin/programs" active-class="active">
          <span class="icon">📋</span> Programs
        </RouterLink>
        <RouterLink to="/admin/members" active-class="active">
          <span class="icon">👥</span> Members
        </RouterLink>

        <p class="nav-section">Content</p>
        <RouterLink to="/admin/pages" active-class="active">
          <span class="icon">📄</span> Pages
        </RouterLink>
        <RouterLink to="/admin/posts" active-class="active">
          <span class="icon">📰</span> Posts
        </RouterLink>
      </nav>

      <div class="sidebar-footer">
        <RouterLink to="/" target="_blank" class="view-site">
          ↗ View Site
        </RouterLink>
        <button class="logout-btn" @click="handleLogout">Sign Out</button>
      </div>
    </aside>

    <!-- Overlay for mobile sidebar -->
    <div
      v-if="sidebarOpen"
      class="sidebar-overlay"
      @click="sidebarOpen = false"
    />

    <!-- ── Main content ── -->
    <div class="admin-content">
      <!-- Top bar -->
      <header class="topbar">
        <button
          class="sidebar-toggle"
          @click="sidebarOpen = !sidebarOpen"
          aria-label="Toggle sidebar"
        >
          ☰
        </button>
        <div class="topbar-right">
          <div class="user-chip">
            <div class="user-avatar">{{ initials }}</div>
            <div class="user-info">
              <p class="user-name">{{ auth.userName }}</p>
              <p class="user-role">Administrator</p>
            </div>
          </div>
        </div>
      </header>

      <!-- Router view -->
      <main class="admin-main">
        <RouterView />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { RouterLink, RouterView, useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";

const auth = useAuthStore();
const router = useRouter();
const sidebarOpen = ref(false);

const initials = computed(() => {
  const name = auth.userName;
  if (!name) return "A";
  return name
    .split(" ")
    .map((n) => n[0])
    .join("")
    .toUpperCase()
    .slice(0, 2);
});

async function handleLogout() {
  await auth.logoutUser();
  router.push({ name: "login" });
}
</script>

<style scoped>
.admin-shell {
  display: flex;
  min-height: 100vh;
  background: var(--gray-100);
}

/* Sidebar */
.sidebar {
  width: 240px;
  flex-shrink: 0;
  background: var(--navy);
  display: flex;
  flex-direction: column;
  position: sticky;
  top: 0;
  height: 100vh;
  z-index: 200;
  transition: transform 0.25s;
}
.sidebar-brand {
  display: flex;
  align-items: baseline;
  gap: 6px;
  padding: 22px 20px 16px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}
.brand-dsp {
  font-size: 20px;
  font-weight: 700;
  color: var(--gold);
  letter-spacing: 0.06em;
}
.brand-text {
  font-size: 12px;
  font-weight: 300;
  color: rgba(255, 255, 255, 0.5);
  letter-spacing: 0.2em;
  text-transform: uppercase;
}

.sidebar-nav {
  flex: 1;
  overflow-y: auto;
  padding: 12px 0;
}
.nav-section {
  color: rgba(255, 255, 255, 0.28);
  font-size: 10px;
  font-weight: 600;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  padding: 14px 20px 4px;
}
.sidebar-nav a {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 20px;
  color: rgba(255, 255, 255, 0.65);
  font-size: 14px;
  font-weight: 500;
  transition:
    background 0.15s,
    color 0.15s;
  border-radius: 0;
}
.sidebar-nav a:hover {
  background: rgba(255, 255, 255, 0.06);
  color: var(--white);
}
.sidebar-nav a.active {
  background: rgba(201, 150, 60, 0.15);
  color: var(--gold);
  border-right: 3px solid var(--gold);
}
.icon {
  font-size: 15px;
  width: 18px;
  text-align: center;
}

.sidebar-footer {
  padding: 16px 20px;
  border-top: 1px solid rgba(255, 255, 255, 0.08);
  display: flex;
  flex-direction: column;
  gap: 8px;
}
.view-site {
  color: rgba(255, 255, 255, 0.45);
  font-size: 12px;
  padding: 6px 0;
}
.view-site:hover {
  color: var(--gold);
}
.logout-btn {
  background: rgba(239, 68, 68, 0.12);
  border: 1px solid rgba(239, 68, 68, 0.25);
  color: #fca5a5;
  padding: 8px 14px;
  border-radius: var(--radius);
  font-size: 13px;
  font-weight: 500;
  text-align: center;
  width: 100%;
  transition: background 0.15s;
}
.logout-btn:hover {
  background: rgba(239, 68, 68, 0.22);
}

.sidebar-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.4);
  z-index: 199;
  display: none;
}

/* Admin main */
.admin-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  min-width: 0;
}

/* Topbar */
.topbar {
  background: var(--white);
  border-bottom: 1px solid var(--gray-200);
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 24px;
  position: sticky;
  top: 0;
  z-index: 100;
  box-shadow: var(--shadow);
}
.sidebar-toggle {
  background: none;
  border: none;
  font-size: 22px;
  color: var(--gray-500);
  display: none;
  padding: 4px;
}
.topbar-right {
  display: flex;
  align-items: center;
  gap: 16px;
}
.user-chip {
  display: flex;
  align-items: center;
  gap: 10px;
}
.user-avatar {
  width: 36px;
  height: 36px;
  background: var(--navy);
  color: var(--white);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 13px;
  font-weight: 600;
}
.user-name {
  font-size: 14px;
  font-weight: 600;
  color: var(--gray-800);
  line-height: 1.2;
}
.user-role {
  font-size: 11px;
  color: var(--gray-400);
}

.admin-main {
  flex: 1;
  padding: 28px 28px;
}

@media (max-width: 900px) {
  .sidebar {
    position: fixed;
    top: 0;
    left: 0;
    height: 100%;
    transform: translateX(-100%);
  }
  .sidebar.open {
    transform: translateX(0);
  }
  .sidebar-overlay {
    display: block;
  }
  .sidebar-toggle {
    display: block;
  }
}
@media (max-width: 600px) {
  .admin-main {
    padding: 16px;
  }
}
</style>

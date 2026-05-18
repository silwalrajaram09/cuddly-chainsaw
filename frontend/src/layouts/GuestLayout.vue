<template>
  <!-- Announcement Bar -->
  <div class="marquee">
    <div class="marquee-content">
      Welcome to InfoTech • Latest updates • Programs • Committees • News
    </div>
  </div>

  <div class="guest-shell">
    <!-- Navbar -->
    <header class="navbar">
      <div class="container">
        <div class="navbar-inner">
          <!-- Brand -->
          <RouterLink to="/" class="brand">
            <span class="logo">IT</span>
            <span class="brand-dsp">Info</span>
            <span class="brand-text">Tech</span>
          </RouterLink>

          <!-- Desktop Navigation -->
          <nav class="nav-links">
            <!-- Committee Dropdown -->
            <div
              class="committee-dropdown"
              @mouseenter="committeeOpen = true"
              @mouseleave="committeeOpen = false"
            >
              <button class="committee-btn">
                Committees

                <svg
                  class="dropdown-arrow"
                  :class="{ rotate: committeeOpen }"
                  width="16"
                  height="16"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                >
                  <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
              </button>

              <Transition name="fade">
                <div v-if="committeeOpen" class="committee-menu">
                  <p v-if="loading" class="menu-state">Loading...</p>

                  <p v-else-if="error" class="menu-state error">
                    {{ error }}
                  </p>

                  <RecursiveMenu v-else :items="committees" />
                </div>
              </Transition>
            </div>

            <RouterLink to="/programs"> Programs </RouterLink>

            <RouterLink to="/members"> Members </RouterLink>

            <RouterLink to="/posts"> News </RouterLink>
          </nav>

          <!-- Mobile Toggle -->
          <button class="hamburger" @click="menuOpen = !menuOpen">
            <span></span>
            <span></span>
            <span></span>
          </button>
        </div>

        <!-- Mobile Menu -->
        <Transition name="slide-down">
          <nav v-if="menuOpen" class="mobile-menu">
            <div class="mobile-committee">
              <p class="mobile-heading">Committees</p>

              <RecursiveMenu :items="committees" />
            </div>

            <RouterLink to="/programs" @click="menuOpen = false">
              Programs
            </RouterLink>

            <RouterLink to="/members" @click="menuOpen = false">
              Members
            </RouterLink>

            <RouterLink to="/posts" @click="menuOpen = false">
              News
            </RouterLink>
          </nav>
        </Transition>
      </div>
    </header>

    <!-- Main -->
    <main class="guest-main">
      <RouterView />
    </main>

    <!-- Footer -->
    <footer class="footer">
      <div class="container">
        <div class="footer-grid">
          <div>
            <div class="brand footer-brand">
              <span class="brand-dsp">Info</span>

              <span class="brand-text"> Tech </span>
            </div>

            <p class="footer-description">
              Spreading positive and spiritual messages around the world through
              technology and community.
            </p>
          </div>

          <div>
            <p class="footer-heading">Quick Links</p>

            <RouterLink to="/programs"> Programs </RouterLink>

            <RouterLink to="/members"> Members </RouterLink>

            <RouterLink to="/posts"> News </RouterLink>
          </div>

          <div>
            <p class="footer-heading">Committees</p>

            <RouterLink to="/committees"> All Committees </RouterLink>
          </div>

          <div>
            <p class="footer-heading">Contact</p>

            <p class="footer-contact">Kathmandu, Nepal</p>
          </div>
        </div>

        <div class="footer-bottom">
          © {{ new Date().getFullYear() }}
          InfoTech. All rights reserved.
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";

import { RouterLink, RouterView } from "vue-router";

import RecursiveMenu from "@/components/RecursiveMenu.vue";

const menuOpen = ref(false);
const committeeOpen = ref(false);

const committees = ref([]);

const loading = ref(false);

const error = ref("");

const fetchCommittees = async () => {
  loading.value = true;

  try {
    const response = await axios.get("http://localhost:8000/api/committees");

    // Convert object into array
    committees.value = response.data.data;
  } catch (err) {
    console.error(err);

    error.value = "Failed to load committees.";
  } finally {
    loading.value = false;
  }
};

onMounted(fetchCommittees);
</script>

<style scoped>
/* Global-like resets for Shell */
* {
  box-sizing: border-box;
}

.guest-shell {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  background: var(--gray-50);
}

.guest-main {
  flex: 1;
}

.container {
  width: 100%;
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 24px;
}

/* Announcement Bar */
.marquee {
  overflow: hidden;
  white-space: nowrap;
  background: var(--navy);
  color: var(--white);
  padding: 8px 0;
  font-size: 13px;
  font-weight: 500;
  letter-spacing: 0.05em;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.marquee-content {
  display: inline-block;
  padding-left: 100%;
  animation: marquee-scroll 24s linear infinite;
}

.marquee:hover .marquee-content {
  animation-play-state: paused;
}

@keyframes marquee-scroll {
  from {
    transform: translateX(0%);
  }
  to {
    transform: translateX(-100%);
  }
}

/* Navbar */
.navbar {
  position: sticky;
  top: 0;
  z-index: 1000;
  backdrop-filter: blur(12px);
  background: rgba(255, 255, 255, 0.85);
  border-bottom: 1px solid var(--gray-200);
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
}

.navbar-inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 72px;
}

/* Logo */
.logo {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 34px;
  height: 34px;
  background: var(--navy);
  color: var(--white);
  font-size: 16px;
  font-weight: 800;
  border-radius: 6px;
}

.brand {
  display: flex;
  align-items: center;
  gap: 8px;
  text-decoration: none;
}

.brand-dsp {
  font-size: 20px;
  font-weight: 800;
  color: var(--navy);
}

.brand-text {
  color: var(--gold);
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.15em;
  text-transform: uppercase;
}

/* Desktop Navigation */
.nav-links {
  display: flex;
  align-items: center;
  gap: 28px;
}

.nav-links a,
.committee-btn {
  position: relative;
  background: none;
  border: none;
  color: var(--gray-600);
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  text-decoration: none;
  transition: color 0.2s ease;
  padding: 6px 0;
  display: flex;
  align-items: center;
  gap: 4px;
}

.nav-links a::after,
.committee-btn::after {
  content: "";
  position: absolute;
  left: 0;
  bottom: 0;
  width: 0%;
  height: 2px;
  background: var(--gold);
  transition: width 0.2s ease;
}

.nav-links a:hover::after,
.committee-btn:hover::after,
.nav-links a.router-link-active::after {
  width: 100%;
}

.nav-links a:hover,
.committee-btn:hover {
  color: var(--navy);
}

.dropdown-arrow {
  transition: transform 0.2s ease;
  color: var(--gray-400);
}

.dropdown-arrow.rotate {
  transform: rotate(180deg);
  color: var(--navy);
}

/* Committee Dropdown Menu */
.committee-dropdown {
  position: relative;
}

.committee-menu {
  position: absolute;
  top: calc(100% + 8px);
  left: 0;
  min-width: 280px;
  background: var(--white);
  border-radius: var(--radius-sm);
  padding: 12px;
  border: 1px solid var(--gray-200);
  box-shadow: var(--shadow-lg);
  z-index: 1010;
}

.menu-state {
  padding: 8px 12px;
  font-size: 13px;
  color: var(--gray-500);
}

.error {
  color: var(--danger);
}

/* Hamburger */
.hamburger {
  display: none;
  flex-direction: column;
  gap: 5px;
  background: none;
  border: none;
  cursor: pointer;
  padding: 4px;
}

.hamburger span {
  width: 22px;
  height: 2px;
  background: var(--navy);
  border-radius: 2px;
  transition: all 0.2s ease;
}

/* Mobile Menu */
.mobile-menu {
  display: flex;
  flex-direction: column;
  padding: 12px 18px;
  background: var(--white);
  border-bottom: 1px solid var(--gray-200);
  box-shadow: var(--shadow-md);
}

.mobile-menu a {
  color: var(--gray-700);
  text-decoration: none;
  padding: 10px 0;
  font-size: 14px;
  font-weight: 500;
  border-bottom: 1px solid var(--gray-100);
}

.mobile-menu a:last-child {
  border-bottom: none;
}

.mobile-committee {
  padding: 10px 0;
  border-bottom: 1px solid var(--gray-100);
}

.mobile-heading {
  color: var(--gray-400);
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  margin-bottom: 8px;
}

/* Footer */
.footer {
  background: var(--gray-100);
  border-top: 1px solid var(--gray-200);
  margin-top: auto;
}

.footer-grid {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr 1fr;
  gap: 40px;
  padding: 48px 0 32px;
}

.footer-brand {
  margin-bottom: 12px;
}

.footer-heading {
  color: var(--navy);
  margin-bottom: 16px;
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
}

.footer-description {
  color: var(--gray-600);
  font-size: 14px;
  line-height: 1.6;
  max-width: 320px;
}

.footer-contact,
.footer a {
  color: var(--gray-600);
  font-size: 14px;
  line-height: 1.7;
  text-decoration: none;
  transition: color 0.2s ease;
  display: block;
}

.footer a {
  margin-bottom: 8px;
}

.footer a:hover {
  color: var(--gold);
}

.footer-bottom {
  border-top: 1px solid var(--gray-200);
  padding: 20px 0;
  color: var(--gray-500);
  text-align: center;
  font-size: 13px;
}

/* Transitions */
.fade-enter-active,
.fade-leave-active {
  transition: all 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(-4px);
}

.slide-down-enter-active,
.slide-down-leave-active {
  transition: all 0.2s ease;
}

.slide-down-enter-from,
.slide-down-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}

/* Responsive */
@media (max-width: 768px) {
  .nav-links {
    display: none;
  }

  .hamburger {
    display: flex;
  }

  .footer-grid {
    grid-template-columns: 1fr 1fr;
    gap: 32px;
  }
}

@media (max-width: 540px) {
  .footer-grid {
    grid-template-columns: 1fr;
    gap: 24px;
  }

  .navbar-inner {
    height: 64px;
  }

  .container {
    padding: 0 16px;
  }
}
</style>

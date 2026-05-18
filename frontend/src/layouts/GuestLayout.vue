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
/* Global */
* {
  box-sizing: border-box;
}

html {
  scroll-behavior: smooth;
}

/* Layout */
.guest-shell {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.guest-main {
  flex: 1;
}

.container {
  width: 100%;
  max-width: 1280px;
  margin: 0 auto;
  padding: 0 24px;
}

/* Announcement */
.marquee {
  overflow: hidden;
  white-space: nowrap;

  background: linear-gradient(90deg, #0f172a, #1e293b);

  color: #facc15;

  padding: 10px 0;

  font-size: 14px;
  font-weight: 500;
}

.marquee-content {
  display: inline-block;
  padding-left: 100%;
  animation: marquee-scroll 18s linear infinite;
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

  backdrop-filter: blur(14px);

  background: rgba(236, 237, 240, 0.88);

  border-bottom: 1px solid rgba(255, 255, 255, 0.06);

  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.18);
  color: #0000;
}

.navbar-inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 72px;
}
.logo {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  background: navy;
  color: white;
  font-size: 24px;
  font-weight: 800;
  border: 2px solid #facc15;
  border-radius: 8px;
}

/* Brand */
.brand {
  display: flex;
  align-items: baseline;
  gap: 6px;

  text-decoration: none;
}

.brand-dsp {
  font-size: 24px;
  font-weight: 800;
  color: blue;
}

.brand-text {
  color: blue;

  font-size: 14px;

  letter-spacing: 0.18em;

  text-transform: uppercase;
}

/* Desktop Navigation */
.nav-links {
  display: flex;
  align-items: center;
  gap: 30px;
}

.nav-links a,
.committee-btn {
  position: relative;
  background: none;
  border: none;
  color: black;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  text-decoration: none;
  transition: all 0.25s ease;
}

.nav-links a::after,
.committee-btn::after {
  content: "";
  position: absolute;
  left: 0;
  bottom: -6px;
  width: 0%;
  height: 2px;
  background: #facc15;
  transition: width 0.25s ease;
}
.nav-links a:hover::after,
.committee-btn:hover::after,
.nav-links a.router-link-active::after {
  width: 100%;
}
.nav-links a:hover,
.committee-btn:hover {
  color: rgb(27, 25, 25);
}
/* Dropdown */
.committee-dropdown {
  position: relative;
}
.dropdown-arrow {
  transition: transform 0.2s ease;
}

.dropdown-arrow.rotate {
  transform: rotate(180deg);
}
.committee-menu {
  position: absolute;
  top: calc(100% + 14px);
  left: 0;
  min-width: 300px;
  background: rgba(231, 228, 228, 0.97);
  backdrop-filter: blur(12px);
  border-radius: 18px;
  padding: 14px;
  border: 1px solid rgba(255, 255, 255, 0.5);
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.18);
}
.menu-state {
  padding: 12px;
  font-size: 14px;
}

.error {
  color: red;
}
/* Hamburger */
.hamburger {
  display: none;

  flex-direction: column;
  gap: 5px;

  background: none;
  border: none;

  cursor: pointer;
}

.hamburger span {
  width: 24px;
  height: 2px;

  background: white;

  border-radius: 20px;
}

/* Mobile Menu */
.mobile-menu {
  display: flex;
  flex-direction: column;

  padding: 18px;

  background: rgba(15, 23, 42, 0.96);

  backdrop-filter: blur(14px);

  border-radius: 0 0 18px 18px;
}

.mobile-menu a {
  color: rgba(255, 255, 255, 0.84);

  text-decoration: none;

  padding: 12px 0;

  border-bottom: 1px solid rgba(255, 255, 255, 0.06);
}

.mobile-heading {
  color: #facc15;
  margin-bottom: 10px;
}

.mobile-login {
  color: #facc15 !important;
}

/* Footer */
.footer {
  background: linear-gradient(to bottom, #0f172a, #020617);

  margin-top: auto;
}

.footer-grid {
  display: grid;

  grid-template-columns: 2fr 1fr 1fr 1fr;

  gap: 40px;

  padding: 60px 0 40px;
}

.footer-heading {
  color: white;

  margin-bottom: 16px;

  font-size: 13px;

  font-weight: 700;

  text-transform: uppercase;

  letter-spacing: 0.08em;
}

.footer-description,
.footer-contact,
.footer a {
  color: #94a3b8;

  line-height: 1.7;

  text-decoration: none;
}

.footer a:hover {
  color: #facc15;
}

.footer-bottom {
  border-top: 1px solid rgba(255, 255, 255, 0.08);

  padding: 18px 0;

  color: #64748b;

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
  transform: translateY(-6px);
}

.slide-down-enter-active,
.slide-down-leave-active {
  transition: all 0.3s ease;
}

.slide-down-enter-from,
.slide-down-leave-to {
  opacity: 0;
  transform: translateY(-10px);
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
  }
}

@media (max-width: 540px) {
  .footer-grid {
    grid-template-columns: 1fr;
  }

  .navbar-inner {
    height: 66px;
  }

  .container {
    padding: 0 16px;
  }
}
</style>

<template>
  <div class="marquee">
    <div class="marquee-content">This is the some news from this site .</div>
  </div>
  <div class="guest-shell">
    <header class="navbar">
      <div class="container">
        <div class="navbar-inner">
          <!-- Brand -->
          <RouterLink to="/" class="brand">
            <span class="brand-dsp">Info</span>
            <span class="brand-text">Tech</span>
          </RouterLink>

          <!-- Desktop Navigation -->
          <nav class="nav-links">
            <!-- Committees Dropdown -->
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
                  width="12"
                  height="12"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
              </button>

              <div v-if="committeeOpen" class="committee-menu">
                <RecursiveMenu :items="committees" />
              </div>
            </div>

            <RouterLink to="/programs"> Programs </RouterLink>

            <RouterLink to="/members"> Members </RouterLink>

            <RouterLink to="/posts"> News </RouterLink>

            <RouterLink to="/login" class="btn btn-primary btn-sm">
              Admin Login
            </RouterLink>
          </nav>

          <!-- Mobile Hamburger -->
          <button
            class="hamburger"
            @click="menuOpen = !menuOpen"
            aria-label="Menu"
          >
            <span></span>
            <span></span>
            <span></span>
          </button>
        </div>

        <!-- ───────────────── Mobile Menu ───────────────── -->
        <Transition name="slide-down">
          <nav v-if="menuOpen" class="mobile-menu">
            <!-- Mobile Committees -->
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

            <RouterLink
              to="/login"
              @click="menuOpen = false"
              class="mobile-login"
            >
              Admin Login
            </RouterLink>
          </nav>
        </Transition>
      </div>
    </header>

    <!-- ───────────────── Main Content ───────────────── -->
    <main class="guest-main">
      <RouterView />
    </main>

    <!-- ───────────────── Footer ───────────────── -->
    <footer class="footer">
      <div class="container">
        <div class="footer-grid">
          <div>
            <div class="brand" style="margin-bottom: 10px">
              <span class="brand-dsp">Info</span>

              <span class="brand-text" style="color: #a0aec0"> Tech </span>
            </div>

            <p class="footer-description">
             Spreading the positive and spritual messsage around the world .
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
          <p>
            © {{ new Date().getFullYear() }}
            Info tech..All rights reserved.
          </p>
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

const fetchCommittees = async () => {
  try {
    const response = await axios.get("http://localhost:8000/api/committees");

    committees.value = response.data.data;
  } catch (error) {
    console.error("Failed to fetch committees:", error);
  }
};

onMounted(() => {
  fetchCommittees();
});
</script>

<style scoped>
.marquee {
  overflow: hidden;
  white-space: nowrap;
  background: var(--navy);
  padding: 8px 0;
  color: rgb(200, 24, 24);
}

.marquee-content {
  display: inline-block;
  padding-left: 100%;
  animation: scroll-left 10s linear infinite;
}

@keyframes scroll-left {
  0% {
    transform: translateX(0%);
  }
  100% {
    transform: translateX(-100%);
  }
}
.guest-shell {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.guest-main {
  flex: 1;
}

.navbar {
  position: sticky;
  top: 0;
  background: var(--navy);
  z-index: 100;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25);
}

.navbar-inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 64px;
}

/* Brand */
.brand {
  display: flex;
  align-items: baseline;
  gap: 6px;
  text-decoration: none;
}

.brand-dsp {
  font-size: 22px;
  font-weight: 700;
  color: var(--gold);
  letter-spacing: 0.06em;
}

.brand-text {
  font-size: 14px;
  font-weight: 300;
  color: var(--white);
  letter-spacing: 0.18em;
  text-transform: uppercase;
}

.nav-links {
  display: flex;
  align-items: center;
  gap: 28px;
}

.nav-links a {
  color: rgba(255, 255, 255, 0.78);
  font-size: 14px;
  font-weight: 500;
  letter-spacing: 0.03em;
  transition: color 0.15s ease;
  text-decoration: none;
}

.nav-links a:hover,
.nav-links a.router-link-active {
  color: var(--white);
}

.committee-dropdown {
  position: relative;
}

.committee-btn {
  background: none;
  border: none;
  color: rgba(255, 255, 255, 0.78);
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: color 0.15s ease;
  display: flex;
  align-items: center;
  gap: 6px;
}

.committee-btn:hover {
  color: var(--white);
}

.dropdown-arrow {
  transition: transform 0.2s ease;
}

.dropdown-arrow.rotate {
  transform: rotate(180deg);
}

.committee-menu {
  position: absolute;
  top: calc(100% + 2px);
  left: 0;
  background: white;
  border-radius: 14px;
  padding: 10px;
  min-width: 260px;
  box-shadow: 0 14px 40px rgba(0, 0, 0, 0.18);
  z-index: 999;
}

.hamburger {
  display: none;
  flex-direction: column;
  gap: 5px;
  background: none;
  border: none;
  padding: 4px;
  cursor: pointer;
}

.hamburger span {
  width: 22px;
  height: 2px;
  background: var(--white);
  border-radius: 2px;
}

/* Mobile Menu Animation */
.slide-down-enter-active,
.slide-down-leave-active {
  transition: all 0.3s ease;
}

.slide-down-enter-from,
.slide-down-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

.mobile-menu {
  display: flex;
  flex-direction: column;
  padding: 12px 0 18px;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.mobile-menu a {
  color: rgba(255, 255, 255, 0.8);
  padding: 10px 0;
  font-size: 15px;
  font-weight: 500;
  text-decoration: none;
  border-bottom: 1px solid rgba(255, 255, 255, 0.06);
}

.mobile-login {
  color: var(--gold) !important;
}

/* Mobile Committee */
.mobile-committee {
  padding: 12px 0;
}

.mobile-heading {
  color: var(--gold);
  font-size: 14px;
  font-weight: 600;
  margin-bottom: 10px;
}

/* ───────────────── Footer ───────────────── */
.footer {
  background: var(--navy);
  margin-top: auto;
}

.footer-grid {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr 1fr;
  gap: 32px;
  padding: 48px 0 32px;
}

.footer-heading {
  color: var(--white);
  font-size: 12px;
  font-weight: 600;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  margin-bottom: 12px;
}

.footer-description,
.footer-contact {
  color: #718096;
  font-size: 13px;
  line-height: 1.7;
}

.footer a {
  display: block;
  color: #718096;
  font-size: 13px;
  margin-bottom: 8px;
  text-decoration: none;
  transition: color 0.15s;
}

.footer a:hover {
  color: var(--gold);
}

.footer-bottom {
  border-top: 1px solid rgba(255, 255, 255, 0.08);
  padding: 16px 0;
  color: #4a5568;
  font-size: 12px;
  text-align: center;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

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

@media (max-width: 480px) {
  .footer-grid {
    grid-template-columns: 1fr;
  }
}
</style>

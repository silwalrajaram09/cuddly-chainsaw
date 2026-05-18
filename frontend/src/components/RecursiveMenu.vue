<template>
  <ul class="menu-list">
    <li
      v-for="item in items"
      :key="item.id"
      class="menu-item"
      @mouseenter="handleMouseEnter(item.id)"
      @mouseleave="handleMouseLeave"
    >
      <!-- Menu Row -->
      <div class="menu-row">
        <RouterLink :to="`/committees/${item.id}`" class="menu-link">
          {{ item.name }}
        </RouterLink>

        <!-- Expand Button with Arrow Icon -->
        <button
          v-if="item.children?.length"
          class="toggle-btn"
          @click.stop="toggleItem(item.id)"
        >
          <svg
            class="toggle-icon"
            :class="{ rotated: openItem === item.id }"
            width="14"
            height="14"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
          >
            <polyline points="9 18 15 12 9 6"></polyline>
          </svg>
        </button>
      </div>

      <!-- Recursive Submenu -->
      <Transition name="submenu-fade">
        <div
          v-if="item.children?.length && openItem === item.id"
          ref="submenuRef"
          class="submenu"
          :class="{
            'submenu-left': shouldOpenLeft,
          }"
        >
          <RecursiveMenu :items="item.children" />
        </div>
      </Transition>
    </li>
  </ul>
</template>

<script setup>
import { ref, computed, nextTick, watch, onMounted, onUnmounted } from "vue";
import RecursiveMenu from "./RecursiveMenu.vue";

const props = defineProps({
  items: {
    type: Array,
    required: true,
  },
});

/* ----------------------------------------
   Responsive Detection
---------------------------------------- */

const screenWidth = ref(window.innerWidth);

const updateWidth = () => {
  screenWidth.value = window.innerWidth;
};

onMounted(() => {
  window.addEventListener("resize", updateWidth);
});

onUnmounted(() => {
  window.removeEventListener("resize", updateWidth);
});

const isDesktop = computed(() => screenWidth.value >= 768);

/* ----------------------------------------
   Open Menu State
---------------------------------------- */

const openItem = ref(null);

const handleMouseEnter = (id) => {
  if (isDesktop.value) {
    openItem.value = id;
  }
};

const handleMouseLeave = () => {
  if (isDesktop.value) {
    openItem.value = null;
  }
};

const toggleItem = (id) => {
  if (!isDesktop.value) {
    openItem.value = openItem.value === id ? null : id;
  }
};

/* ----------------------------------------
   Smart Submenu Positioning
---------------------------------------- */

const submenuRef = ref(null);
const shouldOpenLeft = ref(false);

watch(openItem, async (newValue, oldValue) => {
  if (!newValue) return;

  await nextTick();

  // Get all submenu elements and check the last one
  const submenuElements = document.querySelectorAll(".submenu");
  const lastSubmenu = submenuElements[submenuElements.length - 1];

  if (lastSubmenu) {
    const rect = lastSubmenu.getBoundingClientRect();
    shouldOpenLeft.value = rect.right + 10 > window.innerWidth;
  }
});
</script>

<style scoped>
/* ----------------------------------------
   Base Menu
---------------------------------------- */

.menu-list {
  list-style: none;
  margin: 0;
  padding: 0;
  min-width: 240px;
}

.menu-item {
  position: relative;
}

/* ----------------------------------------
   Menu Row
---------------------------------------- */

.menu-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 4px;
}

/* ----------------------------------------
   Menu Link
---------------------------------------- */

.menu-link {
  flex: 1;
  display: flex;
  align-items: center;
  padding: 10px 14px;
  color: #2d3748;
  text-decoration: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  transition: all 0.18s ease;
}

.menu-link:hover {
  background: #f7fafc;
  color: #c9a227;
}

/* ----------------------------------------
   Toggle Button
---------------------------------------- */

.toggle-btn {
  border: none;
  background: none;
  width: 32px;
  height: 32px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #718096;
  border-radius: 6px;
  transition: all 0.15s ease;
  flex-shrink: 0;
}
.toggle-btn:hover {
  background: #edf2f7;
  color: #c9a227;
}
.toggle-icon {
  transition: transform 0.2s ease;
}
.toggle-icon.rotated {
  transform: rotate(90deg);
}
.submenu {
  background: white;
  border-radius: 14px;
  padding: 8px;
  min-width: 240px;
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
}

/* Desktop Positioning */
@media (min-width: 768px) {
  .submenu {
    position: absolute;
    top: 0;
    left: calc(100% + 6px);
    z-index: 999;
  }

  /* Smart overflow prevention */
  .submenu-left {
    left: auto;
    right: calc(100% + 6px);
  }

  /* Hide mobile expand buttons on desktop */
  .toggle-btn {
    display: none;
  }
}

/* Mobile Layout */
@media (max-width: 767px) {
  .menu-list {
    min-width: 100%;
  }

  .submenu {
    position: relative;
    margin-top: 6px;
    margin-left: 14px;
    border-left: 2px solid #e2e8f0;
    box-shadow: none;
  }
}

/* ----------------------------------------
   Animations
---------------------------------------- */

.submenu-fade-enter-active,
.submenu-fade-leave-active {
  transition: all 0.2s ease;
}

.submenu-fade-enter-from,
.submenu-fade-leave-to {
  opacity: 0;
  transform: translateY(-6px);
}
</style>

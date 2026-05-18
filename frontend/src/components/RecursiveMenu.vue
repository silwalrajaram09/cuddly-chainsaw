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
          :aria-label="openItem === item.id ? 'Collapse' : 'Expand'"
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
          :ref="(el) => setSubmenuRef(el, item.id)"
          class="submenu"
          :class="{
            'submenu-left': submenuPositions[item.id]?.openLeft,
            'submenu-top': submenuPositions[item.id]?.openTop,
          }"
        >
          <RecursiveMenu :items="item.children" />
        </div>
      </Transition>
    </li>
  </ul>
</template>

<script setup>
import { ref, computed, nextTick, onMounted, onUnmounted } from "vue";
import RecursiveMenu from "./RecursiveMenu.vue";

const props = defineProps({
  items: {
    type: Array,
    required: true,
  },
  depth: {
    type: Number,
    default: 0,
  },
});

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
const openItem = ref(null);
const submenuRefs = ref({});
const submenuPositions = ref({});

const setSubmenuRef = (el, id) => {
  if (el) {
    submenuRefs.value[id] = el;
  }
};

const handleMouseEnter = (id) => {
  if (isDesktop.value) {
    openItem.value = id;
    calculatePosition(id);
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
    if (openItem.value === id) {
      setTimeout(() => calculatePosition(id), 50);
    }
  }
};
const calculatePosition = async (itemId) => {
  await nextTick();

  const submenuEl = submenuRefs.value[itemId];
  if (!submenuEl) return;

  // Find the parent menu item
  const parentMenuItem = submenuEl.closest(".menu-item");
  if (!parentMenuItem) return;

  const submenuRect = submenuEl.getBoundingClientRect();
  const parentRect = parentMenuItem.getBoundingClientRect();
  const viewportWidth = window.innerWidth;
  const viewportHeight = window.innerHeight;

  let openLeft = false;
  let openTop = false;

  // Check horizontal overflow (right side)
  const spaceOnRight = viewportWidth - parentRect.right;
  const spaceOnLeft = parentRect.left;
  const submenuWidth = submenuRect.width;

  // Decide which side to open
  if (spaceOnRight < submenuWidth + 20 && spaceOnLeft > submenuWidth + 20) {
    openLeft = true; // Open to the left
  } else if (
    spaceOnRight < submenuWidth + 20 &&
    spaceOnLeft < submenuWidth + 20
  ) {
    // Not enough space on either side, open to the right but adjust position
    openLeft = false;
  }

  // Check vertical overflow
  const spaceBelow = viewportHeight - parentRect.bottom;
  const spaceAbove = parentRect.top;
  const submenuHeight = submenuRect.height;

  if (spaceBelow < submenuHeight + 20 && spaceAbove > submenuHeight + 20) {
    openTop = true; // Open upward
  }

  submenuPositions.value[itemId] = { openLeft, openTop };

  // Apply inline styles for precise positioning if needed
  if (openTop) {
    submenuEl.style.top = "auto";
    submenuEl.style.bottom = "0";
  } else {
    submenuEl.style.top = "0";
    submenuEl.style.bottom = "auto";
  }
};

// Recalculate position when window is resized
const handleResize = () => {
  if (openItem.value) {
    calculatePosition(openItem.value);
  }
};

onMounted(() => {
  window.addEventListener("resize", handleResize);
  window.addEventListener("scroll", () => {
    if (openItem.value) {
      calculatePosition(openItem.value);
    }
  });
});

onUnmounted(() => {
  window.removeEventListener("resize", handleResize);
});
</script>

<style scoped>
.menu-list {
  list-style: none;
  margin: 0;
  padding: 0;
  min-width: 240px;
}

.menu-item {
  position: relative;
}

.menu-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 4px;
}

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

  /* Smart overflow prevention - open to the left */
  .submenu-left {
    left: auto;
    right: calc(100% + 6px);
  }

  /* Open upward instead of downward */
  .submenu-top {
    top: auto;
    bottom: 0;
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

  /* Reset positioning for mobile */
  .submenu-left,
  .submenu-top {
    left: auto;
    right: auto;
    top: auto;
    bottom: auto;
  }
}

/* Animations */
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

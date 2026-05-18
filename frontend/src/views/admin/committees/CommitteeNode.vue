<template>
  <div class="node" :style="{ marginLeft: `${depth * 20}px` }">
    <!-- Row -->
    <div class="node-row" :class="{ 'has-children': hasChildren }">
      <!-- Toggle -->
      <button
        v-if="hasChildren"
        class="toggle"
        @click="expanded = !expanded"
        :aria-label="expanded ? 'Collapse' : 'Expand'"
      >
        <svg
          width="12"
          height="12"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="3"
        >
          <polyline
            :points="expanded ? '6 9 12 15 18 9' : '9 18 15 12 9 6'"
          ></polyline>
        </svg>
      </button>

      <div v-else class="toggle-placeholder"></div>

      

      <!-- Name -->
      <div class="node-info">
        <span class="node-name">{{ node.name }}</span>
        
      </div>

      <!-- Actions -->
      <div class="actions">
        <button
          class="action-btn add"
          @click="$emit('create-child', node)"
          title="Add Subcommittee"
        >
          + Child
        </button>
        <button
          class="action-btn edit"
          @click="$emit('edit', node)"
          title="Edit"
        >
          edit
        </button>
        <button
          class="action-btn delete"
          @click="$emit('delete', node)"
          title="Delete"
        >
          del
        </button>
      </div>
    </div>

    <!-- Children -->
    <Transition name="slide">
      <div v-if="hasChildren && expanded" class="children">
        <CommitteeNode
          v-for="child in node.children"
          :key="child.id"
          :node="child"
          :depth="depth + 1"
          @create-child="$emit('create-child', $event)"
          @edit="$emit('edit', $event)"
          @delete="$emit('delete', $event)"
        />
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed } from "vue";

const props = defineProps({
  node: {
    type: Object,
    required: true,
  },
  depth: {
    type: Number,
    default: 0,
  },
});

defineEmits(["create-child", "edit", "delete"]);

const expanded = ref(true);

const hasChildren = computed(
  () => props.node.children && props.node.children.length > 0,
);
</script>

<style scoped>
.node {
  margin-bottom: 8px;
}

.node-row {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  transition: all 0.2s ease;
}

.node-row:hover {
  border-color: #cbd5e1;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.node-row.has-children {
  border-left: 3px solid #d4af37;
}

.toggle {
  width: 28px;
  height: 28px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: none;
  border-radius: 6px;
  background: #f1f5f9;
  cursor: pointer;
  transition: background 0.2s;
}

.toggle:hover {
  background: #e2e8f0;
}

.toggle-placeholder {
  width: 28px;
}

.node-icon {
  font-size: 18px;
}

.node-info {
  flex: 1;
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
}

.node-name {
  font-weight: 600;
  color: #1e293b;
}

.child-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 22px;
  height: 22px;
  padding: 0 6px;
  background: #d4af37;
  color: #1e293b;
  font-size: 11px;
  font-weight: 700;
  border-radius: 11px;
}

.actions {
  display: flex;
  gap: 6px;
}

.action-btn {
  border: none;
  padding: 6px 10px;
  border-radius: 6px;
  font-size: 12px;
  cursor: pointer;
  transition: all 0.2s;
}

.action-btn.add {
  background: #dbeafe;
  color: #1e40af;
}

.action-btn.add:hover {
  background: #bfdbfe;
}

.action-btn.edit {
  background: #f1f5f9;
  color: #475569;
}

.action-btn.edit:hover {
  background: #e2e8f0;
}

.action-btn.delete {
  background: #fee2e2;
  color: #b91c1c;
}

.action-btn.delete:hover {
  background: #fecaca;
}

.children {
  margin-top: 8px;
  border-left: 2px solid #e2e8f0;
  margin-left: 14px;
  padding-left: 8px;
}

/* Animations */
.slide-enter-active,
.slide-leave-active {
  transition: all 0.2s ease;
}

.slide-enter-from,
.slide-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

/* Responsive */
@media (max-width: 768px) {
  .node-row {
    flex-wrap: wrap;
  }

  .actions {
    width: 100%;
    justify-content: flex-end;
  }

  .children {
    margin-left: 10px;
  }
}
</style>

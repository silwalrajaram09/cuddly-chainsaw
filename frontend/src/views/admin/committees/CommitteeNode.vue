<template>
  <div class="node">
    <!-- Row -->
    <div class="node-row">
      <!-- Toggle -->
      <button v-if="hasChildren" class="toggle" @click="expanded = !expanded">
        {{ expanded ? "−" : "+" }}
      </button>

      <span v-else class="toggle-placeholder"></span>

      <!-- Name -->
      <span class="node-name">
        {{ node.name }}
      </span>

      <!-- Actions -->
      <div class="actions">
        <button @click="$emit('create-child', node)">+ Child</button>

        <button @click="$emit('edit', node)">Edit</button>

        <button class="danger" @click="$emit('delete', node)">Delete</button>
      </div>
    </div>

    <!-- Children -->
    <div v-if="hasChildren && expanded" class="children">
      <CommitteeNode
        v-for="child in node.children"
        :key="child.id"
        :node="child"
        @create-child="$emit('create-child', $event)"
        @edit="$emit('edit', $event)"
        @delete="$emit('delete', $event)"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from "vue";
import CommitteeNode from "./CommitteeNode.vue";

const props = defineProps({
  node: {
    type: Object,
    required: true,
  },
});

defineEmits(["create-child", "edit", "delete"]);

const expanded = ref(true);

const hasChildren = computed(
  () => props.node.children && props.node.children.length,
);
</script>

<style scoped>
.node {
  margin-top: 10px;
}

.node-row {
  display: flex;
  align-items: center;

  gap: 10px;

  padding: 10px 14px;

  border: 1px solid #e2e8f0;

  border-radius: 8px;

  background: white;
}

.toggle {
  width: 28px;
  height: 28px;

  border: none;

  border-radius: 6px;

  background: #edf2f7;

  cursor: pointer;
}

.toggle-placeholder {
  width: 28px;
}

.node-name {
  flex: 1;

  font-weight: 600;
}

.actions {
  display: flex;
  gap: 6px;
}

.actions button {
  border: none;

  padding: 6px 10px;

  border-radius: 6px;

  cursor: pointer;

  background: #edf2f7;
}

.actions .danger {
  background: #fee2e2;

  color: #b91c1c;
}

.children {
  margin-left: 30px;

  margin-top: 10px;

  border-left: 2px solid #e2e8f0;

  padding-left: 12px;
}

@media (max-width: 768px) {
  .node-row {
    flex-wrap: wrap;
  }

  .actions {
    width: 100%;
  }
}
</style>

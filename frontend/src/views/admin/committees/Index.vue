<template>
  <div>
    <!-- Header -->
    <div class="header">
      <h1>Committees</h1>

      <button class="create-btn" @click="openCreate(null)">
        + New Committee
      </button>
    </div>

    <!-- Loading -->
    <div v-if="loading">Loading...</div>

    <!-- Empty -->
    <div v-else-if="!committees.length">No committees found.</div>

    <!-- Tree -->
    <div v-else>
      <CommitteeNode
        v-for="node in committees"
        :key="node.id"
        :node="node"
        @create-child="openCreate"
        @edit="openEdit"
        @delete="openDelete"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";

import CommitteeNode from "./CommitteeNode.vue";

import { getCommittees } from "@/api/committees";

const committees = ref([]);

const loading = ref(true);

async function load() {
  loading.value = true;

  try {
    const { data } = await getCommittees();

    committees.value = data.data ?? data;
  } finally {
    loading.value = false;
  }
}

function openCreate(node) {
  console.log("create child", node);
}

function openEdit(node) {
  console.log("edit", node);
}

function openDelete(node) {
  console.log("delete", node);
}

onMounted(load);
</script>

<style scoped>
.header {
  display: flex;

  justify-content: space-between;

  align-items: center;

  margin-bottom: 20px;
}

.create-btn {
  border: none;

  background: #1e3a8a;

  color: white;

  padding: 10px 16px;

  border-radius: 8px;

  cursor: pointer;
}
</style>

<template>
  <div class="not-found">
    <div class="container">
      <h1>404</h1>
      <p>Page not found.</p>
      <RouterLink to="/" class="btn btn-primary">Go Home</RouterLink>
    </div>
  </div>
</template>

<script setup>
import { RouterLink } from "vue-router";
</script>

<style scoped>
.not-found {
  min-height: 60vh;
  display: flex;
  align-items: center;
  justify-content: center;
}
.not-found h1 {
  font-size: 56px;
  color: var(--navy);
  margin-bottom: 8px;
}
.not-found p {
  color: var(--gray-500);
  margin-bottom: 16px;
}
</style>

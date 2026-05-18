<template>
  <div class="login-page">
    <div class="login-brand">
      <span class="brand-dsp">Info</span>
      <span class="brand-text">Tech</span>
    </div>

    <div class="login-card">
      <div class="login-card-header">
        <h1>Admin Sign In</h1>
        <p>Enter your credentials to access the dashboard.</p>
      </div>

      <form @submit.prevent="submit" class="login-form">
        <div v-if="error" class="alert alert-error">{{ error }}</div>

        <div class="form-group">
          <label for="email">Email address</label>
          <input
            id="email"
            v-model="form.email"
            type="email"
            class="form-control"
            placeholder="admin@infoTech.com"
            autocomplete="email"
            required
          />
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input
            id="password"
            v-model="form.password"
            type="password"
            class="form-control"
            placeholder="••••••••"
            autocomplete="current-password"
            required
          />
        </div>

        <button
          type="submit"
          class="btn btn-primary w-full login-btn"
          :disabled="loading"
        >
          <span
            v-if="loading"
            class="spinner"
            style="width: 16px; height: 16px; border-width: 2px"
          ></span>
          {{ loading ? "Signing in…" : "Sign In" }}
        </button>
      </form>
    </div>

    <RouterLink to="/" class="back-link">← Back to website</RouterLink>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter, useRoute, RouterLink } from "vue-router";
import { useAuthStore } from "@/stores/auth";

const auth = useAuthStore();
const router = useRouter();
const route = useRoute();

const form = ref({ email: "", password: "" });
const error = ref(null);
const loading = ref(false);

async function submit() {
  loading.value = true;
  error.value = null;
  try {
    await auth.loginUser(form.value);
    const redirect = route.query.redirect || "/admin";
    router.push(redirect);
  } catch (e) {
    error.value =
      e.response?.data?.message || "Invalid credentials. Please try again.";
  } finally {
    loading.value = false;
  }
}
</script>

<style scoped>
.login-page {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, var(--navy) 0%, #1a3a6e 100%);
  padding: 24px;
}
.login-brand {
  display: flex;
  align-items: baseline;
  gap: 8px;
  margin-bottom: 28px;
}
.brand-dsp {
  font-size: 32px;
  font-weight: 700;
  color: var(--gold);
  letter-spacing: 0.06em;
}
.brand-text {
  font-size: 16px;
  font-weight: 300;
  color: rgba(255, 255, 255, 0.7);
  letter-spacing: 0.2em;
  text-transform: uppercase;
}

.login-card {
  background: var(--white);
  border-radius: 14px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  width: 100%;
  max-width: 400px;
  overflow: hidden;
}
.login-card-header {
  padding: 28px 28px 0;
}
.login-card-header h1 {
  font-size: 22px;
  font-weight: 700;
  color: var(--navy);
  margin-bottom: 4px;
}
.login-card-header p {
  color: var(--gray-500);
  font-size: 14px;
}

.login-form {
  padding: 24px 28px 28px;
}
.login-btn {
  margin-top: 8px;
  justify-content: center;
  padding: 12px;
  font-size: 15px;
}

.back-link {
  margin-top: 20px;
  color: rgba(255, 255, 255, 0.45);
  font-size: 13px;
  transition: color 0.15s;
}
.back-link:hover {
  color: rgba(255, 255, 255, 0.8);
}
</style>

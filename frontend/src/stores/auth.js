import { defineStore } from "pinia";
import {
  login as loginApi,
  logout as logoutApi,
//   me as meApi,
} from "@/api/auth";

export const useAuthStore = defineStore("auth", {
  state: () => ({
    token: localStorage.getItem("token") || null,
    user: null,
  }),
  getters: {
    isAuthenticated: (state) => !!state.token,
    userName: (state) => state.user?.name || state.user?.email || "",
  },
  actions: {
    async loginUser(credentials) {
      const { data } = await loginApi(credentials);
      // expected: { token, user } (backend may vary)
      const token = data?.token || data?.access_token || data?.data?.token;
      const user = data?.user || data?.data?.user || data?.userData;
      if (!token) throw new Error("Authentication token missing");

      this.token = token;
      localStorage.setItem("token", token);
      this.user = user || null;

      // If user isn't returned, hydrate it
      if (!this.user) {
        await this.fetchUser();
      }
      return this.user;
    },

    async fetchUser() {
    //   const { data } = await meApi();
      // expected: user object
    //   this.user = data?.data || data || null;
    //   return this.user;
    },

    async logoutUser() {
      try {
        await logoutApi();
      } finally {
        this.token = null;
        this.user = null;
        localStorage.removeItem("token");
      }
    },
  },
});

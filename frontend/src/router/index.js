import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "@/stores/auth";

const routes = [
  // ── Auth ────────────────────────────────────────────────
  {
    path: "/login",
    name: "login",
    component: () => import("@/views/Login.vue"),
    meta: { guestOnly: true },
  },

  // ── Guest / Public ───────────────────────────────────────
  {
    path: "/",
    component: () => import("@/layouts/GuestLayout.vue"),
    children: [
      {
        path: "",
        name: "home",
        component: () => import("@/views/guest/Home.vue"),
      },
      {
        path: "committees",
        name: "committees",
        component: () => import("@/views/guest/CommitteeList.vue"),
      },
      {
        path: "committees/:id",
        name: "committee-detail",
        component: () => import("@/views/guest/CommitteeDetail.vue"),
      },
      {
        path: "programs",
        name: "programs",
        component: () => import("@/views/guest/ProgramList.vue"),
      },
      {
        path: "programs/:id",
        name: "program-detail",
        component: () => import("@/views/guest/ProgramDetail.vue"),
      },
      {
        path: "members",
        name: "members",
        component: () => import("@/views/guest/MemberList.vue"),
      },
      {
        path: "members/:id",
        name: "member-detail",
        component: () => import("@/views/guest/MemberDetail.vue"),
      },
      {
        path: "pages/:id",
        name: "page",
        component: () => import("@/views/guest/PageView.vue"),
      },
      {
        path: "posts",
        name: "posts",
        component: () => import("@/views/guest/PostList.vue"),
      },
      {
        path: "posts/:id",
        name: "post-detail",
        component: () => import("@/views/guest/PostDetail.vue"),
      },
    ],
  },

  // ── Admin (all protected) ────────────────────────────────
  {
    path: "/admin",
    component: () => import("@/layouts/AdminLayout.vue"),
    meta: { requiresAuth: true },
    children: [
      {
        path: "",
        name: "dashboard",
        component: () => import("@/views/admin/Dashboard.vue"),
      },
      // Committees
      {
        path: "committees",
        name: "admin-committees",
        component: () => import("@/views/admin/committees/Index.vue"),
      },
      // Programs
      {
        path: "programs",
        name: "admin-programs",
        component: () => import("@/views/admin/programs/Index.vue"),
      },
      // Members
      {
        path: "members",
        name: "admin-members",
        component: () => import("@/views/admin/members/Index.vue"),
      },
      {
        path: "members/:id",
        name: "admin-member-detail",
        component: () => import("@/views/admin/members/Detail.vue"),
      },
      // Pages
      {
        path: "pages",
        name: "admin-pages",
        component: () => import("@/views/admin/pages/Index.vue"),
      },
      // Posts
      {
        path: "posts",
        name: "admin-posts",
        component: () => import("@/views/admin/posts/Index.vue"),
      },
    ],
  },

  // ── 404 ─────────────────────────────────────────────────
  {
    path: "/:pathMatch(.*)*",
    name: "not-found",
    component: () => import("@/views/NotFound.vue"),
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior: () => ({ top: 0 }),
});

// ── Navigation Guard ─────────────────────────────────────
router.beforeEach(async (to, from) => {
  const auth = useAuthStore();

  // Hydrate user from token if not yet loaded
  if (auth.token && !auth.user) {
    await auth.fetchUser();
  }

  // Redirect unauthenticated users away from protected routes
  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return {
      name: "login",
      query: { redirect: to.fullPath },
    };
  }

  // Redirect authenticated admin away from login page
  if (to.meta.guestOnly && auth.isAuthenticated) {
    return { name: "dashboard" };
  }

  // allow navigation
  return true;
});

export default router;

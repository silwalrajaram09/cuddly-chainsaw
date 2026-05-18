<template>
  <div>
    <div class="page-header">
      <h1>Dashboard</h1>
      <p>Welcome back,admin</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-4 mb-6">
      <div class="stat-card card" v-for="s in statCards" :key="s.label">
        <div class="card-body stat-body">
          <div class="stat-icon" :style="{ background: s.bg }">{{ s.icon }}</div>
          <div>
            <p class="stat-num">{{ s.value }}</p>
            <p class="stat-label">{{ s.label }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick links -->
    <div class="grid grid-2 gap-6">
      <div class="card">
        <div class="card-header"><h2>Quick Actions</h2></div>
        <div class="card-body quick-links">
          <RouterLink to="/admin/committees" class="ql-item">
            <span class="ql-icon">🏛</span>
            <div>
              <p>Manage Committees</p>
              <small>Create, edit or delete committees</small>
            </div>
            <span class="ql-arrow">→</span>
          </RouterLink>
          <RouterLink to="/admin/programs" class="ql-item">
            <span class="ql-icon">📋</span>
            <div>
              <p>Manage Programs</p>
              <small>Manage investment programs</small>
            </div>
            <span class="ql-arrow">→</span>
          </RouterLink>
          <RouterLink to="/admin/members" class="ql-item">
            <span class="ql-icon">👥</span>
            <div>
              <p>Manage Members</p>
              <small>Add members and assign roles</small>
            </div>
            <span class="ql-arrow">→</span>
          </RouterLink>
          <RouterLink to="/admin/posts" class="ql-item">
            <span class="ql-icon">📰</span>
            <div>
              <p>Manage Posts</p>
              <small>Publish news and updates</small>
            </div>
            <span class="ql-arrow">→</span>
          </RouterLink>
          <RouterLink to="/admin/pages" class="ql-item">
            <span class="ql-icon">📄</span>
            <div>
              <p>Manage Pages</p>
              <small>Edit static site pages</small>
            </div>
            <span class="ql-arrow">→</span>
          </RouterLink>
        </div>
      </div>

      <div class="card">
        <div class="card-header"><h2>Logged in as</h2></div>
        <div class="card-body">
          <div class="user-profile">
            <div class="profile-avatar">{{ initials }}</div>
            <div>
              <p class="profile-name">{{ auth.userName }}</p>
              <p class="profile-email">{{ auth.userEmail }}</p>
              <span class="badge badge-blue" style="margin-top:6px">Administrator</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { getCommittees } from '@/api/committees'
import { getPrograms } from '@/api/programs'
import { getMembers } from '@/api/members'
import { getPosts } from '@/api/posts'

const auth = useAuthStore()

const counts = ref({ committees: 0, programs: 0, members: 0, posts: 0 })

const statCards = computed(() => [
  { label: 'Committees', value: counts.value.committees, icon: '🏛', bg: '#dbeafe' },
  { label: 'Programs',   value: counts.value.programs,   icon: '📋', bg: '#fef3c7' },
  { label: 'Members',    value: counts.value.members,    icon: '👥', bg: '#dcfce7' },
  { label: 'Posts',      value: counts.value.posts,      icon: '📰', bg: '#ede9fe' },
])

const initials = computed(() => {
  const name = auth.userName
  if (!name) return 'A'
  return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
})

onMounted(async () => {
  const results = await Promise.allSettled([
    getCommittees(), getPrograms(), getMembers(), getPosts()
  ])
  const keys = ['committees', 'programs', 'members', 'posts']
  results.forEach((r, i) => {
    if (r.status === 'fulfilled') {
      const d = r.value.data
      counts.value[keys[i]] = d.total ?? (d.data ?? d).length
    }
  })
})
</script>

<style scoped>
.page-header { margin-bottom: 28px; }
.page-header h1 { font-size: 26px; font-weight: 700; color: var(--navy); }
.page-header p  { color: var(--gray-500); margin-top: 2px; }

.stat-body  { display: flex; align-items: center; gap: 16px; }
.stat-icon  { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0; }
.stat-num   { font-size: 28px; font-weight: 700; color: var(--navy); line-height: 1; }
.stat-label { font-size: 13px; color: var(--gray-500); margin-top: 2px; }

.quick-links { display: flex; flex-direction: column; gap: 4px; padding: 16px 24px; }
.ql-item {
  display: flex; align-items: center; gap: 14px;
  padding: 12px 10px; border-radius: var(--radius);
  transition: background .15s;
  color: var(--gray-700);
}
.ql-item:hover { background: var(--gray-50); }
.ql-icon  { font-size: 20px; width: 32px; text-align: center; }
.ql-item p { font-size: 14px; font-weight: 500; color: var(--navy); line-height: 1.2; }
.ql-item small { font-size: 12px; color: var(--gray-400); }
.ql-arrow { margin-left: auto; color: var(--gray-300); font-size: 18px; }
.ql-item:hover .ql-arrow { color: var(--gold); }

.user-profile { display: flex; gap: 18px; align-items: center; }
.profile-avatar {
  width: 64px; height: 64px; border-radius: 50%;
  background: var(--navy); color: var(--white);
  display: flex; align-items: center; justify-content: center;
  font-size: 22px; font-weight: 700; flex-shrink: 0;
}
.profile-name  { font-size: 17px; font-weight: 600; color: var(--navy); }
.profile-email { font-size: 13px; color: var(--gray-400); margin-top: 2px; }
</style>

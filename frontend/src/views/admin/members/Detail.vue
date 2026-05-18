<template>
  <div>
    <!-- Back -->
    <RouterLink to="/admin/members" class="back-link">← Back to Members</RouterLink>

    <div v-if="loading" class="loading-center" style="min-height:50vh"><div class="spinner"></div></div>

    <div v-else-if="member">
      <!-- Member header -->
      <div class="member-header card mb-6">
        <div class="card-body member-hero">
          <div class="big-avatar">{{ initials(member.name) }}</div>
          <div>
            <h1>{{ member.name }}</h1>
            <p>{{ member.email }}</p>
          </div>
        </div>
      </div>

      <div class="grid grid-2 gap-6">

        <!-- ── PROGRAMS ────────────────────────────── -->
        <div class="card">
          <div class="card-header">
            <h2>Programs</h2>
            <button class="btn btn-primary btn-sm" @click="showAttachProgram = true">
              + Attach
            </button>
          </div>
          <div v-if="!memberPrograms.length" class="empty-state" style="padding:32px">
            <p>No programs attached yet.</p>
          </div>
          <div v-else class="table-wrap">
            <table>
              <thead>
                <tr><th>Program</th><th>Action</th></tr>
              </thead>
              <tbody>
                <tr v-for="p in memberPrograms" :key="p.id">
                  <td>{{ p.name }}</td>
                  <td>
                    <button
                      class="btn btn-danger btn-xs"
                      :disabled="detachingProgram === p.id"
                      @click="handleDetachProgram(p.id)"
                    >
                      {{ detachingProgram === p.id ? '…' : 'Detach' }}
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- ── COMMITTEES ─────────────────────────── -->
        <div class="card">
          <div class="card-header">
            <h2>Committees</h2>
            <button class="btn btn-primary btn-sm" @click="showAttachCommittee = true">
              + Attach
            </button>
          </div>
          <div v-if="!memberCommittees.length" class="empty-state" style="padding:32px">
            <p>No committees attached yet.</p>
          </div>
          <div v-else class="table-wrap">
            <table>
              <thead>
                <tr><th>Committee</th><th>Order</th><th>Actions</th></tr>
              </thead>
              <tbody>
                <tr v-for="c in memberCommittees" :key="c.id">
                  <td>{{ c.name }}</td>
                  <td>
                    <input
                      type="number" min="1"
                      class="order-input"
                      :value="c.pivot?.order ?? ''"
                      @change="handleUpdateOrder(c.id, $event.target.value)"
                    />
                  </td>
                  <td>
                    <button
                      class="btn btn-danger btn-xs"
                      :disabled="detachingCommittee === c.id"
                      @click="handleDetachCommittee(c.id)"
                    >
                      {{ detachingCommittee === c.id ? '…' : 'Detach' }}
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </div>

      <!-- Attach Program Modal -->
      <div class="modal-overlay" v-if="showAttachProgram" @mousedown.self="showAttachProgram = false">
        <div class="modal">
          <div class="modal-header">
            <h3>Attach Program</h3>
            <button class="modal-close" @click="showAttachProgram = false">✕</button>
          </div>
          <div class="modal-body">
            <div v-if="attachProgramError" class="alert alert-error">{{ attachProgramError }}</div>
            <div class="form-group">
              <label>Select Program</label>
              <select v-model="selectedProgram" class="form-control">
                <option value="">— choose —</option>
                <option
                  v-for="p in availablePrograms"
                  :key="p.id"
                  :value="p.id"
                >{{ p.name }}</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-outline" @click="showAttachProgram = false">Cancel</button>
            <button
              class="btn btn-primary"
              :disabled="!selectedProgram || attachingProgram"
              @click="handleAttachProgram"
            >
              <span v-if="attachingProgram" class="spinner" style="width:14px;height:14px;border-width:2px"></span>
              {{ attachingProgram ? 'Attaching…' : 'Attach' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Attach Committee Modal -->
      <div class="modal-overlay" v-if="showAttachCommittee" @mousedown.self="showAttachCommittee = false">
        <div class="modal">
          <div class="modal-header">
            <h3>Attach Committee</h3>
            <button class="modal-close" @click="showAttachCommittee = false">✕</button>
          </div>
          <div class="modal-body">
            <div v-if="attachCommitteeError" class="alert alert-error">{{ attachCommitteeError }}</div>
            <div class="form-group">
              <label>Select Committee</label>
              <select v-model="selectedCommittee" class="form-control">
                <option value="">— choose —</option>
                <option
                  v-for="c in availableCommittees"
                  :key="c.id"
                  :value="c.id"
                >{{ c.name }}</option>
              </select>
            </div>
            <div class="form-group">
              <label>Order (optional)</label>
              <input v-model.number="committeeOrder" type="number" min="1" class="form-control" placeholder="e.g. 1" />
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-outline" @click="showAttachCommittee = false">Cancel</button>
            <button
              class="btn btn-primary"
              :disabled="!selectedCommittee || attachingCommittee"
              @click="handleAttachCommittee"
            >
              <span v-if="attachingCommittee" class="spinner" style="width:14px;height:14px;border-width:2px"></span>
              {{ attachingCommittee ? 'Attaching…' : 'Attach' }}
            </button>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { RouterLink, useRoute } from 'vue-router'
import { getMember } from '@/api/members'
import { getPrograms, attachProgram, detachProgram } from '@/api/programs'
import { getCommittees, attachCommittee, detachCommittee, updateCommitteeOrder } from '@/api/committees'

const route  = useRoute()
const member = ref(null)
const loading = ref(true)

// All available lists
const allPrograms    = ref([])
const allCommittees  = ref([])

// Attach Program
const showAttachProgram   = ref(false)
const selectedProgram     = ref('')
const attachingProgram    = ref(false)
const detachingProgram    = ref(null)
const attachProgramError  = ref(null)

// Attach Committee
const showAttachCommittee   = ref(false)
const selectedCommittee     = ref('')
const committeeOrder        = ref('')
const attachingCommittee    = ref(false)
const detachingCommittee    = ref(null)
const attachCommitteeError  = ref(null)

function initials(name) {
  if (!name) return '?'
  return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
}

// Member's currently attached programs & committees
const memberPrograms    = computed(() => member.value?.programs    ?? [])
const memberCommittees  = computed(() => member.value?.committees  ?? [])

// Programs not yet attached
const availablePrograms = computed(() => {
  const attached = new Set(memberPrograms.value.map(p => p.id))
  return allPrograms.value.filter(p => !attached.has(p.id))
})

// Committees not yet attached
const availableCommittees = computed(() => {
  const attached = new Set(memberCommittees.value.map(c => c.id))
  return allCommittees.value.filter(c => !attached.has(c.id))
})

async function loadMember() {
  const { data } = await getMember(route.params.id)
  member.value = data
}

async function handleAttachProgram() {
  attachingProgram.value   = true
  attachProgramError.value = null
  try {
    await attachProgram(route.params.id, { program_id: selectedProgram.value })
    showAttachProgram.value = false
    selectedProgram.value   = ''
    await loadMember()
  } catch (e) {
    attachProgramError.value = e.response?.data?.message ?? 'Attach failed.'
  } finally { attachingProgram.value = false }
}

async function handleDetachProgram(programId) {
  detachingProgram.value = programId
  try {
    await detachProgram(route.params.id, programId)
    await loadMember()
  } catch (e) {
    alert(e.response?.data?.message ?? 'Detach failed.')
  } finally { detachingProgram.value = null }
}

async function handleAttachCommittee() {
  attachingCommittee.value   = true
  attachCommitteeError.value = null
  try {
    const payload = { committee_id: selectedCommittee.value }
    if (committeeOrder.value) payload.order = committeeOrder.value
    await attachCommittee(route.params.id, payload)
    showAttachCommittee.value = false
    selectedCommittee.value   = ''
    committeeOrder.value      = ''
    await loadMember()
  } catch (e) {
    attachCommitteeError.value = e.response?.data?.message ?? 'Attach failed.'
  } finally { attachingCommittee.value = false }
}

async function handleDetachCommittee(committeeId) {
  detachingCommittee.value = committeeId
  try {
    await detachCommittee(route.params.id, committeeId)
    await loadMember()
  } catch (e) {
    alert(e.response?.data?.message ?? 'Detach failed.')
  } finally { detachingCommittee.value = null }
}

async function handleUpdateOrder(committeeId, order) {
  try {
    await updateCommitteeOrder(route.params.id, committeeId, { order: parseInt(order) })
    await loadMember()
  } catch (e) {
    alert(e.response?.data?.message ?? 'Update failed.')
  }
}

onMounted(async () => {
  loading.value = true
  try {
    const [, p, c] = await Promise.all([
      loadMember(),
      getPrograms(),
      getCommittees(),
    ])
    allPrograms.value   = p.data.data ?? p.data
    allCommittees.value = c.data.data ?? c.data
  } finally { loading.value = false }
})
</script>

<style scoped>
.back-link {
  display: inline-block;
  color: var(--gray-500); font-size: 13px;
  margin-bottom: 20px;
  transition: color .15s;
}
.back-link:hover { color: var(--navy); }
.member-hero { display: flex; align-items: center; gap: 20px; }
.big-avatar {
  width: 72px; height: 72px; border-radius: 50%;
  background: var(--navy); color: var(--white);
  display: flex; align-items: center; justify-content: center;
  font-size: 26px; font-weight: 700; flex-shrink: 0;
}
.member-hero h1 { font-size: 22px; font-weight: 700; color: var(--navy); }
.member-hero p  { color: var(--gray-500); font-size: 14px; }
.order-input {
  width: 64px; padding: 4px 8px;
  border: 1.5px solid var(--gray-300); border-radius: 6px;
  font-size: 13px; text-align: center;
}
.order-input:focus { outline: none; border-color: var(--navy); }
</style>

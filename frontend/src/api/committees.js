import api from './axios'

// ── Guest (public) ──────────────────────────────────────
export const getCommittees = (params = {}) => api.get('/committees', { params })
export const getCommittee  = (id)          => api.get(`/committees/${id}`)

// ── Admin (protected) ───────────────────────────────────
export const createCommittee = (data)       => api.post('/committees', data)
export const updateCommittee = (id, data)   => api.put(`/committees/${id}`, data)
export const deleteCommittee = (id)         => api.delete(`/committees/${id}`)

// ── Member ↔ Committee (attach / detach / reorder) ──────
export const attachCommittee      = (memberId, data)                   => api.post(`/members/${memberId}/committees`, data)
export const updateCommitteeOrder = (memberId, committeeId, data)      => api.patch(`/members/${memberId}/committees/${committeeId}`, data)
export const detachCommittee      = (memberId, committeeId)            => api.delete(`/members/${memberId}/committees/${committeeId}`)
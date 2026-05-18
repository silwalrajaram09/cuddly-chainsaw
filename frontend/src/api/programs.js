import api from './axios'

// ── Guest (public) ──────────────────────────────────────
export const getPrograms = (params = {}) => api.get('/programs', { params })
export const getProgram  = (id)          => api.get(`/programs/${id}`)

// ── Admin (protected) ───────────────────────────────────
export const createProgram = (data)     => api.post('/programs', data)
export const updateProgram = (id, data) => api.put(`/programs/${id}`, data)
export const deleteProgram = (id)       => api.delete(`/programs/${id}`)

// ── Member ↔ Program (attach / detach) ──────────────────
export const attachProgram = (memberId, data)          => api.post(`/members/${memberId}/programs`, data)
export const detachProgram = (memberId, programId)     => api.delete(`/members/${memberId}/programs/${programId}`)
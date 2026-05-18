import api from './axios'

// ── Guest (public) ──────────────────────────────────────
export const getPages = (params = {}) => api.get('/pages', { params })
export const getPage  = (id)          => api.get(`/pages/${id}`)

// ── Admin (protected) ───────────────────────────────────
export const createPage = (data)     => api.post('/pages', data)
export const updatePage = (id, data) => api.put(`/pages/${id}`, data)
export const deletePage = (id)       => api.delete(`/pages/${id}`)
import api from './axios'

// ── Guest (public) ──────────────────────────────────────
export const getPosts = (params = {}) => api.get('/posts', { params })
export const getPost  = (id)          => api.get(`/posts/${id}`)

// ── Admin (protected) ───────────────────────────────────
export const createPost = (data)     => api.post('/posts', data)
export const updatePost = (id, data) => api.put(`/posts/${id}`, data)
export const deletePost = (id)       => api.delete(`/posts/${id}`)
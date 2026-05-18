import api from './axios'

export const login  = (credentials) => api.post('/admin-login', credentials)
export const logout = ()            => api.post('/logout')
// export const me     = ()            => api.get('/me')
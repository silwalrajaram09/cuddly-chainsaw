import api from "./axios";

// Guest (public) routes
export const getPosts = (params = {}) => api.get("/posts", { params });
export const getPost = (id) => api.get(`/posts/${id}`);

// Admin (protected) routes
export const createPost = (data) => {
  return api.post("/posts", data, {
    headers: {
      "Content-Type": "multipart/form-data",
    },
  });
};

export const updatePost = (id, data) => {
  return api.post(`/posts/${id}`, data, {
    headers: {
      "Content-Type": "multipart/form-data",
    },
  });
};

export const deletePost = (id) => api.delete(`/posts/${id}`);

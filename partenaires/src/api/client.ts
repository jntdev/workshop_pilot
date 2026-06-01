import axios from 'axios';

const client = axios.create({
    baseURL: import.meta.env.VITE_API_URL ?? '',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
});

client.interceptors.request.use((config) => {
    const token = localStorage.getItem('partenaire_token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

client.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            localStorage.removeItem('partenaire_token');
            localStorage.removeItem('partenaire_data');
            window.location.href = '/login';
        }
        return Promise.reject(error);
    }
);

export default client;

import axios from "axios";

const api = axios.create({
    baseURL: "http://127.0.0.1:8000/api",
    headers: { Accept: "application/json" }
});

api.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem('token');

        if(token){
            config.headers.Authorization = `Bearer ${token}`
        }

        return config
    }, 
    (error) => {
        return Promise.reject(error)
    }
)

//Token expirado ou inválido: limpa a sessão e volta para o login
api.interceptors.response.use(
    (response) => response,
    (error) => {
        const url = error.config?.url ?? '';
        if(error.response?.status === 401 && !url.includes('/login')){
            localStorage.removeItem('token');
            localStorage.removeItem('usuario');
            window.location.href = '/';
        }
        return Promise.reject(error)
    }
)

export default api;

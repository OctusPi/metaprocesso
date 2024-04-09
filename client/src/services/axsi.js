import axios from 'axios'
import auth from '@/stores/auth'

const axiosInstance = axios.create({
    baseURL: import.meta.env.VITE_URL_API
})

const axiosInstanceAuth = axios.create({
    baseURL: import.meta.env.VITE_URL_API,
    headers: {
        'Content-Type':'multipart/form-data',
        'Authorization': 'Bearer '+auth.token
    }
})

export default {
    axiosInstance,
    axiosInstanceAuth
}

import axios from 'axios'
// import jwt from '@/stores/jwt'

const axiosInstance = axios.create({
    baseURL: import.meta.env.VITE_URL_API,
    headers: {
        'Content-Type':'multipart/form-data',
        'Authorization': ''
    }
})

export default {
    axiosInstance
}
import axios from 'axios'

const axiosInstance = axios.create({
    baseURL: import.meta.env.VITE_URL_API,
    headers: {
        'Content-Type':'multipart/form-data'
    }
})

export default axiosInstance
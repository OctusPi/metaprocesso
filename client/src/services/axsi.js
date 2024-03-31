import axios from 'axios'
import {useJwt} from '@/stores/auth'

const auth = useJwt()

const axiosInstance = axios.create({
    baseURL: import.meta.env.VITE_URL_API,
    headers: {
        'Content-Type':'multipart/form-data',
        'Authorization': 'Bearer '+auth.token
    }
})

export default axiosInstance

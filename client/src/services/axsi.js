import axios from 'axios'
import auth from '@/stores/auth'
import organ from '@/stores/organ'

const axiosInstance = axios.create({
    baseURL: import.meta.env.VITE_URL_API,
    headers: {
        'Accept': 'application/json'
    }
})

const axiosInstanceAuth = axios.create({
    baseURL: import.meta.env.VITE_URL_API,
    headers: {
        'Accept': 'application/json',
        'Content-Type':'multipart/form-data',
        'Authorization': 'Bearer ' + auth.get_user()?.token,
        'X-Custom-Header-Organ': organ.get_organ()?.id
    }
})

export default {
    axiosInstance,
    axiosInstanceAuth
}

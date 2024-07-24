import axios from "axios"

const api = axios.create({
    baseURL: '',
});

const get = async (url) => {
    const response = await api.get(url)
    return response.data
}

const post = async (url, params, token = null) => {
    const response = await api.post(url, params, token)
    return response?.data
}

const getUser = () => {
    return get('/user')
}

const authenticate = (data) => {
    return post('/authenticate', data)
}

export {
    getUser,
    authenticate
}
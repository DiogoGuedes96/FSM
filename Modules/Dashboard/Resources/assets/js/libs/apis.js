import axios from "axios"

const token = localStorage.getItem('user') ? {
    'Content-Type': 'application/json',
    Authorization: `Bearer ${JSON.parse(localStorage.getItem('user')).token}`,
} : {};

const api = axios.create({
    baseURL: '',
    headers: token
});

const get = async (url) => {
    const response = await api.get(url)
    return response.data
}

const getKpis = ({ entity, period }) => {
    return get(`/api/dashboard/${entity}/kpis?period=${period}`);
}

export {
    getKpis
}
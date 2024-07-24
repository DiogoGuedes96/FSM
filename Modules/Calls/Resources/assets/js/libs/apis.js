import axios from "axios"
import { message as messageAntd } from 'antd';
import { end } from "@popperjs/core";

const token = localStorage.getItem('user') ? {
    'Content-Type': 'application/json',
    Authorization: `Bearer ${JSON.parse(localStorage.getItem('user')).token}`,
} : {};

const api = axios.create({
    baseURL: '',
    headers: token
});

api.interceptors.response.use(
    response => response,
    error => {
        if (error.response.status === 422) {
            Object.keys(error.response.data.errors)
                    .forEach(key => error.response.data.errors[key]
                        .forEach(messageTxt => messageAntd.error(messageTxt)));

            return Promise.reject(error)
        }

        return error;
    }
);

const get = async (url) => {
    const response = await api.get(url)
    return response.data
}

const post = async (url, params) => {
    const response = await api.post(url, params, token)
    return response?.data
}

const put = async (url, params, token = null) => {
    const response = await api.put(url, params)
    return response?.data
}

const getCallsInProgress = () => {
    return get('/api/calls/in-progress')
}

const getCallsHangup = (page = null) => {
    if(Number.isInteger(page)){
        return get(`/api/calls/hangup?page=${page}`)
    }
    return get(`/api/calls/hangup`)
}

const getCallsMissed = (page = null) => {
    if(Number.isInteger(page)){
        return get(`/api/calls/missed?page=${page}`)
    }
    return get(`/api/calls/missed`)
}

const getLastSalesAndProducts = (
    clientId,
    startDate = null,
    endDate = null
) => {
    let url = `/api/calls/orders/${clientId}`;

    const queryParams = {
        startDate: null,
        endDate: null,
    };
    
    if (startDate)
        queryParams['startDate'] = startDate;

    if (endDate)
        queryParams['endDate'] = endDate;

    const queryString = new URLSearchParams(queryParams).toString();

    if (queryString) {
        url += `?${queryString}`;
    }

    return get(url);
}

const terminateCall = ({ callId }) => {
    return put(`/api/calls/closeGhostCall/${callId}`)
}

const getCountMissedCalls = () => {
    return get(`/api/calls/count/missed`) 
}

const setMissedCallsViewed = (calls) => {
    return put(`/api/calls/set-viewed`, {
        calls: calls,
    });
}

export {
    getCallsInProgress,
    getCallsHangup,
    getCallsMissed,
    getLastSalesAndProducts,
    terminateCall,
    getCountMissedCalls,
    setMissedCallsViewed
}
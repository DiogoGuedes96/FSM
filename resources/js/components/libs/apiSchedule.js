import axios from "axios";
import { message as messageAntd } from 'antd';

const token = localStorage.getItem("user")
    ? {
          "Content-Type": "application/json",
          Authorization: `Bearer ${
              JSON.parse(localStorage.getItem("user")).token
          }`,
      }
    : {};

const api = axios.create({
    baseURL: "",
    headers: token,
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

const put = async (url, params) => {
    const response = await api.put(url, params);
    return response.data;
};

const del = async (url) => {
    const response = await api.delete(url);
    return response.data;
};

const get = async (url) => {
    const response = await api.get(url);
    return response.data;
};

const post = async (url, params) => {
    const response = await api.post(url, params);
    return response?.data;
};

const createBmsReminder = (data) => {
    return post("/api/schedule/event/reminder/create", data);
};

const getFilteredClients = (value) => {
    return post("/api/clients/filter", { searchInput: value });
};

const getEventsByDates = ({ days }) => {
    return post("/api/schedule/event/listByDates", { dates: days });
};

const deleteEvent = ({id, type}) => {
    return del(`/api/schedule/event/reminder/delete/${id}/${type}`);
};

const editBmsReminder = (data) => {
    return put("/api/schedule/event/reminder/edit", data);
};

const listCurrentMinute = (data = false) => {
    return get(`/api/schedule/event/remember/listCurrentMinute/${data}`, data);
};

const addRemenberDelay = (data) => {
    return post("/api/schedule/event/remember/delay", data);
};

const setDoneRemember = ({event_id}) => {
    return put(`/api/schedule/event/remember/done/${event_id}`);
};

export {
    createBmsReminder,
    getFilteredClients,
    getEventsByDates,
    deleteEvent,
    editBmsReminder,
    listCurrentMinute,
    addRemenberDelay,
    setDoneRemember
};

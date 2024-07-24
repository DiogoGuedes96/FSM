import axios from "axios";
import { message as messageAntd } from "antd";

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
    (response) => response,
    (error) => {
        if (error.response.status === 422) {
            Object.keys(error.response.data.errors).forEach((key) =>
                error.response.data.errors[key].forEach((messageTxt) =>
                    messageAntd.error(messageTxt)
                )
            );

            return Promise.reject(error);
        }

        return error;
    }
);

const get = async (url) => {
    const response = await api.get(url);
    return response.data;
};

const post = async (url, params) => {
    const response = await api.post(url, params);
    return response?.data;
};

const getProducts = (
    page = 1,
    perPage = 20,
    search = "",
    sorter = null,
    filters = null,
    status = null
) => {
    let params = { page, perPage };

    if (search && search.length > 2) params.search = search;

    if (sorter && sorter.field && sorter.sortDirection) {
        params.sort = sorter.field;
        params.sortDirection = sorter.sortDirection;
    }

    if (filters && filters.length > 0) {
        params.unit = filters.join(",");
    }

    if (status) {
        params.status = status;
    }

    let queryParams = new URLSearchParams(params).toString();

    const url = params
        ? `/api/products/list?${queryParams}`
        : `/api/products/list`;

    return get(url);
};

const getUnitsProducts = () => {
    return get(`/api/products/units`);
};

const saveProducts = (infos) => {
    return post(`/api/products/update`, infos);
};

const getSyncProducts = () => {
    return get(`/api/products/sync`);
};

const getSyncBatchs = () => {
    return get(`/api/products/batchs/sync`);
};

const getSyncClients = () => {
    return get(`/api/products/clients/sync`);
};

export {
    getProducts,
    saveProducts,
    getUnitsProducts,
    getSyncProducts,
    getSyncBatchs,
    getSyncClients,
};

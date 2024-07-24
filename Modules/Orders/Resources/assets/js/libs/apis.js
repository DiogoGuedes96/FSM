import axios from "axios";
import { message } from "laravel-mix/src/Log";
import { message as messageAntd } from "antd";

const token = localStorage.getItem("user")
    ? {
          "Content-Type": "application/json",
          Authorization: `Bearer ${
              JSON.parse(localStorage.getItem("user")).token
          }`,
      }
    : {};

const browserToken = localStorage.getItem("browserToken") ?? "";

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
        }
        
        return Promise.reject(error);
        // return error;
    }
);

const get = async (url) => {
    const response = await api.get(url);
    return response.data;
};

const download = async (url, fileName = `${new Date().getTime()}.pdf`) => {
    const token = localStorage.getItem("user")
        ? {
              Authorization: `Bearer ${
                  JSON.parse(localStorage.getItem("user")).token
              }`,
          }
        : {};

    const response = await axios.get(url, {
        responseType: "blob",
        headers: token,
    });

    const urlBlob = window.URL.createObjectURL(new Blob([response.data]));

    const link = document.createElement("a");
    link.href = urlBlob;
    link.setAttribute("download", fileName);

    document.body.appendChild(link);

    link.click();
    window.URL.revokeObjectURL(urlBlob);
};

const post = async (url, params, token = null) => {
    const response = await api.post(url, params);
    return response?.data;
};

const put = async (url, params, token = null) => {
    const response = await api.put(url, params);
    return response?.data;
};

const getAllProducts = ({ stockFilter = "inStock" }) => {
    return get(`/api/products/all/${stockFilter}`);
};

const getFilteredProducts = (filters) => {
    if (filters.page) {
        return post(`/api/products/filter?page=${filters.page}`, {
            category_code: filters.family,
            sub_category_code: filters.filterSubFamily,
            searchInput: filters.searchInput,
            group: filters.group,
            stock: filters.stock,
        });
    }
    return post(`/api/products/filter`, {
        category_code: filters.family,
        sub_category_code: filters.filterSubFamily,
        searchInput: filters.searchInput,
        group: filters.group,
    });
};

const getFilteredClients = (filters) => {
    return post("/api/clients/filter", { searchInput: filters.searchInput });
};

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

const getMostBoughtProducts = (data) => {
    return post(`/api/orders/client/mostBought/${data?.clientId}`, {
        startDate: data?.startDate ? data?.startDate : null,
        endDate: data?.endDate ? data?.endDate : null,
        numProducts: data?.numProducts,
    });
};

const getLessBoughtProducts = (data) => {
    return post(`/api/orders/client/lessBought/${data?.clientId}`, {
        startDate: data?.startDate ? data?.startDate : null,
        endDate: data?.endDate ? data?.endDate : null,
        numProducts: data?.numProducts,
    });
};

const saveNewOrder = (orderData) => {
    return post(`/api/orders/saveNewOrder`, {
        orderId: orderData?.orderId,
        isDirectSale: orderData?.isDirectSale ?? false,
        orderNotes: orderData?.orderNotes,
        bmsClient: orderData?.bmsClient,
        orderProducts: orderData?.orderProducts,
        callerPhone: orderData?.callerPhone,
    });
};

const updateOrderAndProducts = (orderData) => {
    return post(`/api/orders/update/${orderData?.id}`, {
        orderNotes: orderData?.orderNotes,
        isDirectSale: orderData?.isDirectSale ?? false,
        bmsClient: orderData?.bmsClient,
        orderProducts: orderData?.orderProducts,
        callerPhone: orderData?.callerPhone,
    });
};

const getOrderData = (orderId) => {
    return get(`/api/orders/${orderId}`);
};

const getPrintInvoicePdf = (orderId) => {
    return download(`/api/orders/invoice/${orderId}`, `invoice-${orderId}.pdf`);
};

const updateOrder = ({ orderId, data }) => {
    return post(`/api/orders/update-order/${orderId}`, data);
};

const updateDirectSaleOrder = ({ orderId, data }) => {
    return post(`/api/orders/direct-sale/update-order/${orderId}`, data);
};

const duplicateOrder = ({ orderId, data }) => {
    return post(`/api/orders/duplicate-order/${orderId}`, data);
};

const setPriorityOrder = (orderId) => {
    return post(`/api/orders/setPriorityOrder/${orderId}`);
};

const getAllOrders = (orderId) => {
    return get(`/api/orders/all`);
};

const getListOrders = (page = 1, status) => {
    return get(`/api/orders/getOrdersByStatus/${status}?page=${page}`);
};

const getListOrdersWithZone = ({
    page = 1,
    status,
    zoneId = undefined,
    deliveryDate = undefined,
    deliveryPeriod = undefined,
}) => {
    let queryParams = `page=${page}`;
    queryParams += zoneId ? `&zoneId=${zoneId}` : "";
    queryParams += deliveryDate ? `&deliveryDate=${deliveryDate}` : "";
    queryParams += deliveryPeriod ? `&deliveryPeriod=${deliveryPeriod}` : "";

    return get(`/api/orders/getOrdersByStatus/${status}?${queryParams}`);

    // if (!zoneId) {
    //     return get(`/api/orders/getOrdersByStatus/${status}?page=${page}`);
    // }
    // return get(
    //     `/api/orders/getOrdersByStatus/${status}/${zoneId}?page=${page}`
    // );
};

const updatePendingOrder = ({ orderId, data }) => {
    return put(`/api/orders/${orderId}/pending`, data);
};

const updateStatusOrder = ({ orderId, status }) => {
    return put(`/api/orders/${orderId}/setStatus/${status}`);
};

const updateSetNumberInvoice = ({ orderId, serie, number }) => {
    return put(`/api/orders/${orderId}/setNumberInvoice`, { serie, number });
};

const createForkorder = ({ order, orderProducts }) => {
    return post(`/api/orders/forkOrder`, { orderId: order, orderProducts });
};

const validateStock = ({ orderId, status, products }) => {
    return put(`/api/orders/validateStock/${orderId}`, { status, products });
};

const searchOrdersByInput = ({ page = 1, input }) => {
    return get(`/api/orders/searchOrdersByInput/${input}?page=${page}`);
};

const getAllCategories = ({ withSubCategories = true }) => {
    return get(`/api/products/categories/${withSubCategories}`);
};

const getAllzones = () => {
    return get(`/api/addresses/zones/all`);
};

const getBlockedOrders = () => {
    return get(`/api/orders/cache/blocked`);
};

const blockOrder = ({ orderId }) => {
    return post(`/api/orders/cache/add`, {
        order_id: orderId,
        browser_token: browserToken,
    });
};

const unBlockOrder = () => {
    return put(`/api/orders/cache/remove`, { browser_token: browserToken });
};
const replaceBlockedOrder = ({ orderId }) => {
    return post(`/api/orders/cache/replaceOrder`, {
        order_id: orderId,
        browser_token: browserToken,
    });
};

const replaceUserOnBlockedOrder = ({ orderId }) => {
    return post(`/api/orders/cache/replaceUser`, {
        order_id: orderId,
        browser_token: browserToken,
    });
};

const changeOrderDate = ({ orderId, data }) => {
    return post(`/api/orders/change-date/${orderId}`, data);
};

const generateInvoice = (orderId) => {
    return put(`/api/orders/gerenate-invoice/${orderId}`);
};

export {
    getAllProducts,
    getFilteredProducts,
    getFilteredClients,
    getLastSalesAndProducts,
    getMostBoughtProducts,
    getLessBoughtProducts,
    saveNewOrder,
    getOrderData,
    setPriorityOrder,
    getAllOrders,
    updateOrder,
    duplicateOrder,
    updateSetNumberInvoice,
    getListOrders,
    updatePendingOrder,
    updateStatusOrder,
    createForkorder,
    getPrintInvoicePdf,
    validateStock,
    searchOrdersByInput,
    getAllCategories,
    updateOrderAndProducts,
    getAllzones,
    getListOrdersWithZone,
    getBlockedOrders,
    blockOrder,
    unBlockOrder,
    replaceBlockedOrder,
    replaceUserOnBlockedOrder,
    changeOrderDate,
    generateInvoice,
    updateDirectSaleOrder
};

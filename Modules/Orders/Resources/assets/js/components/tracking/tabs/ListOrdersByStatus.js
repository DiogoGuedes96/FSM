import { List, Empty, Spin, Form } from "antd";
import React, { useEffect, useState } from "react";
import {
    getListOrders,
    updateSetNumberInvoice,
    updateStatusOrder,
} from "../../../libs/apis";
import InfiniteScroll from "react-infinite-scroll-component";
import { useMutation, useQuery, QueryClient } from "react-query";
import {
    STATUS_CANCELED,
    STATUS_COMPLETED,
    STATUS_IN_DELIVERY,
    formatOrderToFullList,
} from "../../../libs/utils";
import OrderFullItem from "../OrderFullItem";
import ModalCompletedOrder from "../ModalCompletedOrder";
import OrderFilter from "../orderFilter";

export default function ListOrdersByStatus({ status, openOrder }) {
    const [loading, setLoading] = useState(true);
    const [orders, setOrders] = useState([]);
    const [page, setPage] = useState(1);
    const [total, setTotal] = useState(0);
    const [refetch, setRefetch] = useState(new Date().getTime());
    const [lastPage, setLastPage] = useState(null);
    const queryClient = new QueryClient();
    const [submitSetInvoiceNumberForm] = Form.useForm();
    const [orderActive, setOrderActive] = useState(null);
    const [isModalSetInvoiceNumberVisible, setIsModalSetInvoiceNumberVisible] =
        useState(false);
    const [loadingBtn, setLoadingBtn] = useState(false);

    const {
        data: listOrdersPedentes,
        isSuccess: isSuccessOrders,
        refetch: refetchQuery,
    } = useQuery(
        ["last-sales-and-products", status, page, refetch],
        () => getListOrders(page, status),
        {
            refetchOnWindowFocus: true,
            refetchInterval: 60000,
        }
    );

    useEffect(() => {
        if (isSuccessOrders) {
            const { current_page, data, last_page } = listOrdersPedentes.Orders;
            const listOrders = formatOrderToFullList(data);

            setLoading(false);
            setLastPage(last_page);
            setPage(current_page);

            setOrders([...orders, ...listOrders]);
            setTotal((prevTotal) => prevTotal + listOrders.length);
        }
    }, [isSuccessOrders]);

    const loadMoreData = () => {
        if (loading || page === lastPage) {
            return;
        }

        setLoading(true);

        if (orders.length > 0) {
            setPage((prevPage) => {
                return prevPage + 1;
            });
        }

        queryClient.invalidateQueries([
            "last-sales-and-products",
            status,
            page,
        ]);
    };

    const {
        isSuccess: isSuccessUpdateStatus,
        isLoading: isLoadingBtn,
        mutate: mutationUpdateStatus,
    } = useMutation(["mutationUpdateStatus"], updateStatusOrder);

    const {
        isSuccess: isSuccessSetNumberInvoice,
        isLoading: isLoadingSetNumberInvoice,
        mutate: mutationSetNumberInvoice,
    } = useMutation(["mutationUpdateSetNumberInvoice"], updateSetNumberInvoice);

    useEffect(() => {
        setLoading(true);
        setOrders([]);
        setOrderActive(null);
        setRefetch(new Date().getTime());
        setLoadingBtn(false);
    }, [isSuccessUpdateStatus]);

    useEffect(() => {
        if (isSuccessSetNumberInvoice) {
            mutationUpdateStatus({
                orderId: orderActive?.id,
                status: STATUS_COMPLETED,
            });
        }
    }, [isSuccessSetNumberInvoice]);

    const openToCompleted = (order) => {
        setOrderActive(order);
        onSetInvoiceNumberOrder();
    };

    const sendToCompleted = () => {
        const values = submitSetInvoiceNumberForm.getFieldsValue();

        if (!orderActive?.id) {
            alert("Selecione uma encomenda.");
            return;
        }
        if (!values?.serie) {
            alert("O campo série é obrigatório.");
            return;
        }
        if (!values?.number) {
            alert("O campo numero é obrigatório.");
            return;
        }

        setLoadingBtn(true);

        mutationSetNumberInvoice({
            orderId: orderActive?.id,
            serie: values?.serie,
            number: values?.number,
        });
    };

    const onSetInvoiceNumberOrder = () => {
        setIsModalSetInvoiceNumberVisible(true);
    };

    const handleModalSetInvoiceNumberCancel = () => {
        setIsModalSetInvoiceNumberVisible(false);
    };

    const cancelOrder = (order) => {
        setLoadingBtn(true);
        mutationUpdateStatus({ orderId: order.id, status: STATUS_CANCELED });
    };

    return (
        <>
            <OrderFilter
                setOrders={setOrders}
                setLoading={setLoading}
                setPage={setPage}
                setLastPage={setLastPage}
                setTotal={setTotal}
                setOrderSelected={setOrderActive}
                status={status}
            />
            {orders.length ? (
                <div
                    id="scrollableDiv"
                    className="scrollable-container"
                    style={{
                        overflow: "auto",
                    }}
                >
                    <InfiniteScroll
                        dataLength={total}
                        next={loadMoreData}
                        hasMore={true}
                        endMessage={"Não há mais encomendas"}
                        scrollableTarget="scrollableDiv"
                    >
                        <List
                            dataSource={orders}
                            renderItem={(item) => {
                                return (
                                    <OrderFullItem
                                        item={item}
                                        loadOrder={openOrder}
                                        delivery={
                                            item.status === STATUS_IN_DELIVERY
                                                ? openToCompleted
                                                : false
                                        }
                                        deliveryBtnLoading={loadingBtn}
                                        cancelOrder={cancelOrder}
                                    />
                                );
                            }}
                        />
                    </InfiniteScroll>
                    <ModalCompletedOrder
                        open={isModalSetInvoiceNumberVisible}
                        onCancel={handleModalSetInvoiceNumberCancel}
                        order={orderActive}
                        isLoading={loadingBtn}
                        setInvoiceNumber={(values) => sendToCompleted(values)}
                        form={submitSetInvoiceNumberForm}
                    />
                    {loading && (
                        <div
                            className="container-spin"
                            style={{ height: 0, margin: 14, gap: 14 }}
                        >
                            <Spin size="medium" />{" "}
                            <span>Carregando mais encomendas...</span>
                        </div>
                    )}
                </div>
            ) : (
                <Empty
                    style={{
                        width: "100%",
                        marginTop: "35vh",
                        padding: "10px",
                    }}
                    description={
                        <span
                            style={{
                                textAlign: "center",
                                color: "#00000073",
                                opacity: "1",
                            }}
                        >
                            Não foram encontradas encomendas!
                        </span>
                    }
                />
            )}
        </>
    );
}

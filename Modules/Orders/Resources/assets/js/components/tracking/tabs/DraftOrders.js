import React, { useEffect, useState } from "react";
import { Row, Col, Spin, Divider, message, Empty } from "antd";
import OrdersList from "../OrdersList";
import {
    getOrderData,
    updatePendingOrder,
    updateStatusOrder,
    getListOrders,
} from "../../../libs/apis";
import { QueryClient, useMutation, useQuery } from "react-query";
import {
    STATUS_CANCELED,
    STATUS_DRAFT,
    formatOrderToOpenDetails,
    formatOrdersToSideList,
} from "../../../libs/utils";
import OrdersDetailHeader from "../OrderDetailHeaderj";
import OrdersDetailInfo from "../OrderDetailInfos";
import TableProductsDraft from "../TableProductsDraft";
import OrderFilter from "../orderFilter";

export default function DraftOrders() {
    const [loading, setLoading] = useState(false);
    const [orders, setOrders] = useState([]);
    const [page, setPage] = useState(1);
    const [total, setTotal] = useState(0);
    const [orderSelected, setOrderSelected] = useState(null);
    const [orderActive, setOrderActive] = useState(null);
    const [messageApi, contextHolder] = message.useMessage();
    const [openModal, setOpenModal] = useState(false);
    const [refetch, setRefetch] = useState(new Date().getTime());
    const [lastPage, setLastPage] = useState(1);
    const queryClient = new QueryClient();
    const [blockedOrders, setBlockedOrders] = useState([]);
    const [user, setUser] = useState(
        localStorage.getItem("user")
            ? JSON.parse(localStorage.getItem("user"))
            : null
    );
    const [browserToken, setBrowserToken] = useState(
        localStorage.getItem("browserToken") ?? null
    );

    const { data: listDraftOrders } = useQuery(
        ["last-sales-and-products-draft", page, STATUS_DRAFT, refetch],
        () => getListOrders(page, STATUS_DRAFT),
        {
            refetchOnWindowFocus: false,
            refetchInterval: 60000,
        }
    );

    const { data: orderSelectedData, isSuccess: isSuccessOrderSelected } =
        useQuery(
            ["get-order-data-draft-selected", orderActive],
            () => {
                if (orderActive) {
                    return getOrderData(orderActive);
                }
                return;
            },
            { refetchOnWindowFocus: false }
        );

    const {
        isSuccess: isSuccessMutation,
        isError: isErrorMutationForm,
        mutate: mutationForm,
    } = useMutation(["mutationFormDraftOrder"], updatePendingOrder, {
        onSuccess: () => {
            unBlockOrder();
        },
    });

    const {
        isSuccess: isSuccessUpdateStatus,
        isError: isErrorMutationUpdateStatus,
        mutate: mutationUpdateStatus,
    } = useMutation(["mutationUpdateStatus"], updateStatusOrder);

    useEffect(() => {
        if (isSuccessUpdateStatus) {
            setOpenModal(false);
            setOrderActive(null);
            setOrderSelected(null);
            setPage(1);
            setOrders([]);
            setTotal(0);
            setRefetch(new Date().getTime());
        }
    }, [isSuccessUpdateStatus]);

    useEffect(() => {
        const urlParams = new URLSearchParams(window.location.search);
        const order = urlParams.get("order");
        if (order) {
            loadOrder(parseInt(order));
        }
    }, []);

    useEffect(() => {
        if (isSuccessOrderSelected && orderSelectedData) {
            const order = formatOrderToOpenDetails(orderSelectedData);
            
            const products = order.products.map((p) => {
                let batch = null;
                if (p.order_product_batch) {
                    batch = p.batches.find(
                        (b) => b.label === p.order_product_batch
                    )?.value ?? null;
                }

                return {
                    [`volume-${p.id}`]: p.volume ?? null,
                    [`quantity-${p.id}`]: p.quantity ?? null,
                    [`conversion-${p.id}`]: p.conversion ?? null,
                    [`notes-${p.id}`]: p.notes ?? null,
                    [`batch-${p.id}`]: batch,
                };
            });

            const initialProducts = {};
            products.forEach((product) => {
                Object.assign(initialProducts, product);
            });

            setOrderSelected(order);

            setLoading(false);
        }
    }, [isSuccessOrderSelected, orderSelectedData]);

    useEffect(() => {
        if (isErrorMutationForm || isErrorMutationUpdateStatus) {
            setOpenModal(false);
            setConfirmLoadingModal(false);
            message.error("Erro ao concluir o processo!");
        }
    }, [isErrorMutationForm, isErrorMutationUpdateStatus]);

    useEffect(() => {
        if (listDraftOrders?.Orders?.data?.length > 0) {
            const { current_page, data, last_page } = listDraftOrders.Orders;
            const listOrders = formatOrdersToSideList(data);

            setLoading(false);
            setPage(current_page);
            setLastPage(last_page);
            setOrders([...orders, ...listOrders]);
            setTotal((prevTotal) => prevTotal + listOrders.length);
        }
    }, [listDraftOrders]);

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

        queryClient.invalidateQueries(["last-sales-and-products-draft", page]);
    };

    const loadOrder = (id) => {
        if (id === orderActive) {
            return;
        }
        setLoading(true);
        setOrderActive(id);

        queryClient.invalidateQueries([
            "get-order-data-draft-selected",
            orderActive,
        ]);
    };

    const cancelOrder = () => {
        mutationUpdateStatus({
            orderId: orderSelected.id,
            status: STATUS_CANCELED,
        });
    };

    const addProducts = () => {
        orderSelected?.isDirectSale ?
            window.location.href = `/orders/directSale?order=${orderSelected.id}`
            :
            window.location.href = `/orders/newOrder?order=${orderSelected.id}`;
    };

    return (
        <>
            <OrderFilter
                setOrders={setOrders}
                setLoading={setLoading}
                setPage={setPage}
                setLastPage={setLastPage}
                setTotal={setTotal}
                setOrderSelected={setOrderSelected}
                status={STATUS_DRAFT}
            />
            {orders.length ? (
                <Row>
                    {contextHolder}
                    <Col
                        className="customer-list"
                        xs={24}
                        md={8}
                    >
                        <OrdersList
                            loadMoreData={loadMoreData}
                            data={orders}
                            total={total}
                            loadOrder={loadOrder}
                            orderActive={orderActive}
                            blockedOrders={blockedOrders}
                            setOrderActive={setOrderActive}
                            setOrderSelected={setOrderSelected}
                            userId={user.id}
                            browserToken={browserToken}
                            removeUnlbockBtn={true}
                        />
                    </Col>
                    <Col
                        className="order-list"
                        xs={24}
                        md={16}
                    >
                        {loading ? (
                            <div className="container-spin">
                                <Spin size="large" />
                            </div>
                        ) : (
                            orderSelected && (
                                <>
                                    <div
                                        className="scrollable-container"
                                        style={{
                                            overflow: "auto",
                                            padding: "1.5rem",
                                            backgroundColor: "#fff",
                                            width: "100%",
                                        }}
                                    >
                                        <OrdersDetailHeader
                                            order={orderSelected}
                                            firstButton={addProducts}
                                            secondButton={cancelOrder}
                                        />
                                        <OrdersDetailInfo
                                            order={orderSelected}
                                        />
                                        <Divider />
                                        <TableProductsDraft
                                            products={orderSelected.products.map(
                                                (p) => ({ ...p, key: p.id })
                                            )}
                                        />
                                    </div>
                                </>
                            )
                        )}
                    </Col>
                </Row>
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

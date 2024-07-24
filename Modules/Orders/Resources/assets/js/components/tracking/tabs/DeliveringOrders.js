import React, { useEffect, useState } from "react";
import {
    Row,
    Col,
    Spin,
    Form,
    Divider,
    Empty,
    message,
    notification,
} from "antd";
import OrdersList from "../OrdersList";
import { QueryClient, useMutation, useQuery } from "react-query";
import OrdersDetailHeader from "../OrderDetailHeaderj";
import OrdersDetailInfo from "../OrderDetailInfos";
import TableProductsGeneric from "../TableProductsGeneric";
import {
    getListOrders,
    getPrintInvoicePdf,
    updateStatusOrder,
    getOrderData,
    getBlockedOrders,
    blockOrder,
    unBlockOrder,
    replaceBlockedOrder,
    replaceUserOnBlockedOrder,
} from "../../../libs/apis";
import {
    STATUS_COMPLETED,
    STATUS_IN_DELIVERY,
    formatOrderToOpenDetails,
    formatOrdersToSideList,
} from "../../../libs/utils";
import OrderFilter from "../orderFilter";

export default function DeliveringOrders() {
    const [formOrder] = Form.useForm();
    const [loading, setLoading] = useState(false);
    const [orders, setOrders] = useState([]);
    const [page, setPage] = useState(1);
    const [total, setTotal] = useState(0);
    const [orderSelected, setOrderSelected] = useState(null);
    const [orderActive, setOrderActive] = useState(null);
    const [refetch, setRefetch] = useState(new Date().getTime());
    const [lastPage, setLastPage] = useState(null);
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
    const [blockOrderApi, contextHolderUnblockOrder] =
        notification.useNotification();

    const popUpMessages = {
        orderBlockedByOtherUser:
            "A sua encomenda foi bloqueada por outro utilizador!",
        orderIsBeingUsed:
            "A encomenda já se encontra a ser preparada por outro utilizador.",
    };

    const { data: dataBlockOrder, mutate: mutateBlockOrder } = useMutation(
        ["mutationBlockOrder"],
        blockOrder
    );
    const { data: dataUnblockOrder, mutate: mutateUnlbockOrder } = useMutation(
        ["mutationUnblockOrder"],
        unBlockOrder
    );
    const { data: dataReplaceOrder, mutate: mutateReplaceOrder } = useMutation(
        ["mutationReplaceOrder"],
        replaceBlockedOrder
    );
    const {
        data: dataReplaceUserOnBlockedOrder,
        mutate: mutateReplaceUserOnBlockedOrder,
    } = useMutation(
        ["mutationReplaceUserOnBlockedOrder"],
        replaceUserOnBlockedOrder
    );

    const { data: listOrdersPedentes } = useQuery(
        [
            "last-sales-and-products-delivering",
            page,
            STATUS_IN_DELIVERY,
            refetch,
        ],
        () => getListOrders(page, STATUS_IN_DELIVERY),
        {
            refetchOnWindowFocus: true,
            refetchInterval: 60000,
        }
    );

    const { data: blockedOrdersData, isSuccess: isSuccessBlockedOrders } =
        useQuery(["blockedOrders"], getBlockedOrders, {
            refetchInterval: 2000,
        });

    const { data: orderSelectedData, isSuccess: isSuccessOrderSelected } =
        useQuery(
            ["get-order-data-delivering-selected", orderActive],
            () => {
                if (orderActive) return getOrderData(orderActive);

                return;
            },
            { refetchOnWindowFocus: false }
        );

    const { mutate: mutationPrint } = useMutation(
        ["get-order-print-invoice", orderActive],
        getPrintInvoicePdf
    );

    const {
        isSuccess: isSuccessUpdateStatus,
        isLoading: isLoadingBtn,
        mutate: mutationUpdateStatus,
    } = useMutation(["mutationUpdateStatus"], updateStatusOrder, {
        onSuccess: () => {
            unBlockOrder();
        },
    });

    useEffect(() => {
        if (blockedOrdersData?.orders?.length > 0) {
            setBlockedOrders(blockedOrdersData?.orders);
        } else {
            setBlockedOrders([]);
        }
    }, [blockedOrdersData, isSuccessBlockedOrders]);

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

            setOrderSelected(order);
            setLoading(false);
        }
    }, [isSuccessOrderSelected, orderSelectedData]);

    useEffect(() => {
        if (listOrdersPedentes?.Orders?.data?.length > 0) {
            const { current_page, data, last_page } = listOrdersPedentes.Orders;
            const listOrders = formatOrdersToSideList(data);

            setLoading(false);
            setLastPage(last_page);
            setPage(current_page);
            setOrders([...orders, ...listOrders]);
            setTotal((prevTotal) => prevTotal + listOrders.length);
        }
    }, [listOrdersPedentes]);

    useEffect(() => {
        if (isSuccessUpdateStatus) {
            setPage(1);
            setLastPage(null);
            setTotal(0);
            setOrders([]);
            setOrderActive(null);
            setOrderSelected(null);
            setRefetch(new Date().getTime());
        }
    }, [isSuccessUpdateStatus]);

    useEffect(() => {
        if (blockedOrders?.length > 0 && orders?.length > 0) {
            if (user && browserToken) {
                const currentOrderWasStolen = blockedOrders.some(
                    (blockedOrder) => {
                        return (
                            blockedOrder.order_id === orderActive &&
                            blockedOrder.browser_token !== browserToken
                        );
                    }
                );

                if (currentOrderWasStolen) {
                    handleOrderWasStolen();
                } else {
                    const blockedOrder = blockedOrders?.find((blockedOrder) => {
                        return (
                            blockedOrder.user_id === user.id &&
                            blockedOrder.browser_token === browserToken
                        );
                    });

                    if (blockedOrder) {
                        const blockedOrderAux = orders.find((orderData) => {
                            return orderData.id === blockedOrder.order_id;
                        });

                        if (blockedOrderAux?.status === STATUS_IN_DELIVERY) {
                            setOrderActive(blockedOrder.order_id);
                        }
                    }
                }
            }
        }
    }, [blockedOrders, orders]);

    const handleOrderWasStolen = () => {
        setOrderActive(null);
        setOrderSelected(null);
        openNotification("top");
    };

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
            "last-sales-and-products-delivering",
            page,
        ]);
    };

    const loadOrder = (id) => {
        if (id === orderActive) {
            return;
        }

        handleOrderActive(id);

        queryClient.invalidateQueries([
            "get-order-data-partially-selected",
            orderActive,
        ]);
    };

    const openNotification = (placement) => {
        blockOrderApi.warning({
            message: popUpMessages.orderBlockedByOtherUser,
            placement,
        });
    };

    const handleOrderActive = (id) => {
        if (id === orderActive) {
            return;
        }

        const userHasBlockedOrders = blockedOrders.some((blockedOrder) => {
            return (
                blockedOrder.user_id === user.id &&
                blockedOrder.browser_token === browserToken
            );
        });

        if (
            blockedOrders.some(
                (blockedOrder) =>
                    blockedOrder.order_id === id &&
                    (blockedOrder.user_id !== user.id ||
                        blockedOrder.browser_token !== browserToken)
            )
        ) {
            handlePermissionsToStoleOrder(id);
        } else {
            setLoading(true);
            setOrderActive(id);
            if (userHasBlockedOrders) {
                mutateReplaceOrder({ orderId: id });
            } else {
                mutateBlockOrder({ orderId: id });
            }
        }
    };

    const handlePermissionsToStoleOrder = (id) => {
        // const module = user?.canAccess?.find(
        //     (module) => module.module === "orders-tracking"
        // );

        // if (module.permissions) {
        //     const permissions = JSON.parse(module.permissions);

        //     if (permissions) {
        //         if (
        //             permissions["unblockOrder"] === false ||
        //             permissions["unblockOrder"] === undefined
        //         ) {
        //             message.error(popUpMessages.orderIsBeingUsed);
        //         } else {
        mutateReplaceUserOnBlockedOrder({ orderId: id });
        //         }
        //     }
        // }
    };

    const sendToCompleted = () => {
        mutationUpdateStatus({
            orderId: orderSelected.id,
            status: STATUS_COMPLETED,
        });
    };

    const onPrint = () => {
        mutationPrint(orderActive);
    };

    return (
        <>
            {contextHolderUnblockOrder}
            <OrderFilter
                setOrders={setOrders}
                setLoading={setLoading}
                setPage={setPage}
                setLastPage={setNextPage}
                setTotal={setTotal}
                setOrderSelected={setOrderSelected}
                status={STATUS_IN_DELIVERY}
            />
            {orders.length ? (
                <Row>
                    <Col className="customer-list" xs={24} md={8}>
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
                        />
                    </Col>
                    <Col className="order-list" xs={24} md={16}>
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
                                            firstButton={sendToCompleted}
                                            secondButton={onPrint}
                                            loadingButton={isLoadingBtn}
                                        />
                                        <OrdersDetailInfo
                                            order={orderSelected}
                                            form={formOrder}
                                        />
                                        <Divider />
                                        <TableProductsGeneric
                                            products={orderSelected.products.map(
                                                (p) => ({ ...p, key: p.id })
                                            )}
                                            form={formOrder}
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

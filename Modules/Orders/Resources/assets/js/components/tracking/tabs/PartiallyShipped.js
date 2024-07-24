import React, { useEffect, useState } from "react";
import {
    Row,
    Col,
    Spin,
    Form,
    Divider,
    Typography,
    message,
    Empty,
    notification,
} from "antd";
import OrdersList from "../OrdersList";
import {
    getListOrders,
    getOrderData,
    updateStatusOrder,
    validateStock,
    getBlockedOrders,
    blockOrder,
    unBlockOrder,
    replaceBlockedOrder,
    replaceUserOnBlockedOrder,
} from "../../../libs/apis";
import { QueryClient, useMutation, useQuery } from "react-query";
import {
    STATUS_CANCELED,
    STATUS_PARTIALLY_SHIPPED,
    STATUS_PENDING,
    formatOrderToOpenDetails,
    formatOrdersToSideList,
    getAvailableProducts,
    getUnavailableProducts,
} from "../../../libs/utils";
import OrdersDetailHeader from "../OrderDetailHeaderj";
import OrdersDetailInfo from "../OrderDetailInfos";
import TableProductsPartially from "../TableProductPartially";
import OrderFilter from "../orderFilter";

const { Text } = Typography;

export default function PartiallyShipped({ setStateTotal }) {
    const [formOrder] = Form.useForm();
    const [loading, setLoading] = useState(false);
    const [orders, setOrders] = useState([]);
    const [page, setPage] = useState(1);
    const [total, setTotal] = useState(0);
    const [orderSelected, setOrderSelected] = useState(null);
    const [nextPage, setNextPage] = useState(null);
    const [orderActive, setOrderActive] = useState(null);
    const [messageApi, contextHolder] = message.useMessage();
    const [refetch, setRefetch] = useState(new Date().getTime());
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

    const { data: blockedOrdersData, isSuccess: isSuccessBlockedOrders } =
        useQuery(["blockedOrders"], getBlockedOrders, {
            refetchInterval: 2000,
        });

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
            "last-sales-and-products-partially",
            page,
            STATUS_PARTIALLY_SHIPPED,
            refetch,
        ],
        () => getListOrders(page, STATUS_PARTIALLY_SHIPPED),
        {
            refetchOnWindowFocus: false,
            refetchInterval: 60000,
        }
    );

    const { data: orderSelectedData, isSuccess: isSuccessOrderSelected } =
        useQuery(
            ["get-order-data-partially-selected", orderActive],
            () => {
                if (orderActive) {
                    return getOrderData(orderActive);
                }
                return;
            },
            { refetchOnWindowFocus: false }
        );

    const { isSuccess: isSuccessUpdateStatus, mutate: mutationUpdateStatus } =
        useMutation(["mutationUpdateStatus"], updateStatusOrder);

    const { isSuccess: isSuccessValidateStock, mutate: mutationValidateStock } =
        useMutation(["mutationValidateStock"], validateStock, {
            onSuccess: () => {
                unBlockOrder();
            },
        });

    useEffect(() => {
        if (isSuccessOrderSelected && orderSelectedData) {
            const order = formatOrderToOpenDetails(orderSelectedData);

            setOrderSelected(order);
            setLoading(false);
        }
    }, [isSuccessOrderSelected, orderSelectedData]);

    useEffect(() => {
        if (listOrdersPedentes?.Orders?.data?.length > 0) {
            const { current_page, data, last_page, total } =
                listOrdersPedentes.Orders;
            const listOrders = formatOrdersToSideList(data);

            setLoading(false);
            setNextPage(last_page);
            setPage(current_page);
            setOrders([...orders, ...listOrders]);
            setStateTotal(total);
            setTotal((prevTotal) => prevTotal + listOrders.length);
        }
    }, [listOrdersPedentes]);

    useEffect(() => {
        if (isSuccessUpdateStatus || isSuccessValidateStock) {
            setOrderActive(null);
            setOrderSelected(null);
            setPage(1);
            setOrders([]);
            setTotal(0);
            setRefetch(new Date().getTime());
        }
    }, [isSuccessUpdateStatus, isSuccessValidateStock]);

    useEffect(() => {
        if (blockedOrdersData?.orders?.length > 0) {
            setBlockedOrders(blockedOrdersData?.orders);
        } else {
            setBlockedOrders([]);
        }
    }, [blockedOrdersData, isSuccessBlockedOrders]);

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

                        if (
                            blockedOrderAux?.status === STATUS_PARTIALLY_SHIPPED
                        ) {
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

    const onConfirm = () => {
        mutationValidateStock({
            orderId: orderSelected.id,
            status: STATUS_PENDING,
            products: orderSelected.products.map((product) => product.id),
        });
    };

    const loadMoreData = () => {
        if (loading || page === nextPage) {
            return;
        }

        setLoading(true);

        if (orders.length > 0) {
            setPage((prevPage) => {
                return prevPage + 1;
            });
        }

        queryClient.invalidateQueries([
            "last-sales-and-products-partially",
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
        // const module = user?.canAccess?.find(module => module.module === 'orders-tracking');

        // if (module.permissions) {
        //     const permissions = JSON.parse(module.permissions);

        //     if ( permissions ){
        //         if(permissions['unblockOrder'] === false || permissions['unblockOrder'] === undefined){
        //             message.error(popUpMessages.orderIsBeingUsed);
        //         }else {
        mutateReplaceUserOnBlockedOrder({ orderId: id });
        //         }
        //     };
        // }
    };

    const cancelOrder = () => {
        mutationUpdateStatus({
            orderId: orderSelected.id,
            status: STATUS_CANCELED,
        });
    };

    const addProducts = () => {
        window.location.href = `/orders/newOrder?order=${orderSelected.id}`;
    };

    const dropdownButton = (btn) => {
        switch (btn.key) {
            case "add_products":
                addProducts();
                break;
            case "cancel":
                cancelOrder();
                break;
            default:
                break;
        }
    };

    const deleteProduct = (product) => {
        if (orderSelected.products.length === 1) {
            messageApi.open({
                type: "error",
                content: `Não é possivel remover todos os produtos de uma encomenda.`,
            });
            return;
        }

        setOrderSelected((prevState) => {
            return {
                ...prevState,
                products: prevState.products.filter((p) => p.id !== product.id),
            };
        });
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
                status={STATUS_PARTIALLY_SHIPPED}
            />
            {orders.length ? (
                <Row>
                    {contextHolder}
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
                                            firstButton={onConfirm}
                                            secondButton={dropdownButton}
                                        />
                                        <OrdersDetailInfo
                                            order={orderSelected}
                                            form={formOrder}
                                        />
                                        <Divider />
                                        <TableProductsPartially
                                            products={orderSelected.products.map(
                                                (p) => ({ ...p, key: p.id })
                                            )}
                                            form={formOrder}
                                            actionDelete={deleteProduct}
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

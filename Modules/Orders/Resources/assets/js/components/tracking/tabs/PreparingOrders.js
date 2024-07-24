import React, { useEffect, useState } from "react";
import {
    Row,
    Col,
    Spin,
    Form,
    Divider,
    Typography,
    Empty,
    message,
    notification,
} from "antd";
import OrdersList from "../OrdersList";
import {
    getOrderData,
    updateStatusOrder,
    getListOrders,
    getPrintInvoicePdf,
    getBlockedOrders,
    blockOrder,
    unBlockOrder,
    replaceBlockedOrder,
    replaceUserOnBlockedOrder,
} from "../../../libs/apis";
import { QueryClient, useMutation, useQuery } from "react-query";
import {
    STATUS_CANCELED,
    STATUS_IN_DELIVERY,
    STATUS_IN_PREPARATION,
    STATUS_PENDING,
    formatOrderToOpenDetails,
    formatOrdersToSideList,
} from "../../../libs/utils";
import OrdersDetailHeader from "../OrderDetailHeaderj";
import OrdersDetailInfo from "../OrderDetailInfos";
import ModalTracking from "../ModalTracking";
import TableProductsGeneric from "../TableProductsGeneric";
import OrderFilter from "../orderFilter";

const { Text } = Typography;

export default function PreparingOrders() {
    const [formOrder] = Form.useForm();
    const [loading, setLoading] = useState(false);
    const [orders, setOrders] = useState([]);
    const [page, setPage] = useState(1);
    const [total, setTotal] = useState(0);
    const [orderSelected, setOrderSelected] = useState(null);
    const [orderActive, setOrderActive] = useState(null);
    const [initialValues, setInitialValues] = useState({});
    const [dataModal, setDataModal] = useState({});
    const [confirmLoadingModal, setConfirmLoadingModal] = useState(false);
    const [openModal, setOpenModal] = useState(false);
    const [refetch, setRefetch] = useState(new Date().getTime());
    const [printAndChangeStatus, setPrintAndChangeStatus] = useState(false);
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
    const [api, contextHolder] = notification.useNotification();

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

    const { data: blockedOrdersData, isSuccess: isSuccessBlockedOrders } =
        useQuery(["blockedOrders"], getBlockedOrders, {
            refetchInterval: 2000,
        });

    const { data: listOrdersPedentes } = useQuery(
        ["last-sales-and-products-peding", page, STATUS_PENDING, refetch],
        () => getListOrders(page, STATUS_PENDING),
        { refetchOnWindowFocus: true, refetchInterval: 60000 }
    );

    const { data: orderSelectedData, isSuccess: isSuccessOrderSelected } =
        useQuery(
            ["get-order-data-peding-selected", orderActive],
            () => {
                if (orderActive) return getOrderData(orderActive);

                return;
            },
            { refetchOnWindowFocus: false }
        );

    const { isSuccess: isSuccessPrint, mutate: mutationPrint } = useMutation(
        ["get-order-print-invoice", orderActive],
        getPrintInvoicePdf
    );

    const { isSuccess: isSuccessUpdateStatus, mutate: mutationUpdateStatus } =
        useMutation(["mutationUpdateStatus"], updateStatusOrder, {
            onSuccess: () => {
                unBlockOrder();
            },
        });

    useEffect(() => {
        if (isSuccessPrint) {
            setConfirmLoadingModal(false);
            setOpenModal(false);

            if (printAndChangeStatus) {
                mutationUpdateStatus({
                    orderId: orderSelected.id,
                    status: STATUS_IN_DELIVERY,
                });
                setPrintAndChangeStatus(false);
            }
        }
    }, [isSuccessPrint]);

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

            setInitialValues({ notes: order.description });

            setOrderSelected(order);
            setLoading(false);
        }
    }, [isSuccessOrderSelected, orderSelectedData]);

    useEffect(() => {
        if (listOrdersPedentes?.Orders?.data?.length > 0) {
            const { current_page, data, last_page } = listOrdersPedentes.Orders;
            const listOrders = formatOrdersToSideList(data);

            setLoading(false);
            setPage(current_page);
            setLastPage(last_page);
            setOrders((prevOrders) => [...prevOrders, ...listOrders]);
            setTotal((prevTotal) => prevTotal + listOrders.length);
        }
    }, [listOrdersPedentes]);

    useEffect(() => {
        if (isSuccessUpdateStatus) {
            setOpenModal(false);
            setConfirmLoadingModal(false);
            setOrderActive(null);
            setOrderSelected(null);
            setOrders([]);
            setPage(1);
            setTotal(0);
            setRefetch(new Date().getTime());
        }
    }, [isSuccessUpdateStatus]);

    useEffect(() => {
        if (initialValues) {
            formOrder.setFieldsValue(initialValues);
        }
    }, [initialValues, formOrder]);


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

                        if (blockedOrderAux?.status === STATUS_PENDING) {
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

    const showModal = () => {
        setDataModal(
            <div>
                <Text>
                    Você deseja enviar a encomenda para entrega e imprimir para
                    viatura ou apenas imprimir para viatura?
                </Text>
            </div>
        );

        setOpenModal(true);
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
        queryClient.invalidateQueries(["last-sales-and-products-peding", page]);
    };

    const loadOrder = (id) => {
        if (id === orderActive) {
            return;
        }

        handleOrderActive(id);

        queryClient.invalidateQueries([
            "get-order-data-peding-selected",
            orderActive,
        ]);
    };

    const openNotification = (placement) => {
        api.warning({
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

    const sendToPreparation = () => {
        setConfirmLoadingModal(true);
        mutationUpdateStatus({
            orderId: orderSelected.id,
            status: STATUS_IN_PREPARATION,
        });
    };

    const onPrint = () => {
        showModal();
    };

    const onConfirmModal = () => {
        setConfirmLoadingModal(true);
        setPrintAndChangeStatus(true);
        mutationPrint(orderActive);
    };

    const onCancelModal = () => {
        setOpenModal(false);
    };

    const secundaryBtn = () => {
        setConfirmLoadingModal(true);
        mutationPrint(orderActive);
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

    const cancelOrder = () => {
        mutationUpdateStatus({
            orderId: orderSelected.id,
            status: STATUS_CANCELED,
        });
    };

    const addProducts = () => {
        window.location.href = `/orders/newOrder?order=${orderSelected.id}`;
    };

    return (
        <>
            {contextHolder}
            <OrderFilter
                setOrders={setOrders}
                setLoading={setLoading}
                setPage={setPage}
                setLastPage={setLastPage}
                setTotal={setTotal}
                setOrderSelected={setOrderSelected}
                status={STATUS_PENDING}
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
                                            firstButton={sendToPreparation}
                                            secondButton={onPrint}
                                            dropdownButton={dropdownButton}
                                        />
                                        <OrdersDetailInfo
                                            order={orderSelected}
                                            form={formOrder}
                                        />
                                        <Divider />
                                        <TableProductsGeneric
                                            short={true}
                                            products={orderSelected.products.map(
                                                (p) => ({ ...p, key: p.id })
                                            )}
                                            form={formOrder}
                                        />
                                    </div>
                                </>
                            )
                        )}
                        <ModalTracking
                            open={openModal}
                            onConfirm={onConfirmModal}
                            onCancel={onCancelModal}
                            secundaryBtn={secundaryBtn}
                            confirmLoading={confirmLoadingModal}
                            data={dataModal}
                            okText="Imprimir para viatura e enviar para entrega"
                            cancelText="Imprimir para viatura"
                            title=""
                        />
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
                            Não foram encontradas encomenda!
                        </span>
                    }
                />
            )}
        </>
    );
}

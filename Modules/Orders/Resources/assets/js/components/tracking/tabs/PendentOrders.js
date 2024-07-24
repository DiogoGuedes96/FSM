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
    getOrderData,
    updatePendingOrder,
    updateStatusOrder,
    createForkorder,
    getListOrders,
    getBlockedOrders,
    blockOrder,
    unBlockOrder,
    replaceBlockedOrder,
    replaceUserOnBlockedOrder,
    getPrintInvoicePdf,
} from "../../../libs/apis";
import { QueryClient, useMutation, useQuery } from "react-query";
import {
    STATUS_CANCELED,
    STATUS_IN_DELIVERY,
    STATUS_IN_PREPARATION,
    formatOrderToOpenDetails,
    formatOrdersToSideList,
    getAvailableProducts,
    getUnavailableProducts,
} from "../../../libs/utils";
import OrdersDetailHeader from "../OrderDetailHeaderj";
import OrdersDetailInfo from "../OrderDetailInfos";
import TableProductsPendent from "../TableProductsPendent";
import ModalTrackingList from "../ModalTrackingList";
import ModalTracking from "../ModalTracking";
import { offset } from "@popperjs/core";
import OrderFilter from "../orderFilter";

const { Text } = Typography;

export default function PendentOrders() {
    const [formOrder] = Form.useForm();
    const [loading, setLoading] = useState(false);
    const [orders, setOrders] = useState([]);
    const [page, setPage] = useState(1);
    const [total, setTotal] = useState(0);
    const [orderSelected, setOrderSelected] = useState(null);
    const [orderActive, setOrderActive] = useState(null);
    const [dataModal, setDataModal] = useState({});
    const [confirmLoadingModal, setConfirmLoadingModal] = useState(false);
    const [openModal, setOpenModal] = useState(false);
    const [data, setData] = useState({});
    const [unavailabilitiesFork, setUnavailabilitiesFork] = useState([]);
    const [availabilitiesProducts, setAvailabilitiesProducts] = useState([]);
    const [initialValues, setInitialValues] = useState({});
    const [messageApi, contextHolder] = message.useMessage();
    const [refetch, setRefetch] = useState(new Date().getTime());
    const [lastPage, setLastPage] = useState(1);
    const queryClient = new QueryClient();
    const [blockedOrders, setBlockedOrders] = useState([]);
    const [downloadInvoice, setDownloadInvoice] = useState(false);
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

    const { isSuccess: isSuccessPrint, mutate: mutationPrint } = useMutation(
        ["get-order-print-invoice", orderActive],
        getPrintInvoicePdf
    );

    // useEffect(() => {
    //     if (isSuccessPrint) {
    //         mutationForm(data);
    //     }
    // }, [isSuccessPrint]);

    const { data: listOrdersPedentes } = useQuery(
        [
            "last-sales-and-products-pending",
            page,
            STATUS_IN_PREPARATION,
            refetch,
        ],
        () => getListOrders(page, STATUS_IN_PREPARATION),
        {
            refetchOnWindowFocus: false,
            refetchInterval: 60000,
        }
    );

    const { data: orderSelectedData, isSuccess: isSuccessOrderSelected } =
        useQuery(
            ["get-order-data-pending-selected", orderActive],
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
    } = useMutation(["mutationFormPendingOrder"], updatePendingOrder, {
        onSuccess: () => {
            unBlockOrder();
        },
    });

    const {
        isSuccess: isSuccessMutationJustSave,
        isError: isErrorMutationFormJustSave,
        mutate: mutationFormJustSave,
    } = useMutation(["mutationFormPendingOrderJustSave"], updatePendingOrder);

    useEffect(() => {
        if (isSuccessMutationJustSave) {
            message.success("As informações foram guardadas com sucesso!");
        }
    }, [isSuccessMutationJustSave]);

    const {
        isSuccess: isSuccessUpdateStatus,
        isError: isErrorMutationUpdateStatus,
        mutate: mutationUpdateStatus,
    } = useMutation(["mutationUpdateStatus"], updateStatusOrder);

    const {
        mutate: mutationForkorder,
        isErrorMutationForkOrder,
        isSuccess: isSuccessForkOrder,
    } = useMutation(["mutationForkorder"], createForkorder);

    useEffect(() => {
        if (isSuccessForkOrder) {
            checkNextStatusAndUpdate();
        }
    }, [isSuccessForkOrder]);

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
                    batch =
                        p.batches.find((b) => b.label === p.order_product_batch)
                            ?.value ?? "";
                }
                return {
                    [`volume-${p.id}`]: p.volume ?? null,
                    [`quantity-${p.id}`]: p.quantity ?? null,
                    [`conversion-${p.id}`]: p.conversion ?? null,
                    [`unavailability-${p.id}`]:
                        p.unavailability != undefined && p.unavailability == 1
                            ? true
                            : null,
                    [`notes-${p.id}`]: p.notes ?? null,
                    [`batch-${p.id}`]: batch,
                };
            });

            const initialProducts = {};
            products.forEach((product) => {
                Object.assign(initialProducts, product);
            });
            setInitialValues({ notes: order.description, ...initialProducts });
            setOrderSelected(order);

            setLoading(false);
        }
    }, [isSuccessOrderSelected, orderSelectedData]);

    useEffect(() => {
        if (
            isErrorMutationForm ||
            isErrorMutationUpdateStatus ||
            isErrorMutationForkOrder
        ) {
            setOpenModal(false);
            setConfirmLoadingModal(false);
            message.error("Erro ao concluir o processo!");
        }
    }, [
        isErrorMutationForm,
        isErrorMutationUpdateStatus,
        isErrorMutationForkOrder,
    ]);

    useEffect(() => {
        if (initialValues) {
            formOrder.setFieldsValue(initialValues);
        }
    }, [initialValues, formOrder]);

    useEffect(() => {
        if (listOrdersPedentes?.Orders?.data?.length > 0) {
            const { current_page, data, last_page } = listOrdersPedentes.Orders;
            const listOrders = formatOrdersToSideList(data);

            setLoading(false);
            setPage(current_page);
            setLastPage(last_page);
            setOrders([...orders, ...listOrders]);
            setTotal((prevTotal) => prevTotal + listOrders.length);
        }
    }, [listOrdersPedentes]);

    useEffect(() => {
        if (isSuccessMutation) {
            if (unavailabilitiesFork.length > 0) {
                mutationForkorder({
                    order: orderSelected.id,
                    orderProducts: unavailabilitiesFork.map((product) =>
                        parseInt(product.id)
                    ),
                });

                return;
            }

            checkNextStatusAndUpdate();
        }
    }, [isSuccessMutation]);

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

                        if (blockedOrderAux?.status === STATUS_IN_PREPARATION) {
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

    const checkNextStatusAndUpdate = () => {
        let nextStatus = STATUS_IN_DELIVERY;

        if (availabilitiesProducts.length === 0) {
            nextStatus = STATUS_CANCELED;
        }

        mutationUpdateStatus({ orderId: orderSelected.id, status: nextStatus });
    };

    useEffect(() => {
        if (isSuccessUpdateStatus) {
            if (downloadInvoice) {
                setDownloadInvoice(false);
                mutationPrint(orderSelected.id);
            }

            setOpenModal(false);
            setConfirmLoadingModal(false);
            setOrderActive(null);
            setOrderSelected(null);
            setPage(1);
            setOrders([]);
            setTotal(0);
            setRefetch(new Date().getTime());
        }
    }, [isSuccessUpdateStatus]);

    const onConfirm = () => {
        const values = formOrder.getFieldsValue();

        const splitValues = {
            products: orderSelected.products.map((product) => {
                return {
                    id: product.id,
                    bms_product: product.bms_product,
                    unavailability:
                        values[`unavailability-${product.id}`] ?? null,
                    volume: values[`volume-${product.id}`] ?? null,
                    quantity: values[`quantity-${product.id}`] ?? null,
                    conversion: values[`conversion-${product.id}`] ?? null,
                    notes: values[`notes-${product.id}`]?.target?.value ?? null,
                    batch: values[`batch-${product.id}`] ?? null,
                };
            }),
            notes: values.notes,
        };

        if (validateProduct(splitValues.products)) {
            return;
        }

        setData({ orderId: orderSelected.id, data: splitValues });

        showModal(splitValues);
    };

    const onSave = () => {
        const values = formOrder.getFieldsValue();

        const splitValues = {
            products: orderSelected.products.map((product) => {
                return {
                    id: product.id,
                    unavailability:
                        values[`unavailability-${product.id}`] ?? null,
                    volume: values[`volume-${product.id}`] ?? null,
                    quantity: values[`quantity-${product.id}`] ?? null,
                    conversion: values[`conversion-${product.id}`] ?? null,
                    notes: values[`notes-${product.id}`]?.target?.value ?? null,
                    batch: values[`batch-${product.id}`] ?? null,
                };
            }),
            notes: values.notes,
        };

        mutationFormJustSave({ orderId: orderSelected.id, data: splitValues });
    };

    const validateProduct = (products) => {
        let hasError = false;

        products.forEach((product, key) => {
            const invalidFields = checkFields(product);
            if (invalidFields.length > 0) {
                const nameProduct =
                    orderSelected.products.find((p) => p.id === product.id)
                        .product ?? "";

                invalidFields.forEach((property) => {
                    hasError = true;
                    messageApi.open({
                        type: "error",
                        content: `O campo ${property} é obrigatório para o produto ${nameProduct}.`,
                    });
                });
            }
        });

        return hasError;
    };

    const checkFields = (product) => {
        const invalidFields = [];

        if (!product.unavailability) {
            if (product.volume === null || product.volume === undefined ) {
                if(product?.bms_product?.active == 1){
                    invalidFields.push("Volume");
                }
            }

            if (
                product.conversion === null ||
                product.conversion === undefined
            ) {
                invalidFields.push("Quantidade");
            }

            orderSelected.products.forEach((orderProduct) => {
                if (orderProduct.id == product.id) {
                    if (orderProduct?.batches?.length > 0) {
                        if (product?.batch == null) {
                            invalidFields.push("Lote");
                        }
                    }
                }
            });
        }

        return invalidFields;
    };

    const showModal = (data) => {
        const availabilities = getAvailableProducts(
            data.products,
            orderSelected.products
        );

        const unavailabilities = getUnavailableProducts(
            data.products,
            orderSelected.products
        );

        setUnavailabilitiesFork(unavailabilities);
        setAvailabilitiesProducts(availabilities);

        setDataModal(
            <div>
                <Text>
                    A encomenda possui produtos indisponíveis e será expedida de
                    forma parcial.
                </Text>
                {availabilities.length > 0 && (
                    <ModalTrackingList
                        title="Prontos para o envio:"
                        products={availabilities}
                    />
                )}
                {unavailabilities.length > 0 && (
                    <ModalTrackingList
                        title="Produtos Indisponíveis:"
                        products={unavailabilities}
                    />
                )}
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

        queryClient.invalidateQueries([
            "last-sales-and-products-pending",
            page,
        ]);
    };

    const loadOrder = (id) => {
        if (id === orderActive) {
            return;
        }

        handleOrderActive(id);

        queryClient.invalidateQueries([
            "get-order-data-pending-selected",
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

    const onConfirmModalAndPrint = () => {
        setConfirmLoadingModal(true);
        setDownloadInvoice(true);
        mutationForm(data);
    };

    const onConfirmModal = () => {
        setConfirmLoadingModal(true);
        mutationForm(data);
    };

    const onCancelModal = () => {
        setOpenModal(false);
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

    return (
        <>
            {contextHolderUnblockOrder}
            <OrderFilter
                setOrders={setOrders}
                setLoading={setLoading}
                setPage={setPage}
                setLastPage={setLastPage}
                setTotal={setTotal}
                setOrderSelected={setOrderSelected}
                status={STATUS_IN_PREPARATION}
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
                                        className="scrollable-container scrollable-container--type-2"
                                        style={{
                                            overflow: "auto",
                                            padding: "1.5rem",
                                            backgroundColor: "#fff",
                                            width: "100%",
                                        }}
                                    >
                                        <div className="purchase__top">
                                            <OrdersDetailHeader
                                                order={orderSelected}
                                                firstButton={onConfirm}
                                                secondButton={onSave}
                                                dropdownButton={dropdownButton}
                                            />
                                        </div>
                                        <div className="purchase__content" style={{ marginTop: 64 }}>
                                            <OrdersDetailInfo
                                                order={orderSelected}
                                                form={formOrder}
                                            />
                                            <Divider />
                                            <TableProductsPendent
                                                products={orderSelected.products.map(
                                                    (p) => ({ ...p, key: p.id })
                                                )}
                                                form={formOrder}
                                            />
                                        </div>
                                    </div>
                                </>
                            )
                        )}
                        <ModalTracking
                            open={openModal}
                            onConfirm={onConfirmModal}
                            onConfirmModalAndPrint={onConfirmModalAndPrint}
                            onCancel={onCancelModal}
                            confirmLoading={confirmLoadingModal}
                            data={dataModal}
                            okText="Confirmar produtos"
                            okSecondText="Confirmar produtos e Imprimir"
                            cancelText="Cancelar"
                            title="Resumo da encomenda"
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
                            Não foram encontradas encomendas!
                        </span>
                    }
                />
            )}
        </>
    );
}

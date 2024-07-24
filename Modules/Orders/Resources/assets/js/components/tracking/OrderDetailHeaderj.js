import { Button, Typography, Dropdown, Tooltip, Modal, message } from "antd";
import React, { useState, useEffect } from "react";
import {
    STATUS_IN_DELIVERY,
    STATUS_IN_PREPARATION,
    STATUS_PARTIALLY_SHIPPED,
    STATUS_PENDING,
    STATUS_DRAFT,
    getColorStatus,
} from "../../libs/utils";
import { useMutation } from "react-query";
import { SwapLeftOutlined } from "@ant-design/icons";
import ModalPreparingOrderDateChange from "./ModalPreparingOrderDateChange";
import { changeOrderDate } from "../../libs/apis";
import dayjs from "dayjs";
import "dayjs/locale/pt";

const { Title } = Typography;

export default function OrdersDetailHeader({
    order,
    firstButton,
    secondButton,
    dropdownButton,
    detail = false,
    loadingButton = false,
}) {
    const { id, status, parent_order, isDirectSale } = order;

    const [openModalChangeOrderDate, setOpenModalChangeOrderDate] =
        useState(false);

    const colorStatus = getColorStatus(status, isDirectSale);


    const user = localStorage.getItem("user")
        ? JSON.parse(localStorage.getItem("user"))
        : null;

    const SHIPPER_PROFILE = "shipper";

    const { mutate: mutateCallMissed } = useMutation(
        ["mutationChangeOrderData"],
        changeOrderDate,
        {
            onSuccess: (data) => {
                order.delivery_date = data.order?.delivery_date;
                message.success("Encomenda atualizada com sucesso!");
                setOpenModalChangeOrderDate(false);
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            },
        }
    );

    const PendingButton = () => {
        const items = [
            {
                key: "add_products",
                label: "Editar encomenda",
                onClick: dropdownButton,
            },
            {
                key: "cancel",
                label: (
                    <span
                        onClick={() =>
                            ModalCancelOrder(dropdownButton, { key: "cancel" })
                        }
                    >
                        Cancelar encomenda
                    </span>
                ),
            },
        ];

        return (
            <div className="order-list__control">
                <div>
                    {user.profile != SHIPPER_PROFILE && (
                        <Tooltip placement="top" title="Mais opções">
                            <Dropdown
                                menu={{
                                    items,
                                }}
                            >
                                <Button size="large" shape="circle">
                                    <span>...</span>
                                </Button>
                            </Dropdown>
                        </Tooltip>
                    )}
                </div>
                <div>
                    <Button
                        type="primary"
                        size="large"
                        style={{ color: "#000000" }}
                        onClick={firstButton}
                    >
                        Enviar para Preparação
                    </Button>
                </div>
            </div>
        );
    };

    const handleChangeOrderDate = () => {
        if (order) {
            let isDateInValid = validateDeliveryDate(order?.delivery_date);

            if (isDateInValid) {
                setOpenModalChangeOrderDate(true);
            } else {
                firstButton();
            }
        }
    };

    const validateDeliveryDate = (date) => {
        const currentDate = dayjs();
        const recievedDate = dayjs(date);

        if (!recievedDate.isValid()) {
            return true;
        }

        return recievedDate.isBefore(currentDate, "day");
    };

    const submitChangeOrderDate = (values) => {
        mutateCallMissed({
            orderId: order.id,
            data: values,
        });
    };

    const handleCancelModalChangeOrderDate = () => {
        setOpenModalChangeOrderDate(false);
    };

    const PreparatingButton = () => {
        const items = [
            {
                key: "add_products",
                label: "Editar encomenda",
                onClick: dropdownButton,
            },
            {
                key: "cancel",
                label: (
                    <span
                        onClick={() =>
                            ModalCancelOrder(dropdownButton, { key: "cancel" })
                        }
                    >
                        Cancelar encomenda
                    </span>
                ),
            },
        ];

        return (
            <div className="order-list__control">
                <div>
                    {user.profile != SHIPPER_PROFILE && (
                        <Tooltip placement="top" title="Mais opções">
                            <Dropdown
                                menu={{
                                    items,
                                }}
                            >
                                <Button size="large" shape="circle">
                                    <span>...</span>
                                </Button>
                            </Dropdown>
                        </Tooltip>
                    )}
                </div>
                <div>
                    <Button
                        type="primary"
                        size="large"
                        style={{ color: "#000000" }}
                        onClick={secondButton}
                    >
                        Guardar validação em stock
                    </Button>
                    <Button
                        type="primary"
                        size="large"
                        style={{ color: "#000000" }}
                        onClick={handleChangeOrderDate}
                    >
                        Enviar para Distribuição
                    </Button>
                </div>
            </div>
        );
    };

    // const PreparatingButton = () => {
    //     const items = [
    //         {
    //             key: "add_products",
    //             label: "Editar encomenda",
    //             onClick: dropdownButton,
    //         },
    //         {
    //             key: "cancel",
    //             label: (
    //                 <span
    //                     onClick={() =>
    //                         ModalCancelOrder(dropdownButton, { key: "cancel" })
    //                     }
    //                 >
    //                     Cancelar encomenda
    //                 </span>
    //             ),
    //         },
    //     ];

    //     return (
    //         <div
    //             style={{
    //                 display: "flex",
    //                 gap: 16,
    //             }}
    //         >
    //             {user.profile != SHIPPER_PROFILE && (
    //                 <Tooltip placement="top" title="Mais opções">
    //                     <Dropdown
    //                         menu={{
    //                             items,
    //                         }}
    //                     >
    //                         <Button size="large" shape="circle">
    //                             <span>...</span>
    //                         </Button>
    //                     </Dropdown>
    //                 </Tooltip>
    //             )}

    //             <Button
    //                 type="primary"
    //                 size="large"
    //                 style={{ color: "#000000" }}
    //                 onClick={firstButton}
    //             >
    //                 Enviar para Distribuição
    //             </Button>

    // <Button
    //     type="primary"
    //     size="large"
    //     style={{ color: "#000000" }}
    //     onClick={secondButton}
    // >
    //     Imprimir para viatura
    // </Button>
    //         </div>
    //     );
    // };

    const DeliveringButton = () => (
        <>
            <Button
                type="primary"
                size="large"
                style={{ color: "#000000", marginRight: 16 }}
                onClick={firstButton}
                loading={loadingButton}
            >
                Concluir
            </Button>

            <Button
                type="primary"
                size="large"
                style={{ color: "#000000" }}
                onClick={secondButton}
            >
                Imprimir para viatura
            </Button>
        </>
    );

    const DraftButton = () => (
        <>
            <Button
                type="primary"
                size="large"
                style={{ color: "#000000", marginRight: 16 }}
                onClick={firstButton}
            >
                Editar encomenda
            </Button>
            <Button
                type="primary"
                size="large"
                style={{ color: "#000000" }}
                onClick={() => ModalCancelOrder(secondButton)}
            >
                Cancelar encomenda
            </Button>
        </>
    );

    const detailOrder = () => (
        <>
            {order.status == "completed" && (
                <Button
                    type="primary"
                    size="large"
                    style={{ color: "#000000", marginRight: 16 }}
                    onClick={firstButton}
                >
                    Duplicar encomenda
                </Button>
            )}
            <Button
                type="primary"
                size="large"
                style={{ color: "#000000" }}
                onClick={secondButton}
            >
                Imprimir para viatura
            </Button>
        </>
    );

    const ModalCancelOrder = (action, parameter = null) => {
        Modal.confirm({
            title: "Cancelar encomenda",
            content: "Estás certo de cancelar a encomenda?",
            okText: "Sim",
            cancelText: "Não",
            onOk: () => {
                console.log("action OK", action);
                if (parameter !== null) {
                    action(parameter);
                } else {
                    action();
                }
            },
            onCancel: () => {},
            okType: "primary",
            okButtonProps: {
                style: { color: "#000000", backgroundColor: "#faad14" },
            },
        });
    };

    const PartiallyShippedButton = () => {
        const items = [
            {
                key: "add_products",
                label: "Editar encomenda",
                onClick: secondButton,
            },
            {
                key: "cancel",
                label: (
                    <span
                        onClick={() =>
                            ModalCancelOrder(secondButton, { key: "cancel" })
                        }
                    >
                        Cancelar encomenda
                    </span>
                ),
            },
        ];

        return (
            <div className="order-list__control">
                <div>
                    <Tooltip placement="top" title="Mais opções">
                        <Dropdown
                            menu={{
                                items,
                            }}
                        >
                            <Button size="large" shape="circle">
                                <span>...</span>
                            </Button>
                        </Dropdown>
                    </Tooltip>
                </div>
                <div>
                    <Button
                        type="primary"
                        size="large"
                        style={{ color: "#000000" }}
                        onClick={firstButton}
                    >
                        Validar em stock
                    </Button>
                </div>
            </div>
        );
    };

    const RenderButtons = () => {
        if (detail) return detailOrder();

        switch (status) {
            case STATUS_PENDING:
                return PendingButton();
            case STATUS_IN_PREPARATION:
                return PreparatingButton();
            case STATUS_IN_DELIVERY:
                return DeliveringButton();
            case STATUS_PARTIALLY_SHIPPED:
                return PartiallyShippedButton();
            case STATUS_DRAFT:
                return DraftButton();
            default:
                return <></>;
        }
    };

    return (
        <div className="order-list__title">
            {openModalChangeOrderDate && (
                <ModalPreparingOrderDateChange
                    open={openModalChangeOrderDate}
                    onCancel={handleCancelModalChangeOrderDate}
                    order={order}
                    changeDeleiveryDate={(values) =>
                        submitChangeOrderDate(values)
                    }
                />
            )}
            <div style={{ display: "flex", flexDirection: "row" }}>
                <Title level={2} style={{ margin: 0, color: colorStatus }}>
                    {id}
                </Title>
                {status === STATUS_PARTIALLY_SHIPPED && parent_order?.id && (
                    <div>
                        <Title
                            level={2}
                            style={{
                                margin: 0,
                                color: "#595959",
                                marginLeft: 8,
                            }}
                        >
                            <SwapLeftOutlined
                                style={{ color: "#BFBFBF", marginRight: 8 }}
                            />
                            {parent_order.id}
                        </Title>
                    </div>
                )}
            </div>
            <div className="order-list__control">{RenderButtons()}</div>
        </div>
    );
}

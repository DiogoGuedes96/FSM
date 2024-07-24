import React, { useEffect, useState } from "react";
import { useMutation } from "react-query";
import {
    List,
    Typography,
    Tag,
    Divider,
    Row,
    Col,
    Button,
    Tooltip,
    message,
} from "antd";
import { RetweetOutlined } from "@ant-design/icons";
import "../../../sass/tracking.scss";
import { getColorStatus, getLabelStatus } from "../../libs/utils";
import { generateInvoice, changeOrderDate } from "../../libs/apis";
const { Text, Title } = Typography;
import dayjs from "dayjs";
import ModalPreparingOrderDateChange from "./ModalPreparingOrderDateChange";

export default function OrderFullItem({
    item,
    loadOrder,
    isOrderSearched = false,
    delivery = false,
    deliveryBtnLoading = false,
    cancelOrder = null,
}) {
    const colorStatus = getColorStatus(item?.status);
    const labelStatus = getLabelStatus(item?.status);
    const [openModalChangeOrderDate, setOpenModalChangeOrderDate] =
        useState(false);

    const formatDate = (date) => {
        return date ? date.split("T")[0].split("-").reverse().join("/") : "";
    };

    const {
        mutate: mutateGenerateInvoice,
        isLoading: isLoadingMutationGenerateInvoice,
    } = useMutation(["mutationGenerateInvoice"], generateInvoice, {
        onSuccess: (data) => {
            window.location.reload();
        },
        onError: (error) => {
            setOpenModalChangeOrderDate(false);
            message.error("Erro ao enviar a encomenda para o Primavera!");
        },
    });

    const { mutate: mutateChangeOrderDate } = useMutation(
        ["mutationChangeOrderData"],
        changeOrderDate,
        {
            onSuccess: (data) => {
                message.success("Encomenda atualizada com sucesso!");
                setOpenModalChangeOrderDate(false);
                mutateGenerateInvoice(item?.id);
            },
        }
    );

    const validateDeliveryDate = (date) => {
        const currentDate = dayjs();
        const recievedDate = dayjs(date);

        if (!recievedDate.isValid()) {
            return true;
        }

        return recievedDate.isBefore(currentDate, "day");
    };

    const handleResentBtn = (order) => {
        let isDateInValid = validateDeliveryDate(order?.delivery_date);

        if (isDateInValid) {
            setOpenModalChangeOrderDate(true);
        } else {
            mutateGenerateInvoice(item?.id);
            setOpenModalChangeOrderDate(false);
        }
    };

    const submitChangeOrderDate = (values) => {
        mutateChangeOrderDate({
            orderId: item?.id,
            data: values,
        });
    };

    const handleCancelModalChangeOrderDate = () => {
        setOpenModalChangeOrderDate(false);
    };

    return (
        <>
            {isOrderSearched ? (
                <List.Item
                    style={{ borderBlockEnd: "none" }}
                    key={item.id}
                    onClick={() => loadOrder(item.id)}
                >
                    <Row
                        align="middle"
                        style={{
                            width: "100%",
                            background: "#fff",
                            borderLeft: `4px solid ${colorStatus}`,
                            borderRadius: 4,
                            cursor: "pointer",
                        }}
                    >
                        <Row
                            align="middle"
                            style={{
                                width: "100%",
                                padding: "26px 26px 16px 26px",
                            }}
                        >
                            <Col span={8}>
                                <Title
                                    level={3}
                                    style={{
                                        margin: 0,
                                        color: "#000000",
                                        marginRight: "1rem",
                                    }}
                                >
                                    Encomenda Nº {item?.id}
                                </Title>
                            </Col>
                            <Col>
                                <Tag size="large" color={colorStatus}>
                                    <Text strong style={{ color: "#FFFFFF" }}>
                                        {labelStatus}
                                    </Text>
                                </Tag>
                            </Col>
                        </Row>
                        <Divider style={{ margin: 0 }} />
                        <Row
                            align="middle"
                            style={{
                                width: "100%",
                                padding: "26px 26px 16px 26px",
                            }}
                        >
                            <Col
                                span={8}
                                style={{
                                    display: "flex",
                                    flexDirection: "column",
                                }}
                            >
                                <Text style={{ marginRight: "1rem" }}>
                                    Nome:{" "}
                                    {item?.client?.name ??
                                        "Cliente não registado"}
                                </Text>
                                <Text style={{ marginRight: "1rem" }}>
                                    Contacto:{" "}
                                    {item?.client?.phone_1 ??
                                        item?.client?.phone_2 ??
                                        item?.client?.phone_3 ??
                                        item?.client?.phone_4 ??
                                        item?.client?.phone_5 ??
                                        "N/a"}
                                </Text>
                                <Text style={{ marginRight: "1rem" }}>
                                    ID Primavera:{" "}
                                    {item?.client?.erp_client_id ??
                                        "Cliente não registado"}
                                </Text>
                                <Text style={{ marginRight: "1rem" }}>
                                    Nº Requisição:{" "}
                                    {item?.request_number ?? "N/a"}
                                </Text>
                            </Col>
                            <Col
                                style={{
                                    display: "flex",
                                    flexDirection: "column",
                                }}
                            >
                                <Text>
                                    Data Criação:{" "}
                                    {formatDate(item?.created_at) ?? "N/a"}
                                </Text>
                                <Text>
                                    Data Validação:{" "}
                                    {formatDate(item?.created_at) ?? "N/a"}
                                </Text>
                                <Text>
                                    Morada: {item?.delivery_address ?? "N/a"}
                                </Text>
                            </Col>
                        </Row>
                    </Row>
                </List.Item>
            ) : (
                <>
                    {delivery && openModalChangeOrderDate && (
                        <>
                            <ModalPreparingOrderDateChange
                                open={openModalChangeOrderDate}
                                onCancel={handleCancelModalChangeOrderDate}
                                order={item}
                                btnLabel="Gravar e enviar para o Primavera"
                                changeDeleiveryDate={(values) =>
                                    submitChangeOrderDate(values)
                                }
                            />
                        </>
                    )}
                    <List.Item style={{ borderBlockEnd: "none" }} key={item.id}>
                        <div
                            style={{
                                display: "flex",
                                flexDirection: "column",
                                width: "100%",
                                margin: "0 16px",
                                background: "#fff",
                                padding: 16,
                                borderLeft: `4px solid ${colorStatus}`,
                                gap: 24,
                                cursor: "pointer",
                            }}
                        >
                            <div
                                style={{
                                    display: "flex",
                                    gap: 16,
                                    alignItems: "center",
                                    justifyContent: "space-between",
                                }}
                            >
                                <div
                                    style={{
                                        display: "flex",
                                        gap: 16,
                                        alignItems: "center",
                                        mouse: "pointer",
                                    }}
                                >
                                    <div onClick={() => loadOrder(item.id)}>
                                        <Title
                                            level={3}
                                            style={{
                                                margin: 0,
                                                color: "#000000",
                                            }}
                                        >
                                            {item.id}
                                        </Title>
                                    </div>
                                    {item.invoice_id ? (
                                        <Tag size="large" color={colorStatus}>
                                            <Text
                                                strong
                                                style={{ color: "#FFFFFF" }}
                                            >
                                                {item.invoice_type === "FA" &&
                                                    "Fatura No. "}
                                                {item.invoice_type === "EC" &&
                                                    "Encomenda No. "}
                                                {item.invoice_id}
                                            </Text>
                                        </Tag>
                                    ) : (
                                        <>
                                            <Tag size="large" color="#E8E8E8">
                                                <Text color="#000000">
                                                    Sem Fatura
                                                </Text>
                                            </Tag>
                                            {delivery && (
                                                <Tooltip title="Enviar para o primavera">
                                                    <Button
                                                        loading={
                                                            isLoadingMutationGenerateInvoice
                                                        }
                                                        onClick={() =>
                                                            handleResentBtn(
                                                                item
                                                            )
                                                        }
                                                    >
                                                        {" "}
                                                        <RetweetOutlined
                                                            style={{
                                                                margin: "auto",
                                                            }}
                                                        />
                                                    </Button>
                                                </Tooltip>
                                            )}
                                        </>
                                    )}
                                </div>
                                {delivery && (
                                    <div
                                        style={{
                                            display: "flex",
                                            gap: 8,
                                            flexWrap: "wrap",
                                            justifyContent: "flex-end",
                                        }}
                                    >
                                        <Button
                                            loading={deliveryBtnLoading}
                                            onClick={() => delivery(item)}
                                            type="primary"
                                        >
                                            Concluir
                                        </Button>
                                        {cancelOrder && (
                                            <Button
                                                type="default"
                                                onClick={() =>
                                                    cancelOrder(item)
                                                }
                                                loading={deliveryBtnLoading}
                                            >
                                                Cancelar
                                            </Button>
                                        )}
                                    </div>
                                )}
                            </div>
                            <div
                                style={{
                                    display: "flex",
                                    flexDirection: "column",
                                }}
                                onClick={() => loadOrder(item.id)}
                            >
                                <Text strong>{item.client}</Text>
                                <Text>Nif: {item.nif}</Text>
                                <Text>
                                    {item?.order_address ??
                                        item?.client_address?.address}
                                </Text>
                            </div>
                        </div>
                    </List.Item>
                </>
            )}
        </>
    );
}

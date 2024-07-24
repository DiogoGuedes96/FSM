import React, { useEffect, useState } from "react";
import { useQuery } from "react-query";
import { Card, Typography, List, Button } from "antd";
import { getOrderData } from "../libs/apis";

const { Text } = Typography;

import successCircle from "../../img/check-circle.svg";
import { formatDatePt } from "../libs/utils";

export default function NewOrderSummary() {
    const [orderData, setOrderData] = useState();
    const [orderId, setOrderId] = useState(null);

    const { data: dataOrderQuery, isSuccess: isSuccessQuery } = useQuery(
        ["orderData", orderId],
        async () => {
            if (!orderId) return null;
            return await getOrderData(orderId);
        }
    );

    const formatPrice = (price) =>
        price.toLocaleString("pt-PT", {
            style: "currency",
            currency: "EUR",
            minimumFractionDigits: 2,
        });

    useEffect(() => {
        if (!orderId) {
            const searchParams = new URLSearchParams(window.location.search);
            const orderId = searchParams.get("order");
            setOrderId(orderId);
        }
    }, []);

    useEffect(() => {
        if (isSuccessQuery) {
            let period = "N/a";

            if (dataOrderQuery?.delivery_period) {
                if (dataOrderQuery.delivery_period == "morning") {
                    period = "Manhã";
                }

                if (dataOrderQuery.delivery_period == "evening") {
                    period = "Tarde";
                }
            }

            let orderInfo = {
                "Nº Encomenda": dataOrderQuery?.id,
                "Nº Requisição": dataOrderQuery?.request_number,
                Nome: dataOrderQuery?.client?.name ?? "Cliente não Registado",
                "ID Primavera":
                    dataOrderQuery?.client?.erp_client_id ??
                    "Cliente não Registado",
                Telefone: dataOrderQuery?.caller_phone,
                Morada: dataOrderQuery?.delivery_address,
                Zona: dataOrderQuery?.zone?.description,
                "Resumo da Compra":
                    dataOrderQuery?.order_products?.length + " Artigos",
                "Data de Entrega": dataOrderQuery?.delivery_date
                    ? formatDatePt(dataOrderQuery?.delivery_date)
                    : "N/a",
                "Período de Entrega": period,
            };

            setOrderData(Object.entries(orderInfo));
        }
    }, [isSuccessQuery]);

    return (
        <div className="order-list__success"
            style={{
                margin: 24,
                padding: "32px",
                display: "flex",
                alignItems: "center",
                justifyContent: "center",
                background: "#ffffff",
            }}
        >
            <Card
                bodyStyle={{
                    display: "flex",
                    flexDirection: "column",
                    alignItems: "center",
                    justifyContent: "center",
                    margin: "auto",
                    border: "1px solid #F0F0F0",
                    borderRadius: 8,
                }}
            >
                <img
                    alt="Success Circle"
                    src={successCircle}
                    style={{ width: 40, height: 40, marginTop: 16 }}
                />
                <div style={{ margin: "16px 0" }}>
                    <p
                        style={{
                            fontSize: 20,
                            fontWeight: 600,
                            textAlign: "center",
                        }}
                    >
                        Nova encomenda criada com sucesso
                    </p>
                    <p style={{ fontSize: 16 }}>
                        A encomenda foi criada com sucesso e irá para validação
                        em stock.
                    </p>
                </div>
                {orderData && (
                    <List
                        style={{ width: "100%", background: "#FAFAFA" }}
                        size="small"
                        dataSource={orderData}
                        renderItem={(item) => {
                            return (
                                <List.Item
                                    style={{
                                        display: "flex",
                                        alignContent: "space-between",
                                        gap: 16,
                                    }}
                                >
                                    <Text
                                        style={{
                                            color: "#00000073",
                                            width: "30%",
                                        }}
                                    >
                                        {item[0]}
                                    </Text>
                                    <Text
                                        style={{
                                            color: "#000000E0",
                                            width: "70%",
                                            textAlign: "end",
                                        }}
                                    >
                                        {item[1]}
                                    </Text>
                                </List.Item>
                            );
                        }}
                    />
                )}
            </Card>
        </div>
    );
}

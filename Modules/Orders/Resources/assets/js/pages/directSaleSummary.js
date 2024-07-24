import React, { useEffect, useState } from "react";
import { useQuery } from "react-query";
import { Card, Typography, List } from "antd";
import { getOrderData } from "../libs/apis";

const { Text } = Typography;

import successCircle from "../../img/check-circle.svg";
import errorCircle from "../../img/close-circle.svg";
import { calculateOrderTotalValue, formatDatePt } from "../libs/utils";

export default function DirectSaleSummary() {
    const [orderData, setOrderData] = useState();
    const [orderId, setOrderId] = useState(null);
    const [cardTitle, setCardTitle] = useState(null);
    const [cardIcon, setCardIcon] = useState(null);

    const titleOrderToDistribution = "Nova encomenda criada com sucesso e enviada para Distribuição";
    const titleOrderToDraft = "A encomenda foi enviada para rascunho";
    const titleOrderWithoutInvoice = `Por algum motivo, não foi possível enviar a encomenda para o primavera. Para concluir o precesso vá ao separador de "Distribuição" e clique no botão de reenviar.`;

    const { data: dataOrderQuery, isSuccess: isSuccessQuery, isLoading: isLoadingGetOrder } = useQuery(
        ["orderData", orderId],
        async () => {
            if (!orderId) return null;
            return await getOrderData(orderId);
        }
    );

    useEffect(() => {
        if (!orderId) {
            const searchParams = new URLSearchParams(window.location.search);
            const orderId = searchParams.get("order");
            setOrderId(orderId);
        }
    }, []);

    const handleCardTitleAndIcon = (order) => {        
        if(order?.status === "draft"){
            setCardIcon(successCircle);
            return titleOrderToDraft ;
        } else {
            if (!order?.primavera_invoices?.id ){
                setCardIcon(errorCircle);
                return titleOrderWithoutInvoice;
            }

            setCardIcon(successCircle);
            return titleOrderToDistribution;
        }
    }

    useEffect(() => {
        if (isSuccessQuery) {
            let invoiceId = dataOrderQuery?.primavera_invoices?.id ?  
                dataOrderQuery?.primavera_invoices?.doc_type + '-' + dataOrderQuery?.primavera_invoices?.number + '/' + dataOrderQuery?.primavera_invoices?.doc_series
                : 
                'n/a';

            let orderInfo = {
                "Nº Encomenda": dataOrderQuery?.id,
                "Nº Requisição": dataOrderQuery?.request_number,
                Nome: dataOrderQuery?.client?.name ?? "Cliente não Registado",

                "ID Primavera":
                    dataOrderQuery?.client?.erp_client_id ??
                    "Cliente não Registado",
                Telefone: dataOrderQuery?.client?.phone_1,
                "Nº documento primavera" : invoiceId ?? 'n/a',
                "Resumo da Compra":
                    dataOrderQuery?.order_products?.length + " Artigos",
                Total: calculateOrderTotalValue(dataOrderQuery?.order_products),
            };

            setCardTitle(handleCardTitleAndIcon(dataOrderQuery));
            setOrderData(Object.entries(orderInfo));
        }
    }, [isSuccessQuery]);

    return (
        <div className="order-list__success"
            style={{
                margin: 24,
                padding: 32,
                display: "flex",
                alignItems: "center",
                justifyContent: "center",
                background: "#ffffff",
            }}
        >
            <Card
                className="order-list__summary-card"
                loading={isLoadingGetOrder}
                bodyStyle={{
                    display: "flex",
                    flexDirection: "column",
                    alignItems: "center",
                    justifyContent: "center",
                    margin: "auto",
                    border: "1px solid #F0F0F0",
                    borderRadius: 8,
                    width: "inherit",
                }}
            >
                {
                    cardIcon &&
                    <img
                        alt="Success Circle"
                        src={cardIcon}
                        style={{ width: 40, height: 40, marginTop: 16 }}
                    />
                }
                <div style={{ display:"flex", justifyContent:"center", margin: "16px 0" }}>
                    <p
                        style={{
                            fontSize: 20,
                            fontWeight: 600,
                            textAlign: "center",
                            width: '70%'
                        }}
                    >
                       {
                        cardTitle
                       }
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

import { List, Typography, Input, Form } from "antd";
import React, { useEffect, useState } from "react";
import ItemInfo from "./ItemInfo";
import {
    STATUS_PARTIALLY_SHIPPED,
    STATUS_PENDING,
    formatDatePt,
} from "../../libs/utils";
import dayjs from 'dayjs';

const { TextArea } = Input;
const { Text, Title } = Typography;

export default function OrdersDetailInfo({ order, form }) {
    const { client, status, description } = order;
    const [deliveringPeriod, setDeliveringPeriod] = useState("");

    useEffect(() => {
        let period = {
            morning: " - Manhã",
            evening: " - Tarde",
        };

        if (order?.delivery_period) {
            setDeliveringPeriod(period[order.delivery_period]);
        }
    }, [order]);

    function formatDateTime(dateTimeString) {
        const date = dayjs(dateTimeString);
        
        const formattedTime = date.format('HH:mm');
        
        return formattedTime;
      }

    return (
        <>
            <Form form={form}>
                <List
                    style={{
                        borderTop: "1px solid #E8E8E8",
                        borderBottom: "1px solid #E8E8E8",
                    }}
                >
                    <List.Item span={24}>
                        <ItemInfo
                            title="Nome"
                            value={client?.name ?? "Cliente não registado"}
                        />
                        <ItemInfo
                            title="Data Criação"
                            value={
                                status != STATUS_PARTIALLY_SHIPPED
                                    ? formatDatePt(order.writen_date)
                                    : formatDatePt(
                                          order?.parent_order?.writen_date
                                      )
                            }
                        />
                        <ItemInfo
                            title="Hora"
                            value={
                                formatDateTime(order?.created_at)
                            }
                        />
                    </List.Item>
                    <List.Item span={24} style={{ backgroundColor: "#FAFAFA" }}>
                        <ItemInfo
                            title="Contacto"
                            value={
                                order.caller_phone
                                    ? order.caller_phone
                                    : client?.phone
                                    ? client.phone
                                    : "N/a"
                            }
                        />
                        <ItemInfo
                            title="Data de Entrega"
                            value={
                                order.delivery_date
                                    ? (order.delivery_date
                                          ? formatDatePt(order.delivery_date)
                                          : "") + deliveringPeriod
                                    : "N/a"
                            }
                        />
                    </List.Item>
                    <List.Item span={24}>
                        <ItemInfo
                            title="ID Primavera"
                            value={client?.primaveraId ?? "N/a"}
                        />
                        {status === STATUS_PARTIALLY_SHIPPED && (
                            <ItemInfo
                                title="Nº Requisição"
                                value={order.request_number ?? "N/a"}
                            />
                        )}
                        {status != STATUS_PARTIALLY_SHIPPED ? (
                            <ItemInfo
                                title="Nº Requisição"
                                value={order.request_number ?? "N/a"}
                            />
                        ) : (
                            <ItemInfo
                                title="Data Validação"
                                value={
                                    formatDatePt(order?.writen_date) ?? "N/a"
                                }
                            />
                        )}
                    </List.Item>
                    <List.Item
                        span={24}
                        style={{ backgroundColor: "#FAFAFA", paddingRight: 4 }}
                    >
                        <ItemInfo
                            title="Morada"
                            value={
                                order.delivery_address
                                    ? order.delivery_address
                                    : client &&
                                      client?.address &&
                                      client?.address?.address
                                    ? client?.address.address +
                                      (client?.address?.postal_code
                                          ? ", " + client.address.postal_code
                                          : "")
                                    : "N/a"
                            }
                        />
                        <ItemInfo title="Zona" value={order.zona ?? "N/a"} />
                    </List.Item>
                    <List.Item
                        span={24}
                        style={{ backgroundColor: "#FAFAFA", paddingRight: 4 }}
                    >
                        <ItemInfo
                            title="Criado por"
                            value={
                                order.order_writer?.name
                                    ? order.order_writer?.name
                                    : "N/a"
                            }
                        />
                        <ItemInfo
                            title="Preparado por"
                            value={
                                order.order_preparer?.name
                                    ? order.order_preparer?.name
                                    : "N/a"
                            }
                        />
                    </List.Item>
                </List>
                <div style={{ display: "flex", flexDirection: "column" }}>
                    <Title level={5} strong>
                        Notas da Encomenda
                    </Title>
                    {status === STATUS_PENDING ? (
                        <Form.Item name="notes">
                            <TextArea
                                style={{ marginTop: "0.5rem" }}
                                rows={3}
                                maxLength={1024}
                            />
                        </Form.Item>
                    ) : (
                        <Text style={{ marginTop: "0.5rem" }}>
                            {description}
                        </Text>
                    )}
                </div>
            </Form>
        </>
    );
}

import { Card, Col, Divider, Row, Typography } from "antd";
import React, { useState, useEffect } from "react";
import { RiseOutlined, FallOutlined } from "@ant-design/icons";
import { getKpis } from "../libs/apis";
import "./base.scss";

const { Title, Text } = Typography;

const tabPeriod = [
    {
        key: "now",
        tab: "Hoje",
    },
    {
        key: "week",
        tab: "Semana",
    },
    {
        key: "month",
        tab: "Mês",
    },
];

const tabPeriodInvoices = [
    {
        key: "week",
        tab: "Últimos 7 dias",
    },
    {
        key: "month",
        tab: "Últimos 30 dias",
    },
];

export default function Dashboard() {
    const [ordersPeriod, setOrdersPeriod] = useState("now");
    const [invoicePeriod, setInvoicePeriod] = useState("week");
    const [callcenterPeriod, setCallcenterPeriod] = useState("now");

    // getKpis
    const [kpisData, setKpisData] = useState({
        invoices: {
            isLoading: true,
            data: { received: 0, to_receive: 0, pending: 0 },
        },
        clients: { isLoading: true, data: { total: 0 } },
        calls: { isLoading: true, data: { lost: 0, hangup: 0, total: 0 } },
        orders: {
            isLoading: true,
            data: { by_calls: 0, pending: 0, completed: 0 },
        },
    });

    const fetchKpisData = ({ entity, period }) => {
        kpisData[entity].isLoading = true;

        getKpis({ entity, period })
            .then((response) => {
                let _kpisData = { ...kpisData };

                Object.keys(response).forEach(
                    (key) => (_kpisData[entity].data[key] = response[key])
                );
                _kpisData[entity].isLoading = false;

                setKpisData(_kpisData);
            })
            .catch((error) => console.error("API error:", error.response));
    };

    const invoiceTabChange = (key) => {
        setInvoicePeriod(key);

        fetchKpisData({ entity: "invoices", period: key });
    };

    const callcenterTabChange = (key) => {
        setCallcenterPeriod(key);

        fetchKpisData({ entity: "calls", period: key });
    };

    const ordersTabChange = (key) => {
        setOrdersPeriod(key);

        fetchKpisData({ entity: "orders", period: key });
    };

    const calculateValues = (value, cash = true) => {
        if (cash && value)
            return value.toLocaleString("pt-PT", {
                style: "currency",
                currency: "EUR",
            });

        return value;
    };

    const calculatePercentage = (old_value, current_value) => {
        const calc =
            (100 * parseFloat(current_value)) / parseFloat(old_value) - 100;

        if (typeof value !== "number") {
            return "";
        }

        return calc.toFixed(1).replace(".", ",") + "%";
    };

    useEffect(() => {
        Object.keys(kpisData).forEach((key) => {
            let period = "now";
            if (key === "invoices") {
                period = "week";
            }
            fetchKpisData({ entity: key, period });
        });
    }, []);

    return (
        <div
            style={{
                margin: 24,
                display: "flex",
                flexDirection: "column",
                gap: 24,
            }}
        >
            <Row gutter={20} style={{rowGap: '24px'}}>
                <Col span={24} md={18}>
                    <Card
                        className="dashboard__card-1"
                        tabList={tabPeriod}
                        onTabChange={ordersTabChange}
                        loading={kpisData.orders.isLoading}
                        tabBarExtraContent={
                            <Title style={{ margin: 0 }} level={4}>
                                Encomendas
                            </Title>
                        }
                    >
                        <div className="dashboard__data-list">
                            <div>
                                <Text type="secondary" strong>
                                    Gerada a partir de chamada
                                </Text>
                                <Title style={{ margin: 0 }} level={3}>
                                    {calculateValues(
                                        kpisData.orders.data.by_calls,
                                        false
                                    )}
                                </Title>
                            </div>
                            <Divider
                                type="vertical"
                                style={{ height: "4em" }}
                            />
                            <div>
                                <Text type="secondary" strong>
                                    Pendentes
                                </Text>
                                <Title style={{ margin: 0 }} level={3}>
                                    {calculateValues(
                                        kpisData.orders.data.pending,
                                        false
                                    )}
                                </Title>
                            </div>
                            <Divider
                                type="vertical"
                                style={{ height: "4em" }}
                            />
                            <div>
                                <Text type="secondary" strong>
                                    Concluídas
                                </Text>
                                <Title style={{ margin: 0 }} level={3}>
                                    {calculateValues(
                                        kpisData.orders.data.completed,
                                        false
                                    )}
                                </Title>
                            </div>
                        </div>
                    </Card>
                </Col>
                <Col span={24} md={6}>
                    <Card
                        style={{ height: "100%" }}
                        loading={kpisData.clients.isLoading}
                        bodyStyle={{
                            height: "100%",
                            display: "flex",
                            flexDirection: "column",
                            justifyContent: "space-between",
                            padding: "24px",
                        }}
                    >
                        <Title style={{ margin: 0 }} level={4}>
                            Total Clientes
                        </Title>
                        <div>
                            <Text type="secondary" strong>
                                Clientes ativos
                            </Text>
                            <Title style={{ margin: 0 }} level={4}>
                                {kpisData.clients.data.total}
                            </Title>
                        </div>
                    </Card>
                </Col>
            </Row>
            <Row gutter={20} style={{rowGap: '24px'}}>
                <Col span={24} xl={12}>
                    <Card
                        className="dashboard__card-1"
                        tabList={tabPeriodInvoices}
                        onTabChange={invoiceTabChange}
                        loading={kpisData.invoices.isLoading}
                        tabBarExtraContent={
                            <Title style={{ margin: 0 }} level={4}>
                                Faturação
                            </Title>
                        }
                    >
                        <div className="dashboard__data-list">
                            <div>
                                <Text type="secondary" strong>
                                    Valor Total
                                </Text>
                                <div
                                    style={{
                                        display: "flex",
                                        alignItems: "baseline",
                                        gap: 8,
                                    }}
                                >
                                    <Title style={{ margin: 0 }} level={3}>
                                        {calculateValues(
                                            kpisData.invoices.data.received_total_value
                                        )}
                                    </Title>
                                    {invoicePeriod != "now" &&
                                        (kpisData.invoices.data.received_total_value > 0 ||
                                            kpisData.invoices.data
                                                .last_received_total_value > 0) && (
                                            <>
                                                {kpisData.invoices.data
                                                    .received_total_value >
                                                kpisData.invoices.data
                                                    .last_received_total_value ? (
                                                    <RiseOutlined
                                                        style={{
                                                            alignSelf: "left",
                                                            color: "#52C41A",
                                                            fontSize: 24,
                                                        }}
                                                    />
                                                ) : (
                                                    <FallOutlined
                                                        style={{
                                                            alignSelf: "left",
                                                            color: "#F5222D",
                                                            fontSize: 24,
                                                        }}
                                                    />
                                                )}

                                                <Text type="secondary" strong>
                                                    {calculatePercentage(
                                                        kpisData.invoices.data
                                                            .last_received_total_value,
                                                        kpisData.invoices.data
                                                            .received_total_value
                                                    )}
                                                </Text>
                                            </>
                                        )}
                                </div>
                            </div>
                        </div>
                    </Card>
                </Col>
                <Col span={24} xl={12}>
                    <Card
                        className="dashboard__card-1"
                        tabList={tabPeriod}
                        onTabChange={callcenterTabChange}
                        loading={kpisData.calls.isLoading}
                        tabBarExtraContent={
                            <Title style={{ margin: 0 }} level={4}>
                                Central telefónica
                            </Title>
                        }
                    >
                        <div className="dashboard__data-list">
                            <div>
                                <Text type="secondary" strong>
                                    Perdidas
                                </Text>
                                <Title style={{ margin: 0 }} level={3}>
                                    {calculateValues(
                                        kpisData.calls.data.lost,
                                        false
                                    )}
                                </Title>
                            </div>
                            <Divider
                                type="vertical"
                                style={{ height: "4em" }}
                            />
                            <div>
                                <Text type="secondary" strong>
                                    Recebidas
                                </Text>
                                <Title style={{ margin: 0 }} level={3}>
                                    {calculateValues(
                                        kpisData.calls.data.hangup,
                                        false
                                    )}
                                </Title>
                            </div>
                            <Divider
                                type="vertical"
                                style={{ height: "4em" }}
                            />
                            <div>
                                <Text type="secondary" strong>
                                    Total de ligações
                                </Text>
                                <Title style={{ margin: 0 }} level={3}>
                                    {calculateValues(
                                        kpisData.calls.data.total,
                                        false
                                    )}
                                </Title>
                            </div>
                        </div>
                    </Card>
                </Col>
            </Row>
        </div>
    );
}

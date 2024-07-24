import React, { useEffect, useState } from "react";
import { Drawer, List, Row, Space, Tag, Typography } from "antd";
import {
    UserOutlined,
    CreditCardOutlined,
    HistoryOutlined,
    ShoppingOutlined,
} from "@ant-design/icons";
import ClientItemBar from "./itemBar";
import CollapseListBar from "./collapseListBar";
import EmptyBar from "./emptyBar";
import LastSalesBar from "./lastSalesBar";
import moment from "moment";
import { getLastSalesAndProducts } from "../../libs/apis";
import { useQuery } from "react-query";
import { gray, red, gold } from "@ant-design/colors";

const { Text } = Typography;

export default function clientBar({ open, onClose, client }) {
    const [valuesLastSales, setValuesLastSales] = useState([]);
    const [pendingInvoices, setPendingInvoices] = useState([]);
    const [period, setPeriod] = useState("7");
    const [lastInvoice, setLastInvoice] = useState(null);

    const sevenDaysAgo = moment().subtract(7, 'days').format('YYYY-MM-DD');
    const [startDate, setStartDate] = useState(sevenDaysAgo);
    const [endDate, setEndDate] = useState(moment().format('YYYY-MM-DD'));

    const { data: lastSalesAndProducts, refetch: refetchLastSalesAndProducts } = useQuery(
        ["getAllClients", client?.id, startDate, endDate],
        () => {
            return getLastSalesAndProducts(client?.id, startDate, endDate)
        },
        {
            refetchOnWindowFocus: false,
            onSuccess: async (data) => {
                if (data && data?.products) {
                    setValuesLastSales(data?.products);
                }
            },
        }
    );
    useEffect(() => {
        if (client && client?.primaveraClient) {
            refetchLastSalesAndProducts();
            if(client?.primaveraClient?.invoices?.length > 0){

                setLastInvoice(client?.primaveraClient?.last_invoice ? client?.primaveraClient?.last_invoice : null);
                setPendingInvoices(
                    client?.primaveraClient?.invoices ? client?.primaveraClient?.invoices : []
                );
            }
        }
    }, [client]);

    const handleFilterDays = (value) => {
        if (!value) {
            return;
        }

        const daysInPast = moment().subtract(value, 'days').format('YYYY-MM-DD');
        setStartDate(daysInPast);
        setPeriod(value);
        refetchLastSalesAndProducts();
    };
    function formatLastInvoceDate(dateString) {
        if(dateString){
            const months = [
                'Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun',
                'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'
            ];
        
            const [year, month, day] = dateString.split('-');
            const monthIndex = parseInt(month) - 1;
        
            return `${parseInt(day)} ${months[monthIndex]} ${parseInt(year)}`;
        }

        return '';
    }

    function daysSinceLastInvoice(dateString) {
        if(dateString){
            const currentDate = moment();
            const inputDate = moment(dateString, 'YYYY-MM-DD');
            const daysDifference = currentDate.diff(inputDate, 'days');

            return '(há ' + daysDifference  + " dias)";
        }

        return '';
    }

    const handleFilterDateRange = (dates) => {
        if (!dates) {
            handleFilterDays(period);
            return;
        }

        const [start, end] = dates;
        setStartDate(moment(start.$d).format('YYYY-MM-DD'));
        setEndDate(moment(end.$d).format('YYYY-MM-DD'));

        refetchLastSalesAndProducts();
    };

    const getPaymentConditions = (paymentConditions) => {
        let condition = "";
        switch (paymentConditions) {
            case "1":
                condition = "Pronto Pagamento";
                break;
            case "2":
                condition = "Fatura 30 dias";
                break;
            case "3":
                condition = "Fatura a 7 dias";
                break;
            case "4":
                condition = "Fatura a 60 dias";
                break;
            case "5":
                condition = "Fim do Mês";
                break;
            case "7":
                condition = "Até dia 5 do Próximo Mês";
                break;
            case "8":
                condition = "Prestações";
                break;
            case "9":
                condition = "Até dia 15 do Próximo Mês";
                break;
            default:
                condition = "Não Definido";
                break;
        }
        return condition;
    };

    const sectionTitle = (icon, title, alert = null) => {
        let sectionIcon = null;
        switch (icon) {
            case UserOutlined:
                sectionIcon = (
                    <UserOutlined style={{ fontSize: 22, color: gray[2] }} />
                );
                break;
            case CreditCardOutlined:
                sectionIcon = (
                    <CreditCardOutlined
                        style={{ fontSize: 22, color: gray[2] }}
                    />
                );
                break;
            case HistoryOutlined:
                sectionIcon = (
                    <HistoryOutlined style={{ fontSize: 22, color: gray[2] }} />
                );
                break;
            case ShoppingOutlined:
                sectionIcon = (
                    <ShoppingOutlined
                        style={{ fontSize: 22, color: gray[2] }}
                    />
                );
                break;
        }

        return (
            <Row style={{ display: "flex", justifyContent: "space-between" }}>
                <Space>
                    {sectionIcon}
                    <Text style={{ fontWeight: 600 }}>{title}</Text>
                </Space>
                {alert && (
                    <Tag style={{ fontWeight: 400 }} color={red[5]}>
                        {alert}
                    </Tag>
                )}
            </Row>
        );
    };

    return (
        <Drawer closable={false} onClose={onClose} open={open} width={544}>
            <div style={{ height: "100%" }}>
                <div>
                    {sectionTitle(
                        UserOutlined,
                        <Text style={{ fontSize: 16 }}>Cliente</Text>,
                        client?.age_debt > 0 ? "Faturas Pendentes" : null
                    )}
                    <List style={{ marginTop: "1rem" }}>
                        <ClientItemBar
                            field="Contacto"
                            value={client?.call?.caller_phone}
                        />
                        <ClientItemBar field="Nome" value={client?.name} />
                        <ClientItemBar
                            field="ID Primavera"
                            value={client?.erp_client_id}
                        />
                    </List>
                </div>
                {!client ? (
                    <EmptyBar />
                ) : (
                    <>
                        <div style={{ paddingTop: 32 }}>
                            {sectionTitle(
                                CreditCardOutlined,
                                <Text style={{ fontSize: 16 }}>
                                    Condições e dívidas
                                </Text>
                            )}
                            <List style={{ marginTop: "1rem" }}>
                                <ClientItemBar
                                    field="Condição de pagamento"
                                    value={getPaymentConditions(
                                        client?.payment_condition
                                    )}
                                />
                                <ClientItemBar
                                    field="Último pagamento"
                                    value={formatLastInvoceDate(lastInvoice?.invoice_date ? lastInvoice?.invoice_date : null)}
                                    extra={daysSinceLastInvoice(lastInvoice?.invoice_date ? lastInvoice?.invoice_date : null)}

                                />
                                <ClientItemBar
                                    field="Valor total da dívida"
                                    value={client?.primaveraClient?.sumInvoiceDebt?.toLocaleString(
                                        "pt-PT",
                                        {
                                            style: "currency",
                                            currency: "EUR",
                                            minimumFractionDigits: 2,
                                        }
                                    )}
                                    tag="true"

                                />
                            </List>
                        </div>
                        <div style={{ paddingTop: 32 }}>
                            {sectionTitle(
                                HistoryOutlined,
                                <Text style={{ fontSize: 16 }}>
                                    Últimas faturas
                                </Text>
                            )}
                            <List style={{ marginTop: "1rem" }}>
                                <CollapseListBar items={pendingInvoices} />
                            </List>
                        </div>
                        <div style={{ paddingTop: 32 }}>
                            {sectionTitle(
                                ShoppingOutlined,
                                <Text style={{ fontSize: 16 }}>
                                    Últimas compras
                                </Text>
                            )}
                            <div style={{ marginTop: "1rem" }}>
                                <LastSalesBar
                                    handleFilterDays={handleFilterDays}
                                    handleFilterDateRange={
                                        handleFilterDateRange
                                    }
                                    values={valuesLastSales}
                                />
                            </div>
                        </div>
                    </>
                )}
            </div>
        </Drawer>
    );
}

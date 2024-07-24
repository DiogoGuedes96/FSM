import React from "react";
import { Collapse, List, Row, Tooltip, Typography } from "antd";
import EmptyBar from "./emptyBar";
const { Text } = Typography;
import { gray } from "@ant-design/colors";
import { formatDatePt } from "../../libs/utils";

export default function CollapseListBar({ items }) {
    const filterTotalValuesIsNotNull = (faturas) => {
        return faturas.filter((item) => item?.total_value);
    };

    const parseSumTotalValues = (item, type = 'total') => {
        const sumTotalValue = item.fa
            .reduce((sum, item) => {
                if(type === 'total') {
                    if (item?.total_value) {
                        sum += parseFloat(item?.total_value);
                    }
                }

                if(type === 'pendent'){
                    if (item?.pendent_value) {
                        sum += parseFloat(item?.pendent_value);
                    }
                }

                return sum;
            }, 0)
            .toLocaleString("pt-PT", {
                style: "currency",
                currency: "EUR",
                minimumFractionDigits: 2,
            });

        if (!isNaN(sumTotalValue) || !sumTotalValue) {
            return "";
        }

        return sumTotalValue;
    };

    const handlePastInvoiceLabel = (month) => {
        switch (month) {
            case 1:
                return "Últimos 30 dias";                
            case 2:
                return "De 30 a 60 dias";                
            case 3:
                return "De 60 a 90 dias";        
            case 4:
                return "Mais de 90 dias";          
            default:
                break;
        }
    } 

    const renderTextWithTooltip = (item) => {
        const textElement = (
            <Text style={{ fontSize: 16 }}>
                {handlePastInvoiceLabel(item?.months)}
            </Text>
        );
    
        if (item?.months === 4) {
            return (
                <Tooltip title="Apenas faturas com valores pendentes">
                    {textElement}
                </Tooltip>
            );
        }
    
        return textElement;
    };

    return (
        <Collapse ghost>
            {items.map((item, index) => (
            <Collapse.Panel
                className="collapse-list-bar-custom"
                panelKey={index}
                header={
                    <Row
                        style={{
                            display: "flex",
                            justifyContent: "space-between",
                        }}
                    >
                        <div>
                            {renderTextWithTooltip(item)}
                        </div>
                        <Text  style={{ fontSize: 16, fontWeight: 500 }}>
                            {filterTotalValuesIsNotNull(item.fa)
                                ? parseSumTotalValues(item, 'total')
                                : null}
                        </Text>
                    </Row>
                }
                extra={
                    <Text type="danger" style={{ fontSize: 16, fontWeight: 500, 'margin': 8 }}>
                        {filterTotalValuesIsNotNull(item.fa)
                            ? parseSumTotalValues(item, 'pendent')
                            : null}
                    </Text>
                }
                >
                    {filterTotalValuesIsNotNull(item.fa) ? (
                        <List
                            dataSource={filterTotalValuesIsNotNull(item.fa)}
                            renderItem={(item, index) => {
                                return (
                                    <List.Item
                                        key={index}
                                        style={{ backgroundColor: "#FAFAFA" }}
                                    >
                                        <Text>
                                            {formatDatePt(item?.invoice_date)}
                                            <Text style={{ color: gray[1] }}>
                                                {' (' + item?.doc_type + '-' + item?.number + '/' + item?.doc_series + ')'}
                                            </Text>
                                        </Text>
                                        <div>
                                            <Text>
                                                {parseFloat(
                                                    item.total_value
                                                ).toLocaleString("pt-PT", {
                                                    style: "currency",
                                                    currency: "EUR",
                                                    minimumFractionDigits: 2,
                                                })}
                                            </Text>
                                            <Text type="danger" style={{ marginLeft: 8 }}>
                                                {parseFloat(
                                                    item?.pendent_value
                                                ).toLocaleString("pt-PT", {
                                                    style: "currency",
                                                    currency: "EUR",
                                                    minimumFractionDigits: 2,
                                                })}
                                            </Text>
                                        </div>
                                    </List.Item>
                                );
                            }}
                        />
                    ) : (
                        <div
                            style={{
                                backgroundColor: "#FAFAFA",
                                padding: "24px",
                                borderRadius: "8px",
                            }}
                        >
                            <EmptyBar text="Sem faturas registadas para o período" />
                        </div>
                    )}
                </Collapse.Panel>
            ))}
        </Collapse>
    );
}

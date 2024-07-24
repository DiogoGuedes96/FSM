import { Table, Typography, Input, Checkbox, Form } from "antd";
import React from "react";

const { Text, Title } = Typography;
const { TextArea } = Input;

import Placeholder from "../../../img/productPlaceholder.jpg";

const columns = [
    {
        title: "Descrição",
        dataIndex: "product",
        key: "product",
        render: (_, record) => {
            return (
                <div
                    style={{
                        display: "flex",
                        alignItems: "center",
                        gap: "0.5rem",
                    }}
                >
                    <img
                        style={{
                            objectFit: "cover",
                            objectPosition: "center",
                            maxWidth: 55,
                            maxHeight: 55,
                        }}
                        alt="Product Image"
                        src={record.image ?? Placeholder}
                    />
                    <Text style={{ wordBreak: "inherit", minWidth: "150px" }}>
                        {record.product}
                    </Text>
                </div>
            );
        },
    },
    {
        title: "ID Primavera",
        dataIndex: "erp_product_id",
        key: "erp_product_id",
    },
    {
        title: "Lote",
        dataIndex: "batch",
        key: "batch",
        width: 130,
        render: (_, record) => {
            return (
                <Text style={{ wordBreak: "inherit", minWidth: "130px" }}>
                    {record?.order_product_batch ?? ""}
                </Text>
            );
        },
    },
    {
        title: "Enc",
        dataIndex: "quantity",
        key: "quantity",
        width: 130,
        render: (_, record) => {
            return (
                <Text>
                    {record.quantity} {record.sale_unit ?? record.unit}
                </Text>
            );
        },
    },
    {
        title: "Quantidade",
        dataIndex: "conversion",
        key: "conversion",
        render: (_, record) => {
            return (
                <>
                    {record.conversion && (
                        <Text>
                            {record.conversion} {record.unit}
                        </Text>
                    )}
                </>
            );
        },
    },
    {
        title: "Volume",
        dataIndex: "volume",
        key: "volume",
    },
    {
        title: "UN",
        dataIndex: "unit",
        key: "unit",
    },
    {
        title: "Preço UN",
        dataIndex: "sale_price",
        key: "sale_price",
        width: 100,
        render: (_, record) => {
            return (
                <Text style={{ wordBreak: "inherit", minWidth: "100px" }}>
                    {isNaN(parseFloat(record.unit_price).toFixed(2))
                        ? ""
                        : parseFloat(record.unit_price).toFixed(2) + "€"}
                </Text>
            );
        },
    },
    {
        title: "Descontos",
        dataIndex: "discount",
        key: "discount",
        render: (_, record) => {
            return <Text>{parseFloat(record.discount).toFixed(2)}€</Text>;
        },
    },
    {
        title: "IVA",
        dataIndex: "iva",
        key: "iva",
        render: (_, record) => {
            return <Text>{record.iva}%</Text>;
        },
    },
    {
        title: "Total",
        dataIndex: "total",
        key: "total",
        width: 100,
        render: (_, record) => {
            return (
                <Text style={{ wordBreak: "inherit", minWidth: "100px" }}>
                    {parseFloat(record.total || 0).toFixed(2)}€
                </Text>
            );
        },
    },
];

export default function TableProductsGeneric({ products, form, short }) {
    return (
        <>
            <Form form={form}>
                <Table
                    dataSource={products}
                    columns={columns}
                    pagination={false}
                    rowKey="id"
                    expandable={{
                        expandedRowRender: (record) => {
                            return (
                                <>
                                    <TextArea
                                        size="large"
                                        placeholder="Observações"
                                        readOnly={true}
                                        defaultValue={record?.notes}
                                    />
                                </>
                            );
                        },
                        expandRowByClick: true,
                        showExpandColumn: false,
                    }}
                />
            </Form>
        </>
    );
}

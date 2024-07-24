import { Table, Typography, Space, Form, Button } from "antd";
import React from "react";

const { Text } = Typography;

import Placeholder from "../../../img/productPlaceholder.jpg";

export default function TableProductsPartially({
    products,
    form,
    actionDelete,
}) {
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
                        <Text
                            style={{ wordBreak: "inherit", minWidth: "150px" }}
                        >
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
            title: "Quantidade",
            dataIndex: "quantity",
            key: "quantity",
            render: (_, record) => {
                return (
                    <Text>
                        {record.quantity} {record.sale_unit ?? record.unit}
                    </Text>
                );
            },
        },
        {
            title: "Ajuste/Conv.",
            dataIndex: "conversion",
            key: "conversion",
            render: (_, record) => {
                return (
                    <Text>
                        {record.conversion
                            ? record.conversion + record.unit
                            : "-"}
                    </Text>
                );
            },
        },
        {
            title: "Volume",
            dataIndex: "volume",
            key: "volume",
            render: (_, record) => {
                return <Text>{record.volume ?? "-"}</Text>;
            },
        },
        {
            title: "Ação",
            dataIndex: "total",
            key: "total",
            render: (_, record) => {
                return (
                    <Space size="middle">
                        <Button
                            onClick={() => actionDelete(record)}
                            type="link"
                        >
                            Remover
                        </Button>
                    </Space>
                );
            },
        },
    ];

    return (
        <>
            <Form form={form}>
                <Table
                    dataSource={products}
                    columns={columns}
                    pagination={false}
                />
            </Form>
        </>
    );
}

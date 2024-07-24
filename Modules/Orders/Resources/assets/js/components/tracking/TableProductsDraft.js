import { Table, Typography, Form } from "antd";
import React from "react";
import Placeholder from "../../../img/productPlaceholder.jpg";

const { Text } = Typography;

export default function TableProductsDraft({ products, form }) {
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
    ];

    return <Table dataSource={products} columns={columns} pagination={false} />;
}

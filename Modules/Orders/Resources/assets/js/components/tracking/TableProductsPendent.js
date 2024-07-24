import {
    Table,
    Typography,
    Input,
    Checkbox,
    Form,
    Select,
    message,
    Button,
    InputNumber,
} from "antd";
import React, { useEffect, useState } from "react";
import { TableOutlined } from "@ant-design/icons";

const { Text, Title } = Typography;
const { TextArea } = Input;

import Placeholder from "../../../img/productPlaceholder.jpg";

export default function TableProductsPendent({
    products,
    form,
    initialValues,
}) {
    const [tableMobile, setTableMobile] = useState(true);

    const handleKeyPress = (event) => {
        const allowedKeys = [
            "Backspace",
            "ArrowUp",
            "ArrowDown",
            "ArrowLeft",
            "ArrowRight",
            "Tab",
        ];
        const validNumberRegex = /^[0-9.]$/;

        if (
            !validNumberRegex.test(event.key) &&
            !allowedKeys.includes(event.key)
        ) {
            event.preventDefault();
            message.error(
                "O campo Quantidade só aceita valores numéricos, inteiros ou decimais!"
            );
        }
    };

    const handleKeyPressQtd = (event) => {
        const allowedKeys = [
            "Backspace",
            "ArrowUp",
            "ArrowDown",
            "ArrowLeft",
            "ArrowRight",
            "Tab",
        ];
        const validNumberRegex = /^[0-9.]$/;

        if (
            !validNumberRegex.test(event.key) &&
            !allowedKeys.includes(event.key)
        ) {
            event.preventDefault();
            message.error(
                "O campo Quantidade só aceita valores numéricos, inteiros ou decimais!"
            );
        }
    };

    const handleMobileTable = () => {
        setTableMobile(!tableMobile);
    };

    const columns = [
        {
            title: "Descrição",
            dataIndex: "product",
            key: "product",
            render: (_, record) => {
                return (
                    <div
                        className="responsive-table__title"
                        data-label="Descrição"
                    >
                        <div
                            className="responsive-table__cell"
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
                                style={{
                                    wordBreak: "inherit",
                                    minWidth: "150px",
                                }}
                            >
                                {record.product}
                            </Text>
                        </div>
                    </div>
                );
            },
        },
        {
            title: "ID Primavera",
            dataIndex: "erp_product_id",
            key: "erp_product_id",
            render: (_, record) => {
                return (
                    <div
                        className="responsive-table__title"
                        data-label="ID Primavera"
                    >
                        <div className="responsive-table__cell">
                            {record.erp_product_id}
                        </div>
                    </div>
                );
            },
        },
        {
            title: "Enc",
            dataIndex: "quantity",
            key: "quantity",
            render: (_, record) => {
                return (
                    <div className="responsive-table__title" data-label="Enc">
                        {/* <div
                            className="responsive-table__cell responsive-table__cell-input-2"
                            style={{
                                display: "flex",
                                gap: 8,
                                alignItems: "baseline",
                                flexWrap: "nowrap",
                            }}
                        > */}
                        {/* <Form.Item name={`quantity-${record.id}`}>
                                <Input
                                    maxLength={6}
                                    max={999999}
                                    min={0}
                                    size="large"
                                    onKeyDown={handleKeyPressQtd}
                                />
                            </Form.Item> */}
                        <Text style={{ whiteSpace: "nowrap" }}>
                            {record.quantity} {record.sale_unit ?? record.unit}
                        </Text>
                        {/* </div> */}
                    </div>
                );
            },
        },
        {
            title: "Lote",
            width: "20%",
            dataIndex: "batch",
            key: "batch",
            render: (_, record) => {
                let batches = record?.batches
                let filteredBatches = [];

                if(batches?.length > 0){
                    batches.forEach(item => {
                        if (item?.quantity > 0) {
                            filteredBatches.push(item);
                        }
                    });
                }

                return (
                    <div className="responsive-table__title" data-label="Lote">
                        <div className="responsive-table__cell responsive-table__cell-select">
                            <Form.Item
                                name={`batch-${record.id}`}
                                style={{ minWidth: 135 }}
                            >
                                <Select
                                    size="large"
                                    value={filteredBatches}
                                    options={filteredBatches}
                                    allowClear
                                />
                            </Form.Item>
                        </div>
                    </div>
                );
            },
        },
        {
            title: "Quantidade",
            dataIndex: "convertion",
            key: "convertion",
            render: (_, record) => {
                return (
                    <div
                        className="responsive-table__title"
                        data-label="Quantidade"
                    >
                        <div className="responsive-table__cell responsive-table__cell-input-1">
                            <Form.Item name={`conversion-${record.id}`}>
                                <InputNumber
                                    min={0}
                                    max={999999}
                                    maxLength={6}
                                    size="large"
                                    suffix={record.unit}
                                    onKeyDown={handleKeyPress}
                                    className="responsive-table__field"
                                    type="number"
                                    inputMode="decimal"
                                />
                                {/* <Input
                                    min={0}
                                    max={999999}
                                    maxLength={6}
                                    size="large"
                                    suffix={record.unit}
                                    onKeyDown={handleKeyPress}
                                    className="responsive-table__field"
                                    type="number"
                                    inputMode="numeric"
                                /> */}
                            </Form.Item>
                        </div>
                    </div>
                );
            },
        },
        {
            title: "Volume",
            dataIndex: "volume",
            key: "volume",
            render: (_, record) => {
                return (
                    <div
                        className="responsive-table__title"
                        data-label="Volume"
                    >
                        <div className="responsive-table__cell responsive-table__cell-input-1">
                            <Form.Item name={`volume-${record.id}`}>
                                {/* <Input size="large" /> */}
                                <InputNumber
                                    min={0}
                                    max={999999}
                                    maxLength={6}
                                    size="large"
                                    suffix={record.unit}
                                    onKeyDown={handleKeyPress}
                                    className="responsive-table__field"
                                    type="number"
                                    inputMode="numeric"
                                />
                            </Form.Item>
                        </div>
                    </div>
                );
            },
        },
        {
            title: "Disponibilidade",
            dataIndex: "disponibility",
            key: "disponibility",
            render: (_, record) => {
                return (
                    <div
                        className="responsive-table__title"
                        data-label="Disponibilidade"
                    >
                        <div className="responsive-table__cell">
                            <Form.Item
                                name={`unavailability-${record.id}`}
                                valuePropName="checked"
                            >
                                <Checkbox>Indisponível</Checkbox>
                            </Form.Item>
                        </div>
                    </div>
                );
            },
        },
    ];

    return (
        <>
            <div className="orders__title-box">
                <Title level={3}>Validação em Estoque</Title>
                <button
                    className={`orders__change-table-button ${
                        tableMobile ? "active" : ""
                    }`}
                    title="Mudar visualização para tabela padrão"
                    type="button"
                    onClick={handleMobileTable}
                >
                    <TableOutlined />
                </button>
            </div>
            <Form form={form} initialValues={initialValues}>
                <Table
                    className={`orders__responsive-table ${
                        tableMobile ? "mobile-on" : ""
                    }`}
                    dataSource={products}
                    columns={columns}
                    pagination={false}
                    rowKey="id"
                    expandable={{
                        expandedRowRender: (record) => {
                            return (
                                <>
                                    <Form.Item
                                        name={`notes-${record.id}`}
                                        valuePropName="checked"
                                    >
                                        <TextArea
                                            size="large"
                                            placeholder="Observações"
                                            maxLength="255"
                                            showCount
                                            defaultValue={record?.notes ?? ""}
                                        />
                                    </Form.Item>
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

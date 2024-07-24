import React from "react";
import { Form, Typography, Button, Modal, InputNumber } from "antd";

import "dayjs/locale/pt";

const { Title } = Typography;

export default function ModalCompletedOrder({
    open,
    onCancel,
    order,
    setInvoiceNumber,
    form,
    isLoading,
}) {
    return (
        <>
            <Modal
                closable={false}
                open={open}
                onCancel={() => onCancel()}
                footer={null}
            >
                <div style={{ display: "flex", flexDirection: "column" }}>
                    {order && (
                        <>
                            <Title
                                level={4}
                                style={{ marginTop: 16, marginBottom: 24 }}
                            >
                                Atribua um numero de fatura para a encomenda n°{" "}
                                {order.id}
                            </Title>

                            <Form
                                layout="vertical"
                                form={form}
                                onFinish={setInvoiceNumber}
                            >
                                <Form.Item
                                    label="Nº da Série"
                                    name="serie"
                                    rules={[
                                        {
                                            required: true,
                                            message: "Insira o Nº da Série",
                                        },
                                    ]}
                                >
                                    <InputNumber
                                        style={{ width: "100%" }}
                                        maxLength={4}
                                        placeholder={new Date().getFullYear()}
                                    />
                                </Form.Item>

                                <Form.Item
                                    label="Nº da Fatura"
                                    name="number"
                                    rules={[
                                        {
                                            required: true,
                                            message: "Insira o Nº da Fatura",
                                        },
                                    ]}
                                >
                                    <InputNumber
                                        style={{ width: "100%" }}
                                        maxLength={12}
                                        placeholder="012345"
                                    />
                                </Form.Item>
                                <Form.Item style={{ marginTop: 48 }}>
                                    <Button
                                        loading={isLoading}
                                        block
                                        size="large"
                                        type="primary"
                                        htmlType="submit"
                                        style={{ color: "#000" }}
                                    >
                                        Gravar
                                    </Button>
                                </Form.Item>
                            </Form>
                        </>
                    )}
                </div>
            </Modal>
        </>
    );
}

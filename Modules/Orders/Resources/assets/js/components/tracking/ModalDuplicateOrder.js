import React, { useState, useEffect } from "react";
import {
    Form,
    Input,
    Typography,
    DatePicker,
    Select,
    Checkbox,
    Button,
    Modal,
} from "antd";
import dayjs from "dayjs";
import "dayjs/locale/pt";
import moment from "moment";

const { TextArea } = Input;
const { Title, Text } = Typography;

const deliveryPeriodOptions = [
    { value: "morning", label: "Manhã" },
    { value: "evening", label: "Tarde" },
];
const dateFormat = "DD/MM/YYYY";

export default function ModalDuplicateOrder({
    open,
    onCancel,
    order,
    duplicateOrder,
}) {
    const [
        initialValuesDuplicateOrderForm,
        setInitialValuesDuplicateOrderForm,
    ] = useState({});
    const [submitDuplicateOrderForm] = Form.useForm();

    useEffect(() => {
        let initial = {
            order_id: order.id,
            request: "",
            priority: order.priority ? true : false,
            notes: order.description,
            delivery_period: order.delivery_period,
            delivery_date: order.delivery_date
                ? dayjs(moment().format(dateFormat), "DD/MM/YYYY").add(
                      1,
                      "hour"
                  )
                : null,
        };

        if (Object.keys(initialValuesDuplicateOrderForm).length) {
            submitDuplicateOrderForm.setFieldsValue(
                initialValuesDuplicateOrderForm
            );
        } else {
            setInitialValuesDuplicateOrderForm(initial);
        }
    }, [initialValuesDuplicateOrderForm, submitDuplicateOrderForm]);

    const disabledDate = (current) => {
        return current && current < moment().startOf("day");
    };

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
                            <Title level={4} style={{ marginTop: 28 }}>
                                Duplicar encomenda n° {order.id}
                            </Title>

                            <Form
                                layout="vertical"
                                form={submitDuplicateOrderForm}
                                initialValues={initialValuesDuplicateOrderForm}
                                onFinish={duplicateOrder}
                            >
                                <Form.Item
                                    label="Nº Requisição"
                                    name="request"
                                    rules={[
                                        {
                                            required: true,
                                            message:
                                                "Insira o Nº da Requisição",
                                        },
                                    ]}
                                >
                                    <Input maxLength={255} showCount />
                                </Form.Item>

                                <Form.Item
                                    label="Notas para a Encomenda"
                                    name="notes"
                                    rules={[
                                        {
                                            required: true,
                                            message:
                                                "Insira as Notas para a Encomenda",
                                        },
                                    ]}
                                >
                                    <TextArea
                                        rows={4}
                                        maxLength={1024}
                                        showCount={true}
                                    />
                                </Form.Item>

                                <label>Entrega</label>

                                <div style={{ display: "flex", gap: 16 }}>
                                    <Form.Item
                                        name="delivery_date"
                                        style={{ width: "100%" }}
                                        rules={[
                                            {
                                                required: true,
                                                message:
                                                    "Insira a Data de Entrega",
                                            },
                                        ]}
                                    >
                                        <DatePicker
                                            placeholder="Data"
                                            size="large"
                                            format={dateFormat}
                                            disabledDate={disabledDate}
                                            showToday={false}
                                            style={{ width: "100%" }}
                                        />
                                    </Form.Item>

                                    <Form.Item
                                        name="delivery_period"
                                        style={{ width: "100%" }}
                                        rules={[
                                            {
                                                required: true,
                                                message:
                                                    "Selecione o Periodo de Entrega",
                                            },
                                        ]}
                                    >
                                        <Select
                                            allowClear
                                            size="large"
                                            placeholder="Periodo"
                                            options={deliveryPeriodOptions}
                                            style={{ width: "100%" }}
                                        />
                                    </Form.Item>
                                </div>

                                <Form.Item
                                    name="priority"
                                    valuePropName="checked"
                                    style={{
                                        background: "#F5F5F5",
                                        fontWeight: "bold",
                                        paddingLeft: "1rem",
                                    }}
                                >
                                    <Checkbox>Encomenda Prioritária</Checkbox>
                                </Form.Item>

                                <Form.Item>
                                    <Button
                                        block
                                        size="large"
                                        type="primary"
                                        htmlType="submit"
                                        style={{ color: "#000" }}
                                    >
                                        Duplicar
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

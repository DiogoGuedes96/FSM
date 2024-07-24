import React, { useState, useEffect } from "react";
import {
    Form,
    Typography,
    DatePicker,
    Button,
    Modal,
} from "antd";
import dayjs from "dayjs";
import "dayjs/locale/pt";
import moment from "moment";
import { formatDatePt } from "../../libs/utils";

const { Title, Text } = Typography;

const deliveryPeriodOptions = [
    { value: "morning", label: "Manhã" },
    { value: "evening", label: "Tarde" },
];
const dateFormat = "DD/MM/YYYY";

export default function ModalPreparingOrderDateChange({
    open,
    onCancel,
    order,
    btnLabel,
    changeDeleiveryDate,
}) {
    const [initialValuesChangeOrderDateForm, setInitialValuesChangeOrderDateForm] = useState({});
    const [blockSubmitBtn, setBlockSubmitBtn] = useState(false);
    const [submitChangeOrderDateForm] = Form.useForm();

    useEffect(() => {
        let delivery_date = dayjs(moment().format(dateFormat), "DD/MM/YYYY").add(
            1,
            "hour"
        )

        setBlockSubmitBtn(validateDeliveryDate(delivery_date));

        let initial = {
            order_id: order.id,
            priority: order.priority ? true : false,
            delivery_period: order.delivery_period,
            delivery_date: delivery_date
        };

        if (Object.keys(initialValuesChangeOrderDateForm).length) {
            submitChangeOrderDateForm.setFieldsValue(
                initialValuesChangeOrderDateForm
            );
        } else {
            setInitialValuesChangeOrderDateForm(initial);
        }
    }, [initialValuesChangeOrderDateForm, submitChangeOrderDateForm]);

    const disabledDate = (current) => {
        return current && current < moment().startOf("day");
    };

    const validateDeliveryDate = (date) => {
        const currentDate = dayjs();
        const recievedDate = dayjs(date);
      
        if (!recievedDate.isValid()) {
          return true;
        }
      
        return recievedDate.isBefore(currentDate, 'day');
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
                                Data de entrega da encomenda n° {order.id}
                            </Title>
                            <Text style={{ marginBottom: 16 }}>O primavera não aceita datas de entrega inferiores à atual.</Text>
                            <Text style={{ marginBottom: 16 }}>A data de entrega para a encomenda n° <Text strong>{order.id}</Text> é <Text strong>{formatDatePt(order.delivery_date)}</Text></Text>
                            <Text style={{ marginBottom: 16 }}>Escolha uma data de hoje em diante.</Text>
                            <Form
                                layout="vertical"
                                form={submitChangeOrderDateForm}
                                initialValues={initialValuesChangeOrderDateForm}
                                onFinish={changeDeleiveryDate}
                            >
                                <Form.Item
                                    name="delivery_date"
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
                                        onChange = {(value) => setBlockSubmitBtn(validateDeliveryDate(value))}
                                    />
                                </Form.Item>
                                <Form.Item>
                                    <Button
                                        block
                                        size="large"
                                        type="primary"
                                        htmlType="submit"
                                        style={{ color: "#000" }}
                                        disabled={blockSubmitBtn}
                                    >
                                        {
                                            btnLabel ? btnLabel : "Gravar"
                                        }
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

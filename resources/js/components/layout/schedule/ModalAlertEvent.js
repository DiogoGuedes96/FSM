import React, { useState, useEffect } from "react";
import {
    Button,
    Form,
    Modal,
    Pagination,
    Select,
    Typography,
    List,
    Badge,
    Card,
} from "antd";
import { InfoCircleOutlined } from "@ant-design/icons";
import dayjs from "dayjs";
import { addRemenberDelay, setDoneRemember } from "bmslibs/apiSchedule";
import { useMutation } from "react-query";
import moment from "moment";

const { Title, Text } = Typography;

const optionsRepeatTime = [
    {
        value: 0,
        label: "Adiar por",
    },
    {
        value: 5,
        label: "5 minutos antes",
    },
    {
        value: 15,
        label: "15 minutos antes",
    },
    {
        value: 30,
        label: "30 minutos antes",
    },
];

const rememberLabel = [
    {
        value: 5,
        label: " Avisar 5 minutos antes.",
    },
    {
        value: 15,
        label: " Avisar 15 minutos antes.",
    },
    {
        value: 30,
        label: " Avisar 30 minutos antes.",
    },
];

export default function ModalAlertEvent({ open, event, close }) {
    const [current, setCurrent] = useState(1);
    const [bntLoading, setBntLoading] = useState(false);
    const [events, setEvents] = useState([]);
    const [selected, setSelected] = useState(0);
    const [FormReschedule] = Form.useForm();
    const [initialValues, setInitialValues] = useState({ rememberTime: 0 });

    useEffect(() => {
        if (event) {
            setEvents(event);
        }
    }, [event]);

    const { mutate: mutateReminderDelay, isSuccess: isSuccessDelay } =
        useMutation("addReminderDelay", addRemenberDelay);

    const { mutate: mutationDone, isSuccess: isSuccessDone } = useMutation(
        "addReminderDelay",
        setDoneRemember
    );

    const formatDateToShow = (date) => {
        const newDate = moment(date).format("DD/MM/YYYY") ?? "";

        return newDate;
    };

    useEffect(() => {
        if (isSuccessDelay) {
            // message.info(
            //     `O alerta foi silenciado e retornará${getRememberTimeLabel(
            //         FormReschedule.getFieldsValue().rememberTime
            //     )}`
            // );
            setBntLoading(false);
            if (events.length === 1) {
                close(true);
            } else {
                const oldSelected = selected;
                const next = oldSelected - 1 < 0 ? 0 : oldSelected - 1;

                setCurrent(next + 1);
                setSelected(next);
                events.splice(oldSelected, 1);
            }
        }
    }, [isSuccessDelay]);

    useEffect(() => {
        if (isSuccessDone) {
            setBntLoading(false);
            if (events.length === 1) {
                close(true);
            } else {
                const oldSelected = selected;
                const next = oldSelected - 1 < 0 ? 0 : oldSelected - 1;

                setCurrent(next + 1);
                setSelected(next);
                events.splice(oldSelected, 1);
            }
        }
    }, [isSuccessDone]);

    const onChange = (page) => {
        if (page < 1) {
            page = 1;
        }

        if (events[page - 1] === undefined) {
            return;
        }

        setCurrent(page);
        setSelected(page - 1);
    };

    const onFinish = (values) => {
        if (values.rememberTime === 0) {
            // message.error("É necessario informar o tempo.");
            return;
        }

        setBntLoading(true);
        const data = {
            event_id: events[selected]?.id,
            delay: values.rememberTime,
        };

        mutateReminderDelay(data);
    };

    const getOptionsRepeatTime = (value) => {
        return optionsRepeatTime.find((item) => item.value === value)?.label;
    };

    const getRememberTimeLabel = (value) => {
        if (value == 0 || value == undefined || value == null) {
            return 0;
        }

        return value !== null && JSON.parse(value).length > 0
            ? rememberLabel.find((item) => item.value === JSON.parse(value)[0])
                  ?.label
            : 0;
    };

    return (
        <>
            {open && events.length && (
                <Modal open={open} footer={null} onCancel={() => close()}>
                    {events[selected]?.read_at == null ? (
                        <Badge.Ribbon text="Lembrete não Lido">
                            <div
                                style={{
                                    display: "flex",
                                    alignItems: "center",
                                    gap: 5,
                                    margin: "16px 0px",
                                }}
                            >
                                <InfoCircleOutlined
                                    style={{ color: "#FAAD14", fontSize: 20 }}
                                />
                                <Title style={{ margin: 0, fontSize: '22px' }} level={3}>
                                    Atenção
                                </Title>
                            </div>
                        </Badge.Ribbon>
                    ) : (
                        <div
                            style={{
                                display: "flex",
                                alignItems: "center",
                                gap: 16,
                                margin: "16px 0px",
                            }}
                        >
                            <InfoCircleOutlined
                                style={{ color: "#FAAD14", fontSize: 20 }}
                            />
                            <Title style={{ margin: 0 }} level={3}>
                                Atenção
                            </Title>
                        </div>
                    )}
                    <Form
                        form={FormReschedule}
                        layout="vertical"
                        onFinish={onFinish}
                        initialValues={initialValues}
                    >
                        <div
                            style={{
                                display: "flex",
                                flexDirection: "column",
                                gap: 24,
                            }}
                        >
                            <Title
                                level={4}
                                style={{
                                    alignContent: "center",
                                    textAlign: "center",
                                }}
                            >
                                {
                                    events[selected]?.schedule_event?.reminder
                                        ?.title
                                }
                            </Title>
                            <List size="small">
                                <List.Item>
                                    <Text strong>{" Tipo de lembrete: "}</Text>
                                    <Text>
                                        {getRememberTimeLabel(
                                            events[selected]?.schedule_event
                                                ?.reminder?.delay
                                        )}
                                    </Text>
                                </List.Item>
                                <List.Item
                                    style={{ backgroundColor: "#f5f5f5" }}
                                >
                                    <Text strong>{" Data: "}</Text>
                                    <Text>
                                        {events[selected]?.schedule_event
                                            ?.reminder?.date
                                            ? formatDateToShow(
                                                  events[selected]
                                                      .schedule_event.reminder
                                                      .date
                                              )
                                            : "N/a"}
                                    </Text>
                                </List.Item>
                                <List.Item>
                                    <Text strong>{"Hora: "}</Text>
                                    <Text>
                                        {events[selected]?.schedule_event?.time}
                                    </Text>
                                </List.Item>
                                <List.Item
                                    style={{ backgroundColor: "#f5f5f5" }}
                                >
                                    <Text strong>{"Nome do Cliente: "}</Text>
                                    <Text>
                                        {events[selected]?.schedule_event
                                            ?.reminder?.client_name ?? "N/a"}
                                    </Text>
                                </List.Item>
                                <List.Item>
                                    <Text strong>{"Contacto: "}</Text>
                                    <Text>
                                        {events[selected]?.schedule_event
                                            ?.reminder?.client_phone ?? "N/a"}
                                    </Text>
                                </List.Item>
                                <List.Item
                                    style={{ backgroundColor: "#f5f5f5" }}
                                >
                                    <div>
                                        <Text strong>
                                            {"Notas da Tarefa: "}
                                        </Text>
                                        <Text>
                                            {events[selected]?.schedule_event
                                                ?.reminder?.notes ?? "n/a"}
                                        </Text>
                                    </div>
                                </List.Item>
                            </List>
                            <Form.Item name="rememberTime">
                                <Select
                                    size="large"
                                    options={optionsRepeatTime}
                                />
                            </Form.Item>
                        </div>
                        <div
                            style={{
                                display: "flex",
                                flexDirection: "row",
                                justifyContent: "space-evenly",
                                gap: 16,
                            }}
                        >
                            <Button
                                size="large"
                                onClick={() => {
                                    const data = {
                                        event_id: events[selected]?.id,
                                    };
                                    setBntLoading(true);
                                    mutationDone(data);
                                }}
                                block={true}
                                loading={bntLoading}
                            >
                                Descartar
                            </Button>
                            <Button
                                size="large"
                                type="primary"
                                htmlType="submit"
                                block={true}
                                loading={bntLoading}
                            >
                                Reagendar
                            </Button>
                        </div>
                        <div
                            style={{
                                display: "flex",
                                justifyContent: "end",
                                marginTop: 24,
                            }}
                        >
                            <Pagination
                                defaultCurrent={1}
                                pageSize={1}
                                onChange={onChange}
                                total={events.length}
                                size="small"
                            />
                        </div>
                    </Form>
                </Modal>
            )}
        </>
    );
}

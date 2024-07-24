import React, { useEffect, useState } from "react";
import {
    Row,
    Col,
    Button,
    Typography,
    Tooltip,
    Dropdown,
    Divider,
    DatePicker,
    Spin,
} from "antd";
import { SyncOutlined, SearchOutlined } from "@ant-design/icons";
import moment from "moment";
import dayjs from "dayjs";
import { getNextSevenDays, formatEventToShowFromList } from "bmslibs/utils.js";
import ModalCancelEvent from "./ModalCancelEvent";
import ModalConfirmCancelEvent from "./ModalConfirmCancelEvent";
import { QueryClient, useMutation, useQuery } from "react-query";
import { getEventsByDates, deleteEvent } from "bmslibs/apiSchedule";

const { Title, Text } = Typography;
const currentDate = dayjs();

export default function ListEventsSchedule({ goTo }) {
    const [selectDay, setSelectDay] = useState(currentDate);
    const [openCalendar, setOpenCalendar] = useState(false);
    const [nextDays, setNextDays] = useState(getNextSevenDays(new Date()));
    const [openModal, setOpenModal] = useState(false);
    const [openModalConfirm, setOpenModalConfirm] = useState(false);
    const [itemEvent, setItemEvent] = useState(null);
    const [events, setEvents] = useState(getNextSevenDays(new Date()));
    const [loading, setLoading] = useState(false);
    const [loadingCancel, setLoadingCancel] = useState(false);
    const queryClient = new QueryClient();

    const { data: dataEvent, refetch } = useQuery(
        "get-events",
        () => {
            setLoading(true);
            return getEventsByDates({
                days: nextDays.map((item) =>
                    moment(item.date).format("YYYY-MM-DD")
                ),
            });
        },
        { refetchOnWindowFocus: false }
    );

    const { mutate: mutateDeleteEvent, isSuccess: isSuccessDelete } =
        useMutation("delete-event", deleteEvent);

    useEffect(() => {
        if (isSuccessDelete) {
            queryClient.invalidateQueries("get-events");
            refetch();

            setLoadingCancel(false);
            setOpenModalConfirm(false);
            setItemEvent(null);
        }
    }, [isSuccessDelete]);

    useEffect(() => {
        queryClient.invalidateQueries("get-events");
        refetch();
    }, [nextDays]);

    useEffect(() => {
        if (dataEvent) {
            const dataKeys = Object.keys(dataEvent.events);

            for (let i = 0; i < dataKeys.length; i++) {
                let eventDay = formatEventToShowFromList(
                    dataEvent.events[dataKeys[i]]
                );

                populateEventsToNextDates(dataKeys[i], eventDay);
            }

            setLoading(false);
        }
    }, [dataEvent]);

    const populateEventsToNextDates = (date, events) => {
        let nextDaysAndEvents = nextDays.map((item) => {
            if (moment(item.date).format("YYYY-MM-DD") == date) {
                item.events = events;
            }

            return item;
        });

        setEvents(nextDaysAndEvents);
    };

    const onSelect = (value) => {
        const newDate = value.format("YYYY-MM-DD");

        if (newDate != selectDay.format("YYYY-MM-DD")) {
            setSelectDay(dayjs(newDate, "YYYY-MM-DD"));
            setNextDays(getNextSevenDays(new Date(newDate)));
            setOpenCalendar(!openCalendar);
        }
    };

    const items = [
        {
            key: "1",
            label: <span>Excluir</span>,
            onClick: () => setOpenModal(true),
        },
        {
            key: "2",
            label: <span>Editar</span>,
            onClick: () => goTo({ page: "form", event: itemEvent }),
        },
    ];

    const formCancelEvent = (deleteType) => {
        setItemEvent((prev) => ({ ...prev, delete: deleteType }));
        setOpenModal(false);
        setOpenModalConfirm(true);
    };

    const confirmCancelEvent = () => {
        setLoadingCancel(true);

        mutateDeleteEvent({ id: itemEvent.id, type: itemEvent.delete });
    };

    return (
        <>
            <div>
                <Row
                    style={{
                        display: "flex",
                        alignItems: "center",
                        margin: "24px 0px",
                    }}
                >
                    <Col xs={24} sm={15} md={17}>
                        <Title style={{ margin: "0 0 10px" }} level={4}>
                            Agenda
                        </Title>
                    </Col>
                    <Col xs={24} sm={9} md={7}>
                        <Button
                            size="large"
                            style={{ width: '100%', color: "#000000" }}
                            type="primary"
                            onClick={() => {
                                goTo({ page: "form" });
                            }}
                        >
                            Adicionar uma tarefa
                        </Button>
                    </Col>
                </Row>
                <Row
                    style={{
                        display: "flex",
                        alignItems: "center",
                        marginBottom: 16,
                    }}
                >
                    <Col span={13} md={6}>
                        <DatePicker
                            defaultValue={selectDay}
                            size="large"
                            format="DD/MM/YYYY"
                            onChange={onSelect}
                        />
                    </Col>
                </Row>
                <Row
                    style={{
                        display: "flex",
                        alignItems: "center",
                        justifyContent: "space-around",
                        flexDirection: "row",
                        flexWrap: "nowrap",
                    }}
                >
                    {loading && (
                        <div
                            style={{ textAlign: "center", margin: 32 }}
                            className="container-spin"
                        >
                            <Spin size="large" />
                        </div>
                    )}
                    {!loading &&
                        events.map((day, index) => {
                            return (
                                <div
                                    style={{
                                        textAlign: "center",
                                        width: "100%",
                                    }}
                                >
                                    <div
                                        style={{
                                            height: 30,
                                            display: "flex",
                                            alignItems: "center",
                                            justifyContent: "center",
                                        }}
                                    >
                                        <Text
                                            style={{
                                                margin: 0,
                                                fontWeight: 600,
                                            }}
                                        >
                                            <strong></strong>
                                            {day.weekDay
                                                .charAt(0)
                                                .toUpperCase()}
                                        </Text>
                                    </div>
                                    <div
                                        style={{
                                            background: "#FFFBE6",
                                            height: 30,
                                            display: "flex",
                                            alignItems: "center",
                                            justifyContent: "center",
                                            ...(index === 0
                                                ? {
                                                      borderTopLeftRadius: 4,
                                                      borderBottomLeftRadius: 4,
                                                  }
                                                : {}),
                                            ...(index === events.length - 1
                                                ? {
                                                      borderTopRightRadius: 4,
                                                      borderBottomRightRadius: 4,
                                                  }
                                                : {}),
                                        }}
                                    >
                                        <Text
                                            style={
                                                day.day === selectDay.date()
                                                    ? {
                                                          background: "#FAAD1F",
                                                          borderRadius: 5,
                                                          padding: "5px 8px",
                                                      }
                                                    : null
                                            }
                                        >
                                            {day.day}
                                        </Text>
                                    </div>
                                </div>
                            );
                        })}
                </Row>
                <Row
                    style={{
                        display: "flex",
                        flexDirection: "column",
                        justifyContent: "center",
                    }}
                >
                    {!loading &&
                        events.map((day, index) => {
                            return (
                                <>
                                    <Row>
                                        <Col span={24}>
                                            <Title
                                                style={{ margin: "24px 0px" }}
                                                level={5}
                                            >
                                                {day.label}
                                            </Title>
                                        </Col>
                                    </Row>
                                    <Row
                                        style={{
                                            display: "flex",
                                            flexDirection: "column",
                                            gap: 8,
                                        }}
                                    >
                                        {day?.events?.length ? (
                                            day.events.map((event) => (
                                                <Row
                                                    style={{
                                                        display: "flex",
                                                        flexDirection: "row",
                                                        alignItems: "center",
                                                        borderLeft:
                                                            "7px solid #fbc53d",
                                                        borderRadius: 8,
                                                        padding: "10px 16px",
                                                        borderTop:
                                                            "1px solid #0000000F",
                                                        borderBottom:
                                                            "1px solid #0000000F",
                                                        borderRight:
                                                            "1px solid #0000000F",
                                                    }}
                                                >
                                                    <Col
                                                        span={19}
                                                        style={{
                                                            cursor: "pointer",
                                                        }}
                                                    >
                                                        {event.time}
                                                        <Divider
                                                            style={{
                                                                height: "2.5em",
                                                            }}
                                                            type="vertical"
                                                            onClick={() =>
                                                                goTo({
                                                                    page: "show",
                                                                    event,
                                                                })
                                                            }
                                                        />
                                                        <Text>
                                                            <strong>
                                                                {event.title}
                                                            </strong>
                                                        </Text>
                                                    </Col>
                                                    <Col
                                                        span={5}
                                                        style={{
                                                            display: "flex",
                                                            justifyContent:
                                                                "end",
                                                            gap: 8,
                                                        }}
                                                    >
                                                        {event.recurrency_type !=
                                                            "never" && (
                                                            <Tooltip title="Evento recorrente">
                                                                <SyncOutlined
                                                                    style={{
                                                                        fontSize: 20,
                                                                        color: "#747474",
                                                                    }}
                                                                />
                                                            </Tooltip>
                                                        )}
                                                        <Tooltip title="Mais opções">
                                                            <Dropdown
                                                                trigger="click"
                                                                onClick={() =>
                                                                    setItemEvent(
                                                                        event
                                                                    )
                                                                }
                                                                menu={{ items }}
                                                            >
                                                                <Button
                                                                    size="large"
                                                                    shape="circle"
                                                                >
                                                                    <span>
                                                                        ...
                                                                    </span>
                                                                </Button>
                                                            </Dropdown>
                                                        </Tooltip>
                                                    </Col>
                                                </Row>
                                            ))
                                        ) : (
                                            <Row>
                                                <Col
                                                    style={{
                                                        display: "flex",
                                                        gap: 16,
                                                        flexDirection: "row",
                                                    }}
                                                >
                                                    <SearchOutlined
                                                        style={{
                                                            fontSize: 20,
                                                            color: "#00000073",
                                                        }}
                                                    />
                                                    <Text
                                                        style={{
                                                            color: "#00000073",
                                                        }}
                                                    >
                                                        Não há eventos agendados
                                                    </Text>
                                                </Col>
                                            </Row>
                                        )}
                                    </Row>
                                </>
                            );
                        })}
                </Row>
            </div>
            {openModal && (
                <ModalCancelEvent
                    onOk={formCancelEvent}
                    open={openModal}
                    onCancel={() => setOpenModal(false)}
                />
            )}
            {openModalConfirm && (
                <ModalConfirmCancelEvent
                    onOk={confirmCancelEvent}
                    open={openModalConfirm}
                    loading={loadingCancel}
                    onCancel={() => setOpenModalConfirm(false)}
                />
            )}
        </>
    );
}

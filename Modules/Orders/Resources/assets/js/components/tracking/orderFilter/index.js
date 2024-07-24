import React, { useState, useEffect } from "react";
import { useQuery, useMutation } from "react-query";
import { Select, Row, Col, message, DatePicker } from "antd";
import { getAllzones, getListOrdersWithZone } from "../../../libs/apis";
import {
    STATUS_CANCELED,
    STATUS_COMPLETED,
    STATUS_IN_DELIVERY,
    formatOrderToFullList,
    formatOrdersToSideList,
} from "../../../libs/utils";
import dayjs from "dayjs";

const currentDate = dayjs();

const deliveryPeriodOptions = [
    { value: "morning", label: "Manhã" },
    { value: "evening", label: "Tarde" },
];

export default function OrderFilter({
    setOrders,
    setLoading,
    setPage,
    setLastPage,
    setTotal,
    setOrderSelected,
    status,
}) {
    const [zones, setZones] = useState(false);
    const [filterPage, setFilterPage] = useState(1);
    const [selectDay, setSelectDay] = useState(undefined);
    const [selectPeriod, setSelectPeriod] = useState(undefined);
    const [selectZone, setSelectZone] = useState(undefined);
    const [previousSelectDay, setPreviousSelectDay] = useState(undefined);
    const [previousSelectPeriod, setPreviousSelectPeriod] = useState(undefined);
    const [previousSelectZone, setPreviousSelectZone] = useState(undefined);

    const { data: zonesData } = useQuery(["allZones"], getAllzones, {
        refetchOnWindowFocus: false,
    });

    const {
        data: orderListData,
        isSuccess: isSuccessListOrders,
        isError: isErrorListOrders,
        mutate: mutateListOrdersWithFilters,
    } = useMutation(["mutationListOrdersWithFilter"], getListOrdersWithZone);

    const handleInput = (value = null) => {
        if (value) {
            setSelectZone(value);
        } else {
            setSelectZone(undefined);
        }
    };

    const handleDateChange = (date, dateString) => {
        if (!!dateString) {
            setSelectDay(dateString);
        } else {
            setSelectDay(undefined);
        }
    };

    const handlePeriodChange = (value) => {
        if (value) {
            setSelectPeriod(value);
        } else {
            setSelectPeriod(undefined);
        }
    };

    useEffect(() => {
        if (
            selectDay !== previousSelectDay ||
            selectPeriod !== previousSelectPeriod ||
            selectZone !== previousSelectZone
        ) {
            setPreviousSelectDay(selectDay);
            setPreviousSelectPeriod(selectPeriod);
            setPreviousSelectZone(selectZone);

            setOrderSelected(null);
            setLoading(true);

            mutateListOrdersWithFilters({
                page: filterPage,
                status: status,
                zoneId: selectZone,
                deliveryDate: selectDay,
                deliveryPeriod: selectPeriod,
            });
        }
    }, [selectDay, selectPeriod, selectZone]);

    useEffect(() => {
        if (zonesData && zonesData.length > 0) {
            const options = zonesData.map((zoneData) => ({
                value: zoneData.id.toString(),
                label: `${zoneData.zone} - ${zoneData.description}`,
            }));

            setZones(options);
        }
    }, [zonesData]);

    useEffect(() => {
        if (isErrorListOrders) {
            message.error("Erro ao carregar encomendas !");
            setOrderSelected(null);
            setLoading(false);
            setPage(1);
            setLastPage(0);
            setOrders([]);
            setTotal(0);
        }
    }, [isErrorListOrders]);

    useEffect(() => {
        if (orderListData) {
            if (orderListData?.Orders?.data?.length > 0) {
                const { current_page, data, last_page } = orderListData.Orders;
                let listOrders = [];

                if (
                    status === STATUS_IN_DELIVERY ||
                    status === STATUS_COMPLETED ||
                    status === STATUS_CANCELED
                ) {
                    listOrders = formatOrderToFullList(data);
                } else {
                    listOrders = formatOrdersToSideList(data);
                }

                setOrderSelected(null);
                setLoading(false);
                setPage(current_page);
                setLastPage(last_page);
                setOrders(listOrders);
                setTotal((prevTotal) => prevTotal + listOrders.length);
            } else {
                setOrderSelected(null);
                setLoading(false);
                setPage(1);
                setLastPage(0);
                setOrders([]);
                setTotal(0);
            }
        }
    }, [orderListData, isSuccessListOrders]);

    return (
        <div style={{ padding: "0 20px" }}>
            <Row style={{ gap: 16 }}>
                <Col style={{ backgroundColor: "#F5F5F5" }} span={24} xl={8}>
                    <Select
                        showSearch
                        allowClear
                        size="large"
                        style={{ width: "100%" }}
                        placeholder="Selecione a zona..."
                        optionFilterProp="children"
                        filterOption={(input, option) =>
                            (option?.label ?? "").includes(input)
                        }
                        onChange={(value) => handleInput(value)}
                        onClear={() => handleInput(null)}
                        filterSort={(optionA, optionB) =>
                            (optionA?.label ?? "")
                                .toLowerCase()
                                .localeCompare(
                                    (optionB?.label ?? "").toLowerCase()
                                )
                        }
                        options={zones}
                    />
                </Col>
                <Col>
                    <DatePicker
                        defaultValue={selectDay}
                        size="large"
                        format="DD/MM/YYYY"
                        onChange={(date, dateString) =>
                            handleDateChange(date, dateString)
                        }
                    />
                </Col>
                <Col>
                    <Select
                        allowClear
                        size="large"
                        placeholder="Periodo"
                        options={deliveryPeriodOptions}
                        style={{ width: "100%" }}
                        onChange={(value) => handlePeriodChange(value)}
                    />
                </Col>
            </Row>
        </div>
    );
}

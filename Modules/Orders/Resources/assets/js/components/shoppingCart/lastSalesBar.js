import React, { useEffect, useState } from "react";
import {
    Select,
    Typography,
    DatePicker,
    Col,
    Row,
    Collapse,
    Divider,
    Empty,
} from "antd";

import ProductImgPlaceholder from "../../../img/productPlaceholder.jpg";

const { Panel } = Collapse;
const { Text } = Typography;

export default function LastSalesBar({
    mostBought = [],
    lessBought = [],
    handleFilterDays,
    handleFilterDateRange,
    handleLastSalesClick,
}) {
    const selectDays = [
        {
            value: 7,
            label: "7 dias",
        },
        {
            value: 15,
            label: "15 dias",
        },
        {
            value: 30,
            label: "1 mês",
        },
    ];

    function isEven(num) {
        return num % 2 === 0;
    }

    const evenCards = {
        paddingTop: 4,
        paddingRight: 4,
        paddinBottom: 4,
        paddingLeft: 0,
    };

    const notEvenCards = {
        paddingTop: 4,
        paddingRight: 0,
        paddinBottom: 4,
        paddingLeft: 4,
    };

    return (
        <Collapse ghost>
            <Panel
                bordered={false}
                header={
                    <Text style={{ fontSize: 16, fontWeight: 600 }}>
                        Últimas Encomendas
                    </Text>
                }
                key="ultimasEncomendas"
            >
                <Divider style={{ margin: "0px 0px 24px 0px", margin: 0 }} />
                <Row style={{ paddingBottom: 16 }}>
                    <Col span={8}>
                        <Select
                            defaultValue="7 dias"
                            size="large"
                            onChange={handleFilterDays}
                            options={selectDays}
                            style={{ width: "100%" }}
                        />
                    </Col>
                    <Col span={15} offset={1}>
                        <DatePicker.RangePicker
                            size="large"
                            format={"DD/MM/YYYY"}
                            onChange={handleFilterDateRange}
                            style={{ width: "100%" }}
                        />
                    </Col>
                </Row>
                {mostBought.length != 0 || lessBought.length != 0 ? (
                    <>
                        <Text>Mais Comprados</Text>
                        <Row justify={"space-between"}>
                            <div
                                style={{
                                    marginTop: 8,
                                    display: "flex",
                                    flexWrap: "wrap",
                                    justifyContent: "space-between",
                                }}
                            >
                                {mostBought &&
                                    mostBought.map((value, i) => {
                                        const coverPath = value?.images?.length
                                            ? value?.images[0]?.image_url
                                            : ProductImgPlaceholder;
                                        return (
                                            <Col
                                                key={value.id}
                                                span={12}
                                                style={
                                                    isEven(i)
                                                        ? evenCards
                                                        : notEvenCards
                                                }
                                                onClick={() =>
                                                    handleLastSalesClick(value)
                                                }
                                            >
                                                <div
                                                    key={value.id}
                                                    style={{
                                                        height: 70,
                                                        display: "flex",
                                                        alignItems: "center",
                                                        flexDirection: "row",
                                                        padding: 4,
                                                        border: "1px solid #D9D9D9",
                                                        borderRadius: 8,
                                                        mariginRight: 8,
                                                        cursor: "pointer",
                                                    }}
                                                >
                                                    <img
                                                        alt="Product Image"
                                                        src={coverPath}
                                                        style={{
                                                            height: 60,
                                                            width: 60,
                                                            marginRight: 8,
                                                        }}
                                                    ></img>
                                                    <Text
                                                        style={{ fontSize: 10 }}
                                                    >
                                                        {value?.name}
                                                    </Text>
                                                </div>
                                            </Col>
                                        );
                                    })}
                            </div>
                        </Row>
                        <div style={{ marginTop: 16 }}>
                            <Text>Menos Comprados</Text>
                            <Row justify={"space-between"}>
                                <div
                                    style={{
                                        marginTop: 8,
                                        display: "flex",
                                        flexWrap: "wrap",
                                        justifyContent: "space-between",
                                    }}
                                >
                                    {lessBought &&
                                        lessBought.map((value, i) => {
                                            const coverPath = value?.images
                                                ?.length
                                                ? value?.images[0]?.image_url
                                                : ProductImgPlaceholder;
                                            return (
                                                <Col
                                                    key={value.id}
                                                    span={12}
                                                    style={
                                                        isEven(i)
                                                            ? evenCards
                                                            : notEvenCards
                                                    }
                                                    onClick={() =>
                                                        handleLastSalesClick(
                                                            value
                                                        )
                                                    }
                                                >
                                                    <div
                                                        key={value.id}
                                                        style={{
                                                            height: 70,
                                                            display: "flex",
                                                            alignItems:
                                                                "center",
                                                            flexDirection:
                                                                "row",
                                                            padding: 4,
                                                            border: "1px solid #D9D9D9",
                                                            borderRadius: 8,
                                                            mariginRight: 8,
                                                            cursor: "pointer",
                                                        }}
                                                    >
                                                        <img
                                                            alt="Product Image"
                                                            src={coverPath}
                                                            style={{
                                                                height: 60,
                                                                width: 60,
                                                                marginRight: 8,
                                                            }}
                                                        ></img>
                                                        <Text
                                                            style={{
                                                                fontSize: 10,
                                                            }}
                                                        >
                                                            {value?.name}
                                                        </Text>
                                                    </div>
                                                </Col>
                                            );
                                        })}
                                </div>
                            </Row>
                        </div>
                    </>
                ) : (
                    <Empty
                        description={
                            "Não existe registo de encomendas nas datas indicadas!"
                        }
                    />
                )}
            </Panel>
        </Collapse>
    );
}

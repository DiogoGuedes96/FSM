import React, { useState, useEffect } from "react";
import { useMutation } from "react-query";
import {
    Typography,
    Card,
    Empty,
    Tag,
    Button,
    Popconfirm,
    Divider,
    Tooltip,
} from "antd";
import { DeleteFilled, QuestionCircleOutlined } from "@ant-design/icons";
import { useQuery } from "react-query";
const { Text } = Typography;
import emptyCalls from "../../../img/empty-calls.svg";
import { getCallsInProgress, terminateCall } from "../../libs/apis";
import { gray, red } from "@ant-design/colors";

import ClientBar from "../clientBar";

export default function ActiveCalls() {
    const { data: mutationData, mutate: mutateTerminateCall } = useMutation(
        ["mutationTerminateCall"],
        terminateCall
    );
    const [open, setOpen] = useState(false);
    const [queryData, setQueryData] = useState();
    const [popconfirmVisibilities, setPopconfirmVisibilities] = useState([]);
    const [confirmLoading, setConfirmLoading] = useState(false);
    const [client, setClient] = useState(null);
    const [user, setUser] = useState(
        localStorage.getItem("user")
            ? JSON.parse(localStorage.getItem("user"))
            : null
    );
    const [terminateCallPermissions, setTerminateCallPermissions] =
        useState(false);

    const showDrawer = (value) => {
        setOpen(true);
        setClient(value);
    };

    const onClose = () => {
        setOpen(false);
        setClient(null);
    };

    const { data } = useQuery(["callsInProgress"], getCallsInProgress, {
        refetchInterval: 5000,
    });

    const handleOkTerminateCall = (call) => {
        setConfirmLoading(true);
        mutateTerminateCall({ callId: call?.id });
        removeCallFromCallsArray(call);
        handleClosePopconfirm(call?.id);
        setConfirmLoading(false);
    };

    const removeCallFromCallsArray = (call) => {
        if (call) {
            const newArray = queryData.filter(
                (item) => item.call.id !== call.id
            );
            setQueryData(newArray);
        }
    };

    const handleOpenPopconfirm = (cardId) => {
        setPopconfirmVisibilities((prevVisibilities) =>
            prevVisibilities.map((visibility, index) =>
                index === cardId ? true : visibility
            )
        );
    };

    const handleClosePopconfirm = (cardId) => {
        setPopconfirmVisibilities((prevVisibilities) =>
            prevVisibilities.map((visibility, index) =>
                index === cardId ? false : visibility
            )
        );
    };

    useEffect(() => {
        if (data) {
            const calls = data?.calls.filter((value, i) => {
                if (value.call?.callee_phone || value.call?.caller_phone) {
                    return value;
                }
                return null;
            });

            setQueryData(calls);
        }
    }, [data]);

    useEffect(() => {
        const module = user?.canAccess?.find(
            (module) => module.module === "calls"
        );

        if (module.permissions) {
            const permissions = JSON.parse(module.permissions);

            if (permissions) {
                if (
                    permissions["terminateCalls"] === false ||
                    permissions["terminateCalls"] === undefined
                ) {
                    setTerminateCallPermissions(false);
                } else {
                    setTerminateCallPermissions(true);
                }
            }
        }
    }, [user]);

    const getClientAddressInProgress = (record) => {
        let foundAddress = '';
        let originalAddresses = record?.client?.original_addresses ? record?.client?.original_addresses : null;
        let alternativeAddresses = record?.client?.addresses ? record?.client?.addresses : null;

        let phone = record?.call?.callee_phone
            ? record?.call?.callee_phone
            : record?.call?.caller_phone
                ? record?.call?.caller_phone
                : null;
                
        if (phone) {
            if (alternativeAddresses?.length > 0) {
                const foundAddressObj = alternativeAddresses.find(address => address.phone === phone || address.phone_2 === phone);
                if (foundAddressObj) {
                    foundAddress = foundAddressObj?.address; 
                }
            }
            if (foundAddress == '' && originalAddresses?.length > 0) {
                foundAddress = originalAddresses[0]?.address;
            }
        }
        return foundAddress;
    };

    return (
        <>
            {(queryData?.length == undefined || queryData.length == 0) && (
                <Empty
                    style={{
                        width: "100%",
                        marginTop: "35vh",
                        padding: "10px",
                    }}
                    image={emptyCalls}
                    imageStyle={{ height: 50 }}
                    description={
                        <span
                            style={{
                                textAlign: "center",
                                color: "#00000073",
                                opacity: "1",
                            }}
                        >
                            Não há chamadas ativas
                        </span>
                    }
                />
            )}
            <div style={{ margin: "0 24px" }}>
                {queryData &&
                    queryData.map((value, i) => {
                        return (
                            <Card
                                className="card-custom calls__card"
                                bordered={false}
                                extra={
                                    client?.client?.age_debt > 0 ? (
                                        <Tag
                                            style={{
                                                fontWeight: 400,
                                                fontSize: 14,
                                            }}
                                            color={red[5]}
                                        >
                                            Faturas Pendentes
                                        </Tag>
                                    ) : null
                                }
                                style={{
                                    width: "100%",
                                    marginBottom: "16px",
                                    cursor: "pointer",
                                }}
                                key={i}
                            >
                                <div
                                    style={{
                                        display: "flex",
                                        justifyContent: "space-between",
                                        alignItems: "center",
                                        width: "100%",
                                    }}
                                >
                                    <div
                                        className="calls__data"
                                        style={{
                                            display: "flex",
                                            gap: 16,
                                            width: "100%",
                                        }}
                                        onClick={() => showDrawer(value)}
                                    >
                                        <Text
                                            style={{
                                                fontSize: 16,
                                                color: gray[8],
                                                fontWeight: 600,
                                            }}
                                        >
                                            {value.call?.callee_phone
                                                ? value.call?.callee_phone
                                                : value.call?.caller_phone
                                                ? value.call?.caller_phone
                                                : "N/a"}
                                        </Text>
                                        <Text
                                            style={{
                                                fontSize: 16,
                                                color: gray[6],
                                                fontWeight: 400,
                                            }}
                                        >
                                            {value.client?.name
                                                ? value.client?.name
                                                : "Cliente Não Registado"}
                                        </Text>
                                    </div>
                                    {terminateCallPermissions && (
                                        <div>
                                            <Tooltip
                                                placement="top"
                                                title={"Remover"}
                                            >
                                                <Popconfirm
                                                    title="Tem a certeza que quer remover esta ligação?"
                                                    description={
                                                        <Text
                                                            style={{
                                                                fontSize: 14,
                                                                color: gray[8],
                                                                fontWeight: 500,
                                                            }}
                                                        >
                                                            {"(+351) "}
                                                            {value.call
                                                                ?.caller_phone
                                                                ? value.call
                                                                      ?.caller_phone
                                                                : "N/a"}
                                                        </Text>
                                                    }
                                                    icon={
                                                        <QuestionCircleOutlined
                                                            style={{
                                                                color: "red",
                                                            }}
                                                        />
                                                    }
                                                    okText="Sim"
                                                    placement="topRight"
                                                    cancelText="Não"
                                                    open={
                                                        popconfirmVisibilities[
                                                            value.call
                                                        ]
                                                    }
                                                    onOpenChange={(visible) => {
                                                        if (visible) {
                                                            handleOpenPopconfirm(
                                                                value.call
                                                            );
                                                        } else {
                                                            handleClosePopconfirm(
                                                                value.call
                                                            );
                                                        }
                                                    }}
                                                    okButtonProps={{
                                                        loading: confirmLoading,
                                                    }}
                                                    onConfirm={() =>
                                                        handleOkTerminateCall(
                                                            value.call
                                                        )
                                                    }
                                                    onCancel={() =>
                                                        handleClosePopconfirm(
                                                            value.call
                                                        )
                                                    }
                                                >
                                                    <Button
                                                        style={{
                                                            width: "38px",
                                                            height: "38px",
                                                            marginLeft: "10px",
                                                        }}
                                                        onClick={(visible) => {
                                                            if (visible) {
                                                                handleOpenPopconfirm(
                                                                    value.call
                                                                );
                                                            } else {
                                                                handleClosePopconfirm(
                                                                    value.call
                                                                );
                                                            }
                                                        }}
                                                        shape="circle"
                                                        icon={<DeleteFilled />}
                                                    />
                                                </Popconfirm>
                                            </Tooltip>
                                        </div>
                                    )}
                                </div>
                                {value.client && (
                                    <>
                                        <Divider style={{ margin: 8 }} />
                                        <div
                                            className="calls__data"
                                            style={{
                                                display: "flex",
                                                flexDirection: "row",
                                                gap: 16,
                                                width: "100%",
                                                paddingTop: 16,
                                            }}
                                            onClick={() => showDrawer(value)}
                                        >
                                            <Text
                                                style={{
                                                    fontSize: 16,
                                                    color: gray[2],
                                                    fontWeight: 400,
                                                }}
                                            >
                                                {
                                                    getClientAddressInProgress(
                                                        value
                                                    )
                                                    // value.client?.addresses[0]?.address ? value.client?.addresses[0]?.address : 'N/a'
                                                }
                                            </Text>
                                            {value.client?.tax_number ? (
                                                <Text
                                                    style={{
                                                        fontSize: 16,
                                                        color: gray[2],
                                                        fontWeight: 400,
                                                    }}
                                                >
                                                    Nif:{" "}
                                                    {value.client?.tax_number
                                                        ? value.client
                                                              ?.tax_number
                                                        : "N/a"}
                                                </Text>
                                            ) : null}
                                            {value.client?.tax_number ? (
                                                <Text
                                                    style={{
                                                        fontSize: 16,
                                                        color: gray[2],
                                                        fontWeight: 400,
                                                    }}
                                                >
                                                    ID Primavera:{" "}
                                                    {value.client?.erp_client_id
                                                        ? value.client
                                                              ?.erp_client_id
                                                        : "N/a"}
                                                </Text>
                                            ) : null}
                                        </div>
                                    </>
                                )}
                            </Card>
                        );
                    })}
            </div>
            {client && (
                <ClientBar open={open} onClose={onClose} client={client} />
            )}
        </>
    );
}

import React, { useState, useEffect } from "react";
import { Table, Space, Typography, Tooltip } from "antd";
import { useQuery, useMutation } from "react-query";
const { Column } = Table;
const { Text } = Typography;

import callAll from "../../../img/call-all.svg";
import callMissed from "../../../img/call-missed.svg";
import outgoingCall from "../../../img/outgoing-call.svg";
import { getCallsHangup } from "../../libs/apis";

import ClientBar from "../clientBar";

export default function AllCalls() {
    const {
        data: mutationData,
        isLoading: isLoadingMutation,
        mutate: mutateCallHangup,
    } = useMutation(["mutationCallsHangup"], getCallsHangup);
    const { data, isLoading } = useQuery(["callsHangup"], getCallsHangup);

    const [callData, setcallData] = useState();
    const [pagination, setPagination] = useState();
    const [currentPage, setCurrentPage] = useState();
    const [open, setOpen] = useState(false);
    const [client, setClient] = useState(null);

    const formatDate = (date) => {
        return date ? date.split("T")[0].split("-").reverse().join("/") : "";
    };

    const formatHour = (date) => {
        return date ? date.split("T")[1].split(".")[0] : "";
    };

    const handleCallStatusImage = (call) => {
        if (call.status === "missed") {
            return callMissed;
        }

        if (
            [
                "17",
                "18",
                "19",
                "21",
                "22",
                "32",
                "34",
                "42",
                "480",
                "487",
                "600",
                "603",
            ].includes(call.hangup_status)
        ) {
            return callMissed;
        }

        if (call.callee_phone) {
            return outgoingCall;
        }

        return callAll;
    };

    const handleCallStatus = (call) => {
        if (call.status === "missed") {
            return "Perdida";
        }

        if (
            [
                "17",
                "18",
                "19",
                "21",
                "22",
                "32",
                "34",
                "42",
                "480",
                "487",
                "600",
                "603",
            ].includes(call.hangup_status)
        ) {
            return "Perdida";
        }

        return "Completa";
    };

    const handlepaginations = (page) => {
        setCurrentPage(page);
        mutateCallHangup(page);
    };

    useEffect(() => {
        if (data) {
            setcallData(data);
            setPagination({
                total: data?.pagination?.total,
                current: data?.pagination?.currentPage,
                pageSize: data?.pagination?.perPage,
            });
        }
    }, [data]);

    useEffect(() => {
        if (
            mutationData &&
            mutationData?.pagination?.currentPage == currentPage
        ) {
            setcallData(mutationData);
            setPagination({
                total: mutationData?.pagination?.total,
                current: mutationData?.pagination?.currentPage,
                pageSize: mutationData?.pagination?.perPage,
            });
        }
    }, [mutationData]);

    const showDrawer = (value) => {
        setOpen(true);
        setClient(value);
    };

    const onClose = () => {
        setOpen(false);
        setClient(null);
    };
    return (
        <div>
          <Table
            className="calls__data-table"
            dataSource={callData?.calls}
            pagination={{
                showSizeChanger: false,
                total: pagination?.total,
                current: pagination?.current,
                pageSize: pagination?.pageSize,
                onChange: (page) => handlepaginations(page),
            }}
            loading={isLoading || isLoadingMutation}
            bordered
            rowKey={(record, index) => index}
            style={{ margin: "0 24px" }}
        >
            <Column
                title="Nome"
                dataIndex="name"
                render={(_, record) => (
                    <Space>
                        <img
                            style={{ width: 32 }}
                            src={handleCallStatusImage(record.call)}
                            alt={
                                record.succeedded
                                    ? "chamada efetuada"
                                    : "chamada perdida"
                            }
                        />
                        <Tooltip title="Detalhes">
                            <Text style={{ cursor: 'pointer' }} onClick={() => showDrawer(record)}>
                                {record.client?.name
                                    ? record.client.name
                                    : "Cliente Não Registado"}
                            </Text>
                        </Tooltip>
                    </Space>
                )}
            />
            <Column
                title="ID Primavera"
                dataIndex="erp_client_id"
                render={(_, render) => (
                    <Space>
                        <Text>
                            {render.client?.erp_client_id
                                ? render.client?.erp_client_id
                                : "N/a"}
                        </Text>
                    </Space>
                )}
            />
            <Column
                title="Data"
                dataIndex="date"
                render={(_, record) => (
                    <Space direction="vertical" size={0}>
                        <Text style={{ fontWeight: 600 }}>
                            {formatDate(record.call.created_at)}
                        </Text>
                        <Text type="secondary">
                            {formatHour(record.call?.created_at)}
                        </Text>
                    </Space>
                )}
            />
            <Column
                title="Morada"
                dataIndex="address"
                render={(_, record) => {
                    let foundAddress = 'N/a';
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

                    return (
                        <Space direction="vertical" size={0}>
                            <Text>
                                {foundAddress}
                            </Text>
                        </Space>
                    )
                }}
            />
            <Column
                title="NIF"
                dataIndex="nif"
                render={(_, record) => (
                    <Space direction="vertical" size={0}>
                        <Text>
                            {record.client?.tax_number
                                ? record.client.tax_number
                                : "N/a"}
                        </Text>
                    </Space>
                )}
            />
            <Column
                title="Contacto Telefónico"
                dataIndex="caller_phone"
                render={(_, record) => (
                    <Space>
                        <Text>
                            {record.call?.callee_phone
                                ? record.call.callee_phone
                                : record.call?.caller_phone
                                ? record.call.caller_phone
                                : "N/a"}
                        </Text>
                    </Space>
                )}
            />{" "}
            <Column
                title="Status"
                dataIndex="hangup_status"
                render={(_, record) => (
                    <Text>{handleCallStatus(record.call)}</Text>
                )}
            />
            </Table>
            {client && (
                    <ClientBar open={open} onClose={onClose} client={client} />
            )}  
        </div>
    );
}

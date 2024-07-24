import React, { useState, useEffect } from "react";
import { Table, Space, Typography, Tooltip } from "antd";
const { Column } = Table;
const { Text } = Typography;

import callMissed from "../../../img/call-missed.svg";
import { getCallsMissed, setMissedCallsViewed, getCountMissedCalls } from "../../libs/apis";
import { useQuery, useMutation } from "react-query";
import ClientBar from "../clientBar";
export default function MissedCalls({ setCoutMissedCalls }) {
    const { data, isLoading } = useQuery(["callsMissed"], getCallsMissed);
    const {
        data: mutationData,
        isLoading: isLoadingMutation,
        mutate: mutateCallMissed,
    } = useMutation(["mutationCallsMissed"], getCallsMissed);

    const { mutate: mutateSetMissedCallsViewed } = useMutation(["mutationCallsMissed"], setMissedCallsViewed, {
        onSuccess: (data) => {
            mutateGetCountMissedCalls();
          },
    });

    const { mutate: mutateGetCountMissedCalls } = useMutation(["mutation-missed-calls-count-total"], getCountMissedCalls, {
        onSuccess: (data) => {
          setCoutMissedCalls(data?.calls ?? 0);
        },
      });
    

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

    const handlepaginations = (page) => {
        setCurrentPage(page);
        mutateCallMissed(page);
    };

    useEffect(() => {
        if (data) {
            handleViewdCalls(data?.calls);

            setcallData(data);
            setPagination({
                total: data?.pagination?.total,
                current: data?.pagination?.currentPage,
                pageSize: data.pagination?.perPage,
            });
        }
    }, [data]);

    const showDrawer = (value) => {
        setOpen(true);
        setClient(value);
    };

    const onClose = () => {
        setOpen(false);
        setClient(null);
    };

    useEffect(() => {
        if (
            mutationData &&
            mutationData?.pagination?.currentPage == currentPage
        ) {
            handleViewdCalls(mutationData?.calls);
            setcallData(mutationData);
            setPagination({
                total: mutationData?.pagination?.total,
                current: mutationData?.pagination?.currentPage,
                pageSize: mutationData?.pagination?.perPage,
            });
        }
    }, [mutationData]);


    const handleViewdCalls = (calls) => {
        if (calls?.length > 0) {
            const arrayOfCallIds = calls.map(callObject => callObject?.call?.id);
    
            if(arrayOfCallIds?.length > 0) {
                mutateSetMissedCallsViewed(arrayOfCallIds);
            }
        }
    }

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
                rowKey={(record) => record.call.id}
                style={{ margin: "0 24px" }}
            >
                <Column
                    title="Nome"
                    dataIndex="name"
                    render={(_, record) => (
                        <Space >
                            <img
                                src={callMissed}
                                style={{ width: 32 }}
                                alt="chamada ativa"
                            />
                            <Tooltip title="Detalhes">
                                <Text style={{ cursor: 'pointer' }} onClick={() => showDrawer(record)}>
                                    {record.client?.name
                                        ? record.client?.name
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
                                {formatHour(record.call.created_at)}
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
                    key="nif"
                    render={(_, record) => (
                        <Space direction="vertical" size={0}>
                            <Text>
                                {record.client?.tax_number
                                    ? record.client?.tax_number
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
                />
            </Table>
            {client && (
                <ClientBar open={open} onClose={onClose} client={client} />
            )}
        </div>
        
    );
}

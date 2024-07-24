import React, { useState, useEffect } from "react";
import { useMutation } from "react-query";
import { Typography, Button, Input, Divider, Empty } from "antd";

import { getFilteredClients } from "../../libs/apis";

const { Text } = Typography;
const { Search } = Input;

export default function SearchClient({ handleClientStatus, searchBarStatus }) {
    const {
        data: filteredClients,
        isLoading: isLoadingFilteredClients,
        isSuccess: isSuccessFilteredClients,
        mutate: mutateFilteredClients,
    } = useMutation(["mutationFilteredClients"], getFilteredClients);

    const [clientList, setClientList] = useState(null);

    const onSearch = (value) => {
        if (value) {
            mutateFilteredClients({ searchInput: value });
        }
    };

    useEffect(() => {
        if (filteredClients && filteredClients.length > 0) {
            setClientList(filteredClients);
        }
    }, [filteredClients]);

    return (
        <div style={{ padding: "0px 24px" }}>
            {searchBarStatus && (
                <>
                    <Search
                        className="search-client"
                        placeholder="Procurar Cliente"
                        allowClear
                        onSearch={(value) => onSearch(value)}
                        size="large"
                        style={{
                            width: "100%",
                            marginTop: 15,
                        }}
                    />
                    <Button
                        type="primary"
                        size="Large"
                        style={{
                            width: '100%',
                            height: 40,
                            marginTop: '10px',
                            color: "#000000E0",
                        }}
                        onClick={() => handleClientStatus(null, false)}
                    >
                        Cancelar
                    </Button>

                    {clientList &&
                        clientList.map((value, i) => {
                            return (
                                <>
                                    <div
                                        key={value?.name}
                                        style={{
                                            marginTop: 32,
                                            width: "100%",
                                            color: "#FAFAFA",
                                            display: "flex",
                                            justifyContent: "space-between",
                                            alignItems: "flex-start",
                                        }}
                                    >
                                        <div style={{ marginLeft: 10 }}>
                                            <Text
                                                style={{
                                                    fontSize: 20,
                                                    fontWeight: 600,
                                                }}
                                            >
                                                {value?.name}
                                            </Text>
                                            <p style={{ color: "#8C8C8C" }}>
                                                {"(+351)"} {value?.phone_1}
                                            </p>
                                            <p style={{ color: "#8C8C8C" }}>
                                                ID Primavera:{" "}
                                                {value?.erp_client_id}
                                            </p>
                                        </div>
                                        <Button
                                            type="primary"
                                            size="Large"
                                            onClick={() =>
                                                handleClientStatus(value, false)
                                            }
                                            style={{
                                                color: "#000000E0",
                                                height: 40,
                                            }}
                                        >
                                            Associar
                                        </Button>
                                    </div>
                                    <Divider />
                                </>
                            );
                        })}
                    {isSuccessFilteredClients && !clientList && (
                        <Empty
                            description={
                                "Não foram encontrados resultados para a pesquisa!"
                            }
                        />
                    )}
                </>
            )}
        </div>
    );
}

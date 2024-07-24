import React, { useState, useEffect } from "react";
import { useMutation } from "react-query";
import { Card, Typography, Space, message } from "antd";
import moment from "moment";

import SearchClient from "../searchClient";
import ClientBar from "../clientBar";
import LastSalesBar from "./lastSalesBar";
import ClientNotes from "../clientNotes";
import ProductModal from "../productModal";
import { getMostBoughtProducts, getLessBoughtProducts } from "../../libs/apis";

import clientIcon from "../../../img/client-icon.svg";
import editClientIcon from "../../../img/edit.svg";
import addClientIcon from "../../../img/add-client-icon.svg";
import DirectSaleProductModal from "../directSale/directSaleProductModal";

const { Text } = Typography;

export default function cartBar({
    order,
    setOrderNotes,
    onAssociateClient,
    addCartProduct,
    clientOrder,
    isDirectSale = false
}) {
    const clientFromCall = localStorage.getItem("clientFromCall");
    const callPhoneNumber = localStorage.getItem("callerPhone");

    const [clientData, setClientData] = useState(null);
    const [callerPhone, setCallerPhone] = useState(null);
    const [clientSearchBarStatus, setClientSearchBarStatus] = useState(false);
    const [openClientBar, setOpenClientBar] = useState(false);
    const [mostBoughtProducts, setMostBoughtProducts] = useState([]);
    const [lessBoughtProducts, setLessBoughtProducts] = useState([]);
    const [numProducts, setNumProducts] = useState(4);
    const [isModalVisible, setIsModalVisible] = useState(false);
    const [modalProduct, setModalProduct] = useState(false);

    const {
        data: mostBoughtProductsData,
        isLoading: isLoadingMutationMost,
        mutate: mutateMostBoughtProductsData,
    } = useMutation(["mutationMostBoughtProductsData"], getMostBoughtProducts);
    const {
        data: lessBoughtProductsData,
        isLoading: isLoadingMutationLess,
        mutate: mutateLessBoughtProductsData,
    } = useMutation(["mutationLessBoughtProductsData"], getLessBoughtProducts);

    const showDrawer = () => setOpenClientBar(true);
    const onCloseClientBar = () => setOpenClientBar(false);

    useEffect(() => {
        if (clientOrder) {
            handleClientStatus(clientOrder);
        }
    }, [clientOrder]);

    const handleClientStatus = (client, searchBarStatus = false) => {
        if (client) {
            setClientData(client);
            message.success("Cliente associado corretamente.");
        }
        setClientSearchBarStatus(searchBarStatus);
    };

    const handleFilterDays = (value) => {
        const startDate = moment().subtract(value, "days").format("YYYY-MM-DD");
        const endDate = moment().format("YYYY-MM-DD");

        mutateMostBoughtProductsData({
            clientId: clientData?.id,
            startDate: startDate ?? null,
            endDate: endDate ?? null,
            numProducts: numProducts ?? null,
        });
        mutateLessBoughtProductsData({
            clientId: clientData?.id,
            startDate: startDate ?? null,
            endDate: endDate ?? null,
            numProducts: numProducts ?? null,
        });
    };

    const handleFilterDateRange = (dates) => {
        const startDate = moment(start.$d).format("YYYY-MM-DD");
        const endDate = moment(end.$d).format("YYYY-MM-DD");

        mutateMostBoughtProductsData({
            clientId: clientData?.id,
            startDate: startDate ?? null,
            endDate: endDate ?? null,
            numProducts: numProducts ?? null,
        });
        mutateLessBoughtProductsData({
            clientId: clientData?.id,
            startDate: startDate ?? null,
            endDate: endDate ?? null,
            numProducts: numProducts ?? null,
        });
    };

    const handleModalCancel = () => {
        setIsModalVisible(false);
    };

    const handleLastSalesClick = (product) => {
        setModalProduct(product);
        setIsModalVisible(true);
    };

    useEffect(() => {
        if (mostBoughtProductsData) {
            setMostBoughtProducts(mostBoughtProductsData?.products ?? []);
        }
    }, [mostBoughtProductsData]);

    useEffect(() => {
        if (lessBoughtProductsData) {
            setLessBoughtProducts(lessBoughtProductsData?.products ?? []);
        }
    }, [lessBoughtProductsData]);

    useEffect(() => {
        if (clientFromCall) {
            try {
                setClientData(JSON.parse(clientFromCall));
                localStorage.removeItem("clientFromCall");
                setClientSearchBarStatus(false);
            } catch (error) {
                console.error("Invalid client data:", error);
            }
        }
    }, [clientFromCall]);

    useEffect(() => {
        if (clientData) {
            const startDate = moment().subtract(7, "days").format("YYYY-MM-DD");
            const endDate = moment().format("YYYY-MM-DD");
            onAssociateClient(callerPhone ?? null, clientData);

            mutateMostBoughtProductsData({
                clientId: clientData?.id,
                startDate: startDate ?? null,
                endDate: endDate ?? null,
                numProducts: numProducts ?? null,
            });
            mutateLessBoughtProductsData({
                clientId: clientData?.id,
                startDate: startDate ?? null,
                endDate: endDate ?? null,
                numProducts: numProducts ?? null,
            });
        }
    }, [clientData]);

    useEffect(() => {
        if (callPhoneNumber) {
            try {
                setCallerPhone(callPhoneNumber);
                onAssociateClient(callPhoneNumber, clientData ?? null);
                localStorage.removeItem("callerPhone");
            } catch (error) {
                console.error("Invalid call phone number!", error);
            }
        }
    }, [callPhoneNumber]);

    return (
        <>
            {clientSearchBarStatus ? (
                <SearchClient
                    handleClientStatus={handleClientStatus}
                    searchBarStatus={clientSearchBarStatus}
                />
            ) : (
                <>
                    {clientData ? (
                        <>
                            <Card
                                bordered={false}
                                style={{ margin: 16, background: "#FAFAFA" }}
                            >
                                <div
                                    style={{
                                        display: "flex",
                                        alignItems: "center",
                                        justifyContent: "space-between",
                                    }}
                                >
                                    <div
                                        style={{
                                            display: "flex",
                                            alignItems: "center",
                                        }}
                                    >
                                        <img
                                            alt="Client Image"
                                            src={clientIcon}
                                            style={{
                                                marginRight: 16,
                                                cursor: "pointer",
                                            }}
                                            onClick={() => showDrawer()}
                                        ></img>
                                        <Text
                                            style={{
                                                fontSize: 20,
                                                fontWeight: 600,
                                                cursor: "pointer",
                                                display: "flex",
                                                flexDirection: "column",
                                            }}
                                            onClick={() => showDrawer()}
                                        >
                                            <span>{clientData?.name}</span>
                                            {clientData?.erp_client_id && (
                                                <span
                                                    style={{
                                                        fontWeight: 400,
                                                        fontSize: 16,
                                                        color: "#000000",
                                                        opacity: 0.4,
                                                    }}
                                                >
                                                    ID Primavera:{" "}
                                                    {clientData?.erp_client_id}
                                                </span>
                                            )}
                                        </Text>
                                    </div>
                                    <img
                                        alt="Edit Client Image"
                                        src={editClientIcon}
                                        style={{ cursor: "pointer" }}
                                        onClick={() =>
                                            setClientSearchBarStatus(true)
                                        }
                                    ></img>
                                </div>
                            </Card>
                            <LastSalesBar
                                handleFilterDays={handleFilterDays}
                                handleFilterDateRange={handleFilterDateRange}
                                handleLastSalesClick={handleLastSalesClick}
                                mostBought={mostBoughtProducts}
                                lessBought={lessBoughtProducts}
                            />
                            <ClientNotes
                                notes={order ? order?.description : clientData?.notes}
                                handleInputOrderNotes={setOrderNotes}
                            />
                            <ClientBar
                                open={openClientBar}
                                onClose={onCloseClientBar}
                                client={clientData}
                            />
                        </>
                    ) : (
                        <Card
                            bordered={false}
                            style={{
                                margin: "0px 24px",
                                background: "#FAFAFA",
                            }}
                        >
                            <Space
                                align="start"
                                direction="horizontal"
                                size="large"
                                style={{ alignItems: "center" }}
                            >
                                <div>
                                    <img
                                        alt="Add Client Image"
                                        src={addClientIcon}
                                        style={{ cursor: "pointer" }}
                                        onClick={() =>
                                            setClientSearchBarStatus(true)
                                        }
                                    ></img>
                                </div>
                                <div
                                    style={{
                                        display: "flex",
                                        flexDirection: "column",
                                    }}
                                >
                                    {callerPhone && (
                                        <Text style={{ color: "#8C8C8C" }}>
                                            (+351) {callerPhone}
                                        </Text>
                                    )}
                                    <Text className="purchase__add-button">
                                        Adicionar Cliente
                                    </Text>
                                </div>
                            </Space>
                        </Card>
                    )}
                </>
            )}
            {
                isDirectSale ?
                    <DirectSaleProductModal
                        open={isModalVisible}
                        onCancel={handleModalCancel}
                        product={modalProduct}
                        addCart={(props) => {
                            handleModalCancel();
                            addCartProduct(props);
                        }}
                    />
                    :
                    <ProductModal
                        open={isModalVisible}
                        onCancel={handleModalCancel}
                        product={modalProduct}
                        addCart={(props) => {
                            handleModalCancel();
                            addCartProduct(props);
                        }}
                    />
            }
        </>
    );
}

import React, { useEffect, useState } from "react";
import { useMutation } from "react-query";
import {
    List,
    Typography,
    Badge,
    Button,
    Popconfirm,
    Tooltip,
    message,
} from "antd";
import {
    ArrowRightOutlined,
    CloseOutlined,
    QuestionCircleOutlined,
} from "@ant-design/icons";
import "../../../sass/tracking.scss";
import { formatDatePt, getColorStatus } from "../../libs/utils";
import { unBlockOrder } from "../../libs/apis";
const { Title, Text } = Typography;

export default function OrderItem({
    item,
    active,
    loadOrder,
    setOrderActive,
    setOrderSelected,
    blocked = false,
    isFromUser = false,
}) {
    const [deliveringPeriod, setDeliveringPeriod] = useState("");
    const [unblockOrderLoading, setUnblockOrderLoading] = useState(false);
    const [popconfirmVisibility, setPopconfirmVisibility] = useState(false);
    const [unblockOrderPermissions, setUnblockOrderPermissions] =
        useState(true);
    const [user, setUser] = useState(
        localStorage.getItem("user")
            ? JSON.parse(localStorage.getItem("user"))
            : null
    );
    const colorStatus = getColorStatus(item.status, item?.isDirectSale ?? null);
    const {
        data: dataUnblockOrder,
        mutate: mutateUnlbockOrder,
        isSuccess: isSuccessUnblockOrder,
        isError: isErrorUnblockOrder,
    } = useMutation(["mutationUnblockOrder"], unBlockOrder);

    const popConfirmMessages = {
        unblockOrderConfirm:
            "Tem a certeza que quer desbloquear esta encomenda?",
        stoleOrderConfirm:
            "Esta encomenda pertence a outro utilizador, pretende prosseguir?",
    };

    const popUpMessages = {
        errorUnblockingOrder: "Erro ao tentar desbloquear uma encomenda!",
        orderOnblocked: "Encomenda desbloqueada!",
    };

    useEffect(() => {
        if (item?.delivery_period) {
            if (item.delivery_period == "morning") {
                setDeliveringPeriod(" - Manhã");
            }
            if (item.delivery_period == "evening") {
                setDeliveringPeriod(" - Tarde");
            }
        }
    }, [item]);

    useEffect(() => {
        setPopconfirmVisibility(false);
        setUnblockOrderLoading(false);
    }, [blocked]);

    // useEffect(() => {
    //     if (user) {
    //         const module = user?.canAccess?.find(
    //             (module) => module.module === "orders-tracking"
    //         );

    //         if (module.permissions) {
    //             const permissions = JSON.parse(module.permissions);

    //             if (permissions) {
    //                 if (
    //                     permissions["unblockOrder"] === false ||
    //                     permissions["unblockOrder"] === undefined
    //                 ) {
    //                     setUnblockOrderPermissions(false);
    //                 } else {
    //                     setUnblockOrderPermissions(true);
    //                 }
    //             }
    //         }
    //     }
    // }, [user]);

    useEffect(() => {
        if (isErrorUnblockOrder) {
            setPopconfirmVisibility(false);
            setUnblockOrderLoading(false);
            message.error(popUpMessages.errorUnblockingOrder);
        }
    }, [isErrorUnblockOrder]);

    useEffect(() => {
        if (isSuccessUnblockOrder) {
            setUnblockOrderLoading(false);
            setPopconfirmVisibility(false);
            setOrderActive(null);
            setOrderSelected(null);
            message.success(popUpMessages.orderOnblocked);
        }
    }, [isSuccessUnblockOrder]);

    const unblockOrderById = () => {
        console.log("unblockOrderById", blocked && unblockOrderPermissions);
        if (blocked && unblockOrderPermissions) {
            loadOrder(item.id);
            setPopconfirmVisibility(false);
            setUnblockOrderLoading(true);
        } else {
            setUnblockOrderLoading(true);
            mutateUnlbockOrder();
        }
    };

    const handleCancelUnblockORder = () => {
        setPopconfirmVisibility(false);
        setUnblockOrderLoading(false);
    };

    const handleCardClick = () => {
        if (!blocked) {
            loadOrder(item.id);
        } else {
            if (unblockOrderPermissions && !popconfirmVisibility) {
                setPopconfirmVisibility(true);
            }
        }
    };

    return (
        <List.Item
            style={{
                borderBlockEnd: "none",
                ...(blocked && !unblockOrderPermissions
                    ? { cursor: "not-allowed" }
                    : { cursor: "pointer" }),
            }}
            key={item.id}
            onClick={() => handleCardClick()}
        >
            <div
                style={{
                    display: "flex",
                    flexDirection: "column",
                    width: "100%",
                    margin: "0 16px",
                    background: blocked ? "#f9f9f9" : "#fff",
                    padding: 16,
                    ...(active
                        ? { borderLeft: `4px solid ${colorStatus}` }
                        : blocked
                        ? { borderLeft: "4px solid #D9D9D9" }
                        : {}),
                    ...(active && { boxShadow: "0px 3px 16px #00000029" }),
                }}
            >
                <div
                    style={{ display: "flex", justifyContent: "space-between" }}
                >
                    <div style={{ display: "flex", alignItems: "center" }}>
                        <Title
                            level={2}
                            style={{
                                margin: 0,
                                ...(active
                                    ? { color: colorStatus }
                                    : { color: "#D9D9D9" }),
                            }}
                        >
                            {item.id}
                        </Title>
                        {isFromUser && (
                            <Tooltip
                                placement="top"
                                title={"Desbloquear Encomenda"}
                            >
                                <Button
                                    type="primary"
                                    loading={unblockOrderLoading}
                                    size="small"
                                    icon={<CloseOutlined />}
                                    style={{ marginLeft: 16 }}
                                    onClick={() =>
                                        setPopconfirmVisibility(true)
                                    }
                                />
                            </Tooltip>
                        )}
                        <Popconfirm
                            title={
                                blocked
                                    ? popConfirmMessages.stoleOrderConfirm
                                    : popConfirmMessages.unblockOrderConfirm
                            }
                            icon={
                                <QuestionCircleOutlined
                                    style={{ color: "red" }}
                                />
                            }
                            okText="Sim"
                            placement="right"
                            cancelText="Não"
                            open={popconfirmVisibility}
                            okButtonProps={{
                                loading: unblockOrderLoading,
                            }}
                            onConfirm={() => unblockOrderById()}
                            onCancel={() => handleCancelUnblockORder()}
                        />
                    </div>

                    <div style={{ display: "flex" }}>
                        {blocked ? (
                            <Badge.Ribbon
                                text="Em utilização"
                                color="gray"
                                style={{ marginRight: 11 }}
                            ></Badge.Ribbon>
                        ) : item.priority ? (
                            <Badge.Ribbon
                                text="Prioridade"
                                style={{ marginRight: 11 }}
                            ></Badge.Ribbon>
                        ) :  item.isDirectSale ? (
                            <Badge.Ribbon
                                text="Venda Direta"
                                color="#EB2F96"
                                style={{ marginRight: 11 }}
                            ></Badge.Ribbon>
                        ) : (
                            <></>
                        )}

                        <ArrowRightOutlined
                            style={{
                                fontSize: 24,
                                marginLeft: 10,
                                ...(active
                                    ? { color: colorStatus }
                                    : { display: "none" }),
                            }}
                        />
                    </div>
                </div>
                <div>
                    <List size="small">
                        <List.Item>
                            <Text
                                style={{
                                    color: blocked ? "#939393" : "#333333",
                                }}
                            >
                                <Text
                                    style={{
                                        color: blocked ? "#939393" : "#333333",
                                    }}
                                    strong
                                >
                                    Nome:{" "}
                                </Text>
                                {item?.client_name ?? "Cliente não registado"}
                            </Text>
                        </List.Item>
                        <List.Item className="active_row">
                            <Text
                                style={{
                                    color: blocked ? "#939393" : "#333333",
                                }}
                            >
                                <Text
                                    style={{
                                        color: blocked ? "#939393" : "#333333",
                                    }}
                                    strong
                                >
                                    Contacto:{" "}
                                </Text>
                                {item?.caller_phone ?? "N/a"}
                            </Text>
                        </List.Item>
                        <List.Item>
                            <Text
                                style={{
                                    color: blocked ? "#939393" : "#333333",
                                }}
                            >
                                <Text
                                    style={{
                                        color: blocked ? "#939393" : "#333333",
                                    }}
                                    strong
                                >
                                    Zona:{" "}
                                </Text>
                                {item?.zone ?? "N/a"}
                            </Text>
                        </List.Item>
                        <List.Item className="active_row last_row">
                            <Text
                                style={{
                                    color: blocked ? "#939393" : "#333333",
                                }}
                            >
                                <Text
                                    style={{
                                        color: blocked ? "#939393" : "#333333",
                                    }}
                                    strong
                                >
                                    Data de Entrega:{" "}
                                </Text>
                                {item?.delivery_date
                                    ? formatDatePt(item?.delivery_date)
                                    : "N/a"}
                                {deliveringPeriod ? deliveringPeriod : " "}
                            </Text>
                        </List.Item>
                    </List>
                </div>
            </div>
        </List.Item>
    );
}

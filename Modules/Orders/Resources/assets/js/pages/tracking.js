import React, { useEffect, useState, useMemo } from "react";
import { Tabs, Input, Badge, Typography } from "antd";
import DraftOrders from "../components/tracking/tabs/DraftOrders";
import PendentOrders from "../components/tracking/tabs/PendentOrders";
import PartiallyShipped from "../components/tracking/tabs/PartiallyShipped";
import PreparingOrders from "../components/tracking/tabs/PreparingOrders";
import DeliveringOrders from "../components/tracking/tabs/DeliveringOrders";
import {
    STATUS_CANCELED,
    STATUS_COMPLETED,
    STATUS_IN_DELIVERY,
    STATUS_PARTIALLY_SHIPPED,
    STATUS_PENDING,
} from "../libs/utils";
import ListOrdersByStatus from "../components/tracking/tabs/ListOrdersByStatus";
import DrawerOrder from "../components/tracking/DrawerOrder";
import ListSearchOrders from "../components/tracking/tabs/ListSearchOrders";
import {
    getListOrders,
    searchOrdersByInput,
    updateStatusOrder,
} from "../libs/apis";
import { useMutation, useQuery } from "react-query";
import { debounce } from "lodash";
import "./base.scss";

const { Text } = Typography;

export default function Tracking() {
    const OperationsSlot = {
        right: (
            <Input
                placeholder="Busca por encomenda, nome do cliente, contacto ou ID Primavera"
                allowClear
                onChange={(e) => debouncedSearch(e.target.value)}
                size="large"
                style={{ marginBottom: 8 }}
            />
        ),
    };

    const [activeTab, setActiveTab] = useState("1");
    const [order, setOrder] = useState(null);

    const openOrder = (order) => {
        setOrder(order);
    };

    const [user, setUser] = useState(
        localStorage.getItem("user")
            ? JSON.parse(localStorage.getItem("user"))
            : null
    );

    const [searchInput, setSearchInput] = useState(null);
    const [page, setPage] = useState(1);
    const [total, setTotal] = useState(0);
    const [lastPage, setLastPage] = useState(null);
    const [loading, setLoading] = useState(false);
    const [orders, setOrders] = useState([]);
    const [partiallyShippedTotal, setPartiallyShippedTotal] = useState(0);
    const [pendingTotal, setPendingTotal] = useState(0);
    const [position, setPosition] = useState(["right"]);

    const { data: mutationData, mutate: mutateSearchOrdersByInput } =
        useMutation(["mutationSearchOrdersByInput"], searchOrdersByInput);

    useQuery(
        ["last-sales-and-products-partially-total"],
        () => getListOrders(1, STATUS_PARTIALLY_SHIPPED),
        {
            refetchOnWindowFocus: false,
            refetchInterval: 60000,
            onSuccess: (data) => {
                setPartiallyShippedTotal(data?.Orders?.total ?? 0);
            },
        }
    );

    useQuery(
        ['last-sales-and-products-pending-total'],
        () => getListOrders(1, STATUS_PENDING),
        {
            refetchOnWindowFocus: false,
            refetchInterval: 60000,
            onSuccess: (data) => {
                setPendingTotal(data?.Orders?.total ?? 0)
            },
        }
    );

    const isActiveTab = (tab, shipperTab) => {
        if (user.profile === "shipper" && activeTab === shipperTab) {
            return true;
        }

        return activeTab === tab;
    };

    const items = [
        {
            name: "draft",
            label: `Rascunho`,
            children: activeTab === "1" ? <DraftOrders /> : null,
        },
        {
            name: "pending",
            label: pendingTotal > 0 ?
            <Text>Pendentes
                <Badge style={{marginLeft: 8}} color="red" count={pendingTotal} />
            </Text> :
            'Pendentes',
            children: isActiveTab("2", "1") ? <PreparingOrders /> : null,
        },
        {
            name: "partially-shipped",
            label:
                partiallyShippedTotal > 0 ? (
                    <Text>
                        Parcialmente expedidas
                        <Badge
                            style={{ marginLeft: 8 }}
                            color="red"
                            count={partiallyShippedTotal}
                        />
                    </Text>
                ) : (
                    `Parcialmente expedidas`
                ),
            children:
                activeTab === "3" ? (
                    <PartiallyShipped
                        setStateTotal={setPartiallyShippedTotal}
                    />
                ) : null,
        },
        {
            name: "preparation",
            label: `Preparação`,
            children: isActiveTab("4", "2") ? <PendentOrders /> : null,
        },
        {
            name: "delivery",
            label: `Distribuição`,
            children: isActiveTab("5", "3") ? (
                <ListOrdersByStatus
                    status={STATUS_IN_DELIVERY}
                    openOrder={openOrder}
                />
            ) : null,
        },
        {
            name: "completed",
            label: `Concluídas`,
            children:
                activeTab === "6" ? (
                    <ListOrdersByStatus
                        status={STATUS_COMPLETED}
                        openOrder={openOrder}
                    />
                ) : null,
        },
        {
            name: "canceled",
            label: `Canceladas`,
            children:
                activeTab === "7" ? (
                    <ListOrdersByStatus
                        status={STATUS_CANCELED}
                        openOrder={openOrder}
                    />
                ) : null,
        },
        {
            name: "search",
            label: `Pesquisa`,
            children: (
                <ListSearchOrders
                    orders={orders}
                    total={total}
                    loading={loading}
                    input={searchInput}
                    loadMoreData={loadMoreData}
                    openOrder={openOrder}
                />
            ),
        },
    ];

    const onChange = (key) => {
        setActiveTab(key);
        window.history.pushState({}, "", `?tab=${key}`);
    };

    useEffect(() => {
        if (mutationData) {
            const urlParams = new URLSearchParams(window.location.search);
            const tab = urlParams.get("tab");
            if (tab) {
                const tabActive = showTabs.find((item) => item.key === tab);
                const lastTab = showTabs.length.toString();

                if (!tabActive.key == !lastTab) {
                    setActiveTab(lastTab);
                    window.history.pushState({}, "", `?tab=${lastTab}`);
                }
            }

            const data = mutationData?.orders;
            const ordersList = mutationData?.orders?.data;

            if (page === 1) {
                setOrders(ordersList);
            } else {
                setOrders([...orders, ...listOrders]);
            }

            setLastPage(data?.last_page);
            setPage(data?.current_page);
            setLoading(false);
            setTotal(data?.total);
        }
    }, [mutationData]);

    const handleSearch = (input) => {
        if (input) {
            setSearchInput(input);
            setLoading(true);
            mutateSearchOrdersByInput({ page: page, input: input });
        } else {
            setOrders([]);
            setTotal(0);
            setLastPage(null);
            setPage(1);
            setLoading(false);
            setSearchInput(null);
        }
    };

    const debouncedSearch = debounce(handleSearch, 500);

    const slot = useMemo(() => {
        const module = user?.canAccess?.find(
            (module) => module.module === "orders-tracking"
        );

        if (module.permissions) {
            const permissions = JSON.parse(module.permissions);

            if (
                permissions &&
                (permissions["search"] === false ||
                    permissions["search"] === undefined)
            )
                return null;
        }

        if (position.length === 0) return null;

        return position.reduce((acc, direction) => {
            return {
                ...acc,
                [direction]: OperationsSlot[direction],
            };
        }, {});
    }, [position]);

    const loadMoreData = () => {
        if (loading || page === lastPage) {
            return;
        }

        setLoading(true);

        if (orders.length > 0) {
            setPage((prevPage) => {
                return prevPage + 1;
            });
        }

        mutateSearchOrdersByInput({ page: page, input: input });
    };

    useEffect(() => {
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get("tab");

        if (tab) {
            const tabActive = showTabs.find((item) => item.key === tab);
            if (tabActive) {
                setActiveTab(tabActive.key);
                window.history.pushState({}, "", `?tab=${tabActive.key}`);
            }
        }
    }, []);

    const showSomeTabs = () => {
        const module = user?.canAccess?.find(
            (module) => module.module === "orders-tracking"
        );

        if (module.permissions) {
            const permissions = JSON.parse(module.permissions);

            let tabs = [];

            items.forEach((item) => {
                permissions[item.name] === true ? tabs.push(item) : null;
            });

            return tabs;
        }

        return items;
    };

    const showTabs = showSomeTabs().map((item, key) => {
        return {
            key: (key + 1).toString(),
            ...item,
        };
    });

    return (
        <>
            <Tabs
                className="orders__tabs"
                defaultActiveKey="1"
                destroyInactiveTabPane={true}
                tabBarExtraContent={slot}
                activeKey={activeTab}
                items={showTabs}
                onChange={onChange}
            />
            {order && (
                <DrawerOrder order={order} close={() => setOrder(null)} />
            )}
        </>
    );
}

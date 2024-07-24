import React, { useEffect, useState } from "react";
import { Layout as LayoutAnt, Menu } from "antd";
import {
    HomeOutlined,
    ShopOutlined,
    PhoneOutlined,
    ShoppingCartOutlined,
    FieldTimeOutlined,
    CalendarOutlined,
    ShoppingOutlined,
} from "@ant-design/icons";
import Logotype from "../../../img/logotype.png";
import DrawerSchedule from "./schedule/DrawerSchedule";
const { Sider } = LayoutAnt;

const routes = [
    {
        key: "homepage",
        url: "/",
        icon: <HomeOutlined />,
        label: "Homepage",
    },
    {
        key: "calls",
        url: "/calls",
        icon: <PhoneOutlined />,
        label: "Central Telefónica",
    },
    {
        key: "order",
        url: "/orders/newOrder",
        icon: <ShoppingCartOutlined />,
        label: "Fazer Encomenda",
    },
    {
        key: "direct-sale",
        url: "/orders/directSale",
        icon: <ShoppingOutlined />,
        label: "Venda Direta",
    },
    {
        key: "products",
        url: "/products",
        icon: <ShopOutlined />,
        label: "Gerir Produtos",
    },
    {
        key: "orders-tracking",
        url: "/orders/tracking",
        icon: <FieldTimeOutlined />,
        label: "Acompanhar Encomendas",
    },
    {
        key: "scheduling",
        component: "DrawerSchedule",
        icon: <CalendarOutlined />,
        label: "Agenda",
    },
];

export default function SiderCustom({ modules, user = null }) {
    const [currentKey, setCurrentKey] = useState(null);
    const [currentComponent, setCurrentComponent] = useState(null);
    const [oldCurrentKey, setOldCurrentKey] = useState(null);
    const [isOnlyModule, setIsOnlyModule] = useState(false);

    useEffect(() => {
        if (window.location.pathname) {
            const pathname = window.location.pathname;

            routes.forEach((route) => {
                if (pathname.includes(route.url)) {
                    setCurrentKey(route.key);
                }
            });
        }
    }, [window.location.pathname]);

    useEffect(() => {
        if (currentKey) {
            const moduleExists = modules.some(
                (module) => module.module === currentKey
            );

            if (modules.length > 1 && !moduleExists) {
                if (user?.profile == 'direct-sale' && currentKey == 'homepage') {
                    goToModule('/orders/directSale');
                }else{
                    window.location.replace("404");
                }
            }
        }
    }, [currentKey]);

    useEffect(() => {
        if (modules.length === 1) {
            const onlyRoute = routes.find(
                (route) => route.key === modules[0].module
            );

            if (onlyRoute) {
                setIsOnlyModule(true);
                setCurrentKey(onlyRoute.key);
                goToModule(onlyRoute.url);
            }
        }
    }, [modules]);

    const goToModule = (url) => {
        if (!window.location.href.includes(url)) {
            window.location.replace(url);
        }
    };

    const closeSchedule = () => {
        setCurrentKey(oldCurrentKey);
        setCurrentComponent(null);
    };

    function MenuComponent({ routes, ...props }) {
        return (
            <Menu
                style={{ marginTop: 32 }}
                mode="inline"
                defaultSelectedKeys={[currentKey]}
                items={routes.map((route) => {
                    const moduleExists = modules.some(
                        (module) => module.module === route.key
                    );

                    if (!moduleExists) {
                        return null;
                    }

                    if (route?.component) {
                        const url = (
                            <a
                                href="#"
                                onClick={() => {
                                    setCurrentComponent(route.component);
                                    setOldCurrentKey(currentKey);
                                    setCurrentKey(route.key);
                                }}
                            >
                                {route.label}
                            </a>
                        );

                        return {
                            key: route.key,
                            icon: route.icon,
                            label: url,
                        };
                    }

                    return {
                        key: route.key,
                        icon: route.icon,
                        label: (
                            <a href={route.url} className="nav-link">
                                {route.label}
                            </a>
                        ),
                    };
                })}
            />
        );
    }

    return (
        <>
            {!isOnlyModule && (
                <>
                    <Sider
                        className="frutaria-navigation-menu"
                        theme="light"
                        breakpoint="xl"
                        collapsedWidth="0"
                        style={{ zIndex: 2 }}
                    >
                        <a href="/" className="nav-link">
                            <img
                                src={Logotype}
                                style={{ width: 170 }}
                                alt="logo"
                            />
                        </a>

                        <MenuComponent routes={routes} />
                    </Sider>
                    <DrawerSchedule
                        open={currentComponent === "DrawerSchedule"}
                        onclose={closeSchedule}
                    />
                </>
            )}
        </>
    );
}

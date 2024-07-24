import React, { useEffect, useRef } from "react";
import { List } from "antd";
import InfiniteScroll from "react-infinite-scroll-component";
import OrderItem from "./OrderItem";

export default function OrdersList({
    loadMoreData,
    data,
    total,
    loadOrder,
    orderActive,
    blockedOrders = [],
    setOrderActive,
    setOrderSelected,
    userId,
    browserToken,
    removeUnlbockBtn
}) {
    const simplifiedData = data
        .map((item) => {
            const isBlocked = blockedOrders.some((blockedOrder) => {
                return (
                    blockedOrder.order_id === item.id &&
                    (blockedOrder.browser_token !== browserToken ||
                        (blockedOrder.user_id == userId &&
                            blockedOrder.browser_token !== browserToken) ||
                        blockedOrder.user_id != userId)
                );
            });

            let isFromUser = false;

            if(!removeUnlbockBtn){
                 isFromUser = blockedOrders.some((blockedOrder) => {
                    return (
                        blockedOrder.order_id === item.id &&
                        blockedOrder.user_id == userId &&
                        blockedOrder.browser_token === browserToken
                    );
                });
            }

            return {
                item,
                isBlocked,
                isFromUser,
            };
        })
        .filter(Boolean);

    const selectedOrderRef = useRef(null);

    useEffect(() => {
        if (selectedOrderRef.current) {
            selectedOrderRef.current.scrollIntoView({
                behavior: "smooth",
                block: "start",
            });
        }
    }, [orderActive]);

    return (
        <div
            id="scrollableDiv"
            className="scrollable-container"
            style={{
                padding: 0,
                overflow: "auto",
            }}
        >
            <InfiniteScroll
                dataLength={total}
                next={loadMoreData}
                hasMore={true}
                endMessage={"Não há mais encomendas"}
                scrollableTarget="scrollableDiv"
            >
                <List
                    dataSource={simplifiedData}
                    renderItem={({ item, isBlocked, isFromUser }) => (
                        <div
                            ref={(ref) => {
                                if (orderActive === item.id) {
                                    selectedOrderRef.current = ref;
                                }
                            }}
                        >
                            <OrderItem
                                item={item}
                                loadOrder={loadOrder}
                                active={orderActive === item.id}
                                blocked={isBlocked}
                                setOrderActive={setOrderActive}
                                setOrderSelected={setOrderSelected}
                                isFromUser={isFromUser}
                                key={item.id}
                            />
                        </div>
                    )}
                />
            </InfiniteScroll>
        </div>
    );
}

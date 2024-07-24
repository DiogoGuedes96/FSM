import { List, Empty, Typography } from "antd";
import React, { useEffect, useState } from "react";
import InfiniteScroll from "react-infinite-scroll-component";
import OrderFullItem from "../OrderFullItem";
import { SearchOutlined } from '@ant-design/icons';

const { Text } = Typography;

export default function ListSearchOrders({ orders, total, loading, input, loadMoreData, openOrder }) {
    return (
        <>
        {orders.length ?
            <>
            {input ? 
                <div style={{ margin: 16 }}>
                    <Text style={{ fontSize: 20 }}><SearchOutlined style={{ marginRight: 16 }} /> {total} resultados de pesquisa para "{input}"</Text>
                </div>
                :
                ''
            }
                <div
                    id="scrollableDiv"
                    className="scrollable-container"
                    style={{
                        overflow: 'auto',
                        margin: 16 
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
                            dataSource={orders}
                            renderItem={(item) => (
                                <OrderFullItem 
                                    item={item} 
                                    loadOrder={openOrder}
                                    isOrderSearched
                                />)}
                        />
                    </InfiniteScroll>
                </div>
            </>
        :
        <Empty
            style={{
                width: '100%',
                marginTop: "35vh",
                padding: "10px"
            }}
            description={
                <span style={{ textAlign: 'center', color: '#00000073', opacity: '1' }}>
                    Não foram encontradas encomendas!
                </span>

            }
        />
        }
        </>
    );
}
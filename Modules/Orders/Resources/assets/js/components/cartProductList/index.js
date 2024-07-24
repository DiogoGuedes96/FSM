import React, { useEffect } from "react";
import { Typography } from "antd";

const { Title } = Typography;

import ProductCard from "./productCard";

export default function CartProductList({
    items,
    removeItemFromCart,
    applyPercentDiscount,
    applyCorrectionPrice,
    handleQuanity,
    typePrice,
}) {
    return (
        <>
            {items.length === 0 ? null : (
                <div style={{ margin: 16 }}>
                    <Title level={5}>Lista de compras</Title>
                    <div>
                        {items.map((item, index) => {
                            return (
                                <ProductCard
                                    item={item}
                                    index={index}
                                    removeItemFromCart={removeItemFromCart}
                                    applyPercentDiscount={applyPercentDiscount}
                                    applyCorrectionPrice={applyCorrectionPrice}
                                    handleQuanity={handleQuanity}
                                    typePrice={typePrice}
                                />
                            );
                        })}
                    </div>
                </div>
            )}
        </>
    );
}

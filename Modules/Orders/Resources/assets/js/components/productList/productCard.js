import React from "react";
import { Card, Typography, Badge } from "antd";

const { Meta } = Card;
const { Text } = Typography;

import Placeholder from "../../../img/productPlaceholder.jpg";

export default function ProductCard({ onCardClick, product, typePrice }) {
    const totalQuantity =
        product?.batches?.length > 0
            ? product?.batches?.reduce((total, batch) => {
                  const value = batch?.quantity ? batch?.quantity : 0;

                  return parseFloat((total + value).toFixed(2));
              }, 0)
            : 0;

    const coverPath = product?.images?.length
        ? product?.images[0]?.image_url
        : Placeholder;
    return (
        <Card
            key={product?.id}
            hoverable
            onClick={() => onCardClick(product)}
            cover={
                <img
                    style={{
                        objectFit: "cover",
                        objectPosition: "center",
                        width: "100%",
                        height: 120,
                    }}
                    alt="Product Image"
                    src={coverPath}
                />
            }
        >
            <Meta
                title={
                    <Text
                        style={{
                            fontSize: 16,
                            color: "#000000E0",
                            marginTop: "6px",
                            textWrap: "wrap",
                        }}
                        ellipsis
                    >
                        {product?.name}
                    </Text>
                }
                description={
                    <>
                        <div
                            style={{
                                display: "flex",
                                alignItems: "center",
                                justifyContent: "center",
                                fontSize: 16,
                                color: "#00000073",
                            }}
                            ellipsis
                        >
                            ID Primavera: {product?.erp_product_id}
                        </div>
                        <div
                            style={{
                                display: "flex",
                                alignItems: "center",
                                justifyContent: "center",
                                fontSize: 16,
                                color: "#00000073",
                            }}
                        >
                            {product[typePrice].toFixed(2) +
                                "€ " +
                                product?.sell_unit}
                        </div>
                        <div
                            style={{
                                display: "flex",
                                alignItems: "center",
                                justifyContent: "center",
                                fontSize: 16,
                                color: "#00000073",
                            }}
                        >
                            {totalQuantity > 0 ? (
                                <Badge
                                    key="available"
                                    color="green"
                                    text={
                                        <Text
                                            style={{
                                                fontSize: 16,
                                                color: "#00000073",
                                            }}
                                        >
                                            Em stock: {totalQuantity}
                                        </Text>
                                    }
                                />
                            ) : (
                                <Badge
                                    key="Not available"
                                    color="red"
                                    text={
                                        <Text
                                            style={{
                                                fontSize: 16,
                                                color: "#00000073",
                                            }}
                                        >
                                            Sem Stock
                                        </Text>
                                    }
                                />
                            )}
                        </div>
                    </>
                }
            />
        </Card>
    );
}

import React, { useEffect, useState } from "react";
import {
    DeleteOutlined,
    EditOutlined,
    PlusOutlined,
    MinusOutlined,
    CloseOutlined,
} from "@ant-design/icons";
import {
    Typography,
    InputNumber,
    Button,
    Form,
    Space,
    Tag,
    message,
} from "antd";

import Placeholder from "../../../img/productPlaceholder.jpg";

const { Text } = Typography;

export default function ProductCard({
    item,
    index,
    removeItemFromCart,
    applyPercentDiscount,
    applyCorrectionPrice,
    handleQuanity,
    typePrice,
}) {
    const product = item
        ? {
              id: item.product.id,
              name: item.product.name,
              originalPrice: item.product[typePrice],
              price: item.price ?? item.product[typePrice],
              unit: item.unit ?? item.product.sell_unit,
              sale_unit: item.sale_unit,
              erp_product_id: item.erp_product_id,
              volume: item.volume,
              quantity: Number(item.quantity),
              image: item.product.images?.length
                  ? item.product.images[0].image_url
                  : Placeholder,
              discount: item.discount ?? 0,
              correctionPricePercentage: item.correctionPricePercentage ?? null,
          }
        : null;

    const [openDiscountForm, setOpenDiscountForm] = useState(false);
    const [showFormCorrectionPrice, setShowFormCorrectionPrice] =
        useState(null);
    const [FormDiscount] = Form.useForm();
    const [FormCorrectionPrice] = Form.useForm();

    const styleCorrectionPrice = {
        color: "#874D00",
        fontWeight: "bold",
    };

    const formatterPrice = (price) => {
        return (price * 100) / 100;
    };

    const parseCurrency = (value) => {
        const parsedValue = parseFloat(value.replace(/[^\d.-]/g, ""));

        if (isNaN(parsedValue)) {
            return null;
        }

        return parsedValue;
    };

    const onFinishDiscount = (values, update = true) => {
        if (values.value > 100) {
            message.error("O valor do desconto deve ser menor que 100%");
            return false;
        }

        applyPercentDiscount({ ...product, product: item.product }, values, index);
        setShowFormCorrectionPrice(false);
        setOpenDiscountForm(false);
    };

    function CustomCloseIcon({ color }) {
        return <CloseOutlined style={{ color: color }} />;
    }

    function ProductDiscountTag({
        discount,
        color,
        onClose,
        style,
        correction,
        onClick,
    }) {
        return (
            <Tag
                style={style}
                closable
                closeIcon={
                    <CustomCloseIcon
                        color={correction ? "#D48806" : "#FFFFFF"}
                    />
                }
                onClick={() => onClick()}
                onClose={() => onClose()}
                color={color}
            >
                {correction ? `Poupa ${discount}%` : `${discount}% OFF`}
            </Tag>
        );
    }

    const removeDiscount = () => {
        applyPercentDiscount(item, { value: 0 }, index);
    };

    const showPriceUpdate = () => {
        setShowFormCorrectionPrice(true);
        setOpenDiscountForm(false);
        removeCorrectionPrice();
    };

    function FormPrice({ form, onFinish, initialValues, messageRule }) {
        return (
            <Form
                form={form}
                name="update_price_form"
                layout="inline"
                onFinish={onFinish}
                initialValues={initialValues}
            >
                <Form.Item
                    name="value"
                    rules={[
                        {
                            required: true,
                            message: messageRule,
                        },
                    ]}
                >
                    <InputNumber
                        formatter={formatterPrice}
                        parser={parseCurrency}
                        min={0}
                        precision={2}
                        name="price"
                        size="large"
                    />
                </Form.Item>
                <Form.Item>
                    <Button type="primary" size="large" htmlType="submit">
                        Aplicar
                    </Button>
                </Form.Item>
            </Form>
        );
    }

    function DiscountProduct() {
        return (
            <>
                {!openDiscountForm ? (
                    <ProductDiscountTag
                        discount={product.discount}
                        color="#237804"
                        onClick={() => {
                            setOpenDiscountForm(true);
                            setShowFormCorrectionPrice(false);
                        }}
                        onClose={() => removeDiscount()}
                        correction={false}
                    />
                ) : (
                    <FormPrice
                        form={FormDiscount}
                        onClick={() => setShowFormCorrectionPrice(false)}
                        onFinish={onFinishDiscount}
                        initialValues={{ value: product.discount }}
                        messageRule={`O campo desconto é obrigatório`}
                    />
                )}
            </>
        );
    }

    function ShowDefaultPrice() {
        const discountPercent = product?.discount && product?.discount > 0;
        return (
            <>
                <Text style={{ marginRight: 8 }} delete={discountPercent}>
                    {product.originalPrice.toLocaleString("pt-PT", {
                        style: "currency",
                        currency: "EUR",
                        minimumFractionDigits: 2,
                    })}{" "}
                    / {product.unit}
                </Text>
                <Button
                    onClick={() => showPriceUpdate()}
                    icon={<EditOutlined />}
                />
            </>
        );
    }

    const removeCorrectionPrice = () => {
        removeDiscount();
        applyCorrectionPrice(item, product.originalPrice, index);
    };

    const transformPrice = (price) => {
        return parseFloat(price.toFixed(2));
    };

    const calculateCorrectionPricePercent = (originalPrice, discount) => {
        return transformPrice(originalPrice - originalPrice * (discount / 100));
    };

    function ShowPriceWithCorrection() {
        const discountPercent = product?.discount && product?.discount > 0;
        return (
            <span
                style={{
                    display: "flex",
                    flexDirection: "column",
                    marginRight: 8,
                }}
            >
                <div style={{ marginBottom: 8 }}>
                    <Text delete>
                        {product.originalPrice.toLocaleString("pt-PT", {
                            style: "currency",
                            currency: "EUR",
                            minimumFractionDigits: 2,
                        })}
                    </Text>
                </div>
                <div
                    style={{
                        display: "flex",
                        justifyContent: "center",
                        alignItems: "center",
                    }}
                >
                    <Text
                        style={{
                            color: "#D48806",
                            fontWeight: "bold",
                            fontSize: 18,
                            marginRight: 8,
                        }}
                    >
                        {calculateCorrectionPricePercent(
                            product.originalPrice,
                            product.correctionPricePercentage
                        ).toLocaleString("pt-PT", {
                            style: "currency",
                            currency: "EUR",
                            minimumFractionDigits: 2,
                        })}
                        /{product.unit}
                    </Text>
                    <Button
                        onClick={() => showPriceUpdate()}
                        icon={<EditOutlined />}
                    />
                </div>
            </span>
        );
    }

    const onFinishUpdatePrice = (values) => {
        applyPercentDiscount(item, { value: 0 }, index);
        if (values.value !== product.originalPrice) {
            const price = values.value;
            applyCorrectionPrice(item, price, index);
        }
        setShowFormCorrectionPrice(false);
    };

    function FinalUnitPrice() {
        const discountPercent = product?.discount && product?.discount > 0;
        const finalPrice = product?.price.toLocaleString("pt-PT", {
            style: "currency",
            currency: "EUR",
            minimumFractionDigits: 2,
        });

        const discountCorrectionPrice = product?.correctionPricePercentage ?? 0;
        const discountPrice = product?.discount ?? 0;
        const totalDiscount = parseFloat(
            (discountCorrectionPrice + discountPrice).toFixed(2)
        );
        return (
            <div
                style={{
                    display: "flex",
                    flexDirection: "column",
                    gap: 8,
                    alignItems: "flex-end",
                }}
            >
                <div>
                    {totalDiscount > 0 && (
                        <ProductDiscountTag
                            discount={totalDiscount}
                            color="#FFC53D"
                            onClose={removeCorrectionPrice}
                            style={styleCorrectionPrice}
                            correction={true}
                        />
                    )}
                </div>
                <div>
                    {discountPercent ? (
                        <Tag
                            style={styleCorrectionPrice}
                            color="#FFC53D"
                        >{`Fica ${finalPrice} / ${product.unit}`}</Tag>
                    ) : (
                        ""
                    )}
                </div>
            </div>
        );
    }

    return (
        <>
            {product && (
                <div
                    className="orders__item"
                    key={index}
                    style={{
                        borderBottom: "2px solid #f0f0f0",
                        padding: "16px 0px",
                    }}
                >
                    <div style={{ margin: "0 16px 10px 0" }}>
                        <img
                            style={{
                                objectFit: "cover",
                                objectPosition: "center",
                                maxWidth: 55,
                                maxHeight: 55,
                            }}
                            alt="Card Image"
                            src={product.image}
                        />
                    </div>
                    <div>
                        <DiscountProduct />
                        <div style={{ marginRight: 16 }}>
                            <div
                                style={{
                                    display: "flex",
                                    justifyContent: "space-between",
                                    alignItems: "center",
                                    gap: 16,
                                }}
                            >
                                <div>
                                    <Text strong>{product.name}</Text>
                                </div>
                                <div>
                                    <Button
                                        onClick={() => {
                                            removeItemFromCart(
                                                product.id,
                                                index
                                            );
                                        }}
                                        shape="circle"
                                        style={{ width: 44, height: 44 }}
                                        icon={<DeleteOutlined />}
                                    />
                                </div>
                            </div>
                            <div>
                                <Text style={{ color: "#00000073" }}>
                                    ID Primavera: {product?.erp_product_id}
                                </Text>
                            </div>
                            <div style={{ margin: "16px 0px 16px 0px" }}>
                                <div
                                    style={{
                                        display: "flex",
                                        justifyContent: "space-between",
                                        alignItems: "baseline",
                                    }}
                                >
                                    {!showFormCorrectionPrice ? (
                                        <Space>
                                            {!product.correctionPricePercentage ? (
                                                <ShowDefaultPrice />
                                            ) : (
                                                <ShowPriceWithCorrection />
                                            )}
                                        </Space>
                                    ) : (
                                        <FormPrice
                                            form={FormCorrectionPrice}
                                            onFinish={onFinishUpdatePrice}
                                            initialValues={{
                                                value: product.originalPrice,
                                            }}
                                            messageRule={`O campo preço é obrigatório`}
                                        />
                                    )}
                                    {<FinalUnitPrice />}
                                </div>
                                <div style={{ marginTop: 8 }}>
                                    <Text strong>
                                        Embalagem: {product?.sale_unit}
                                    </Text>
                                </div>
                            </div>
                            <div
                                style={{
                                    display: "flex",
                                    justifyContent: "space-between",
                                    alignItems: "center",
                                }}
                            >
                                <div style={{ width: "65%" }}>
                                    <InputNumber
                                        size="large"
                                        min={1}
                                        value={product.quantity}
                                        controls={false}
                                        max={999999}
                                        maxLength={6}
                                        addonAfter={
                                            <Button
                                                type="text"
                                                onClick={(event) =>
                                                    handleQuanity(
                                                        event,
                                                        item,
                                                        "increase",
                                                        index,
                                                        1
                                                    )
                                                }
                                                icon={<PlusOutlined />}
                                            />
                                        }
                                        addonBefore={
                                            <Button
                                                type="text"
                                                onClick={(event) =>
                                                    handleQuanity(
                                                        event,
                                                        item,
                                                        "decrease",
                                                        index,
                                                        1
                                                    )
                                                }
                                                disabled={
                                                    product?.quantity <= 0.1
                                                }
                                                icon={<MinusOutlined />}
                                            />
                                        }
                                        onKeyUp={(event) =>
                                            handleQuanity(
                                                event,
                                                item,
                                                "custom",
                                                index
                                            )
                                        }
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </>
    );
}

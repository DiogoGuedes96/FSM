import React, { useState, useEffect } from "react";
import {
    InputNumber,
    Radio,
    Modal,
    Typography,
    Button,
    message,
    Input,
} from "antd";
import { PlusOutlined, MinusOutlined } from "@ant-design/icons";

import Placeholder from "../../../img/productPlaceholder.jpg";
import { initOnKeyDown } from "../../libs/utils";

export default function ProductModal({
    open,
    onCancel,
    product,
    addCart,
    typePrice,
}) {
    const { Title, Text } = Typography;
    const [inputValue, setInputValue] = useState(1);
    const [btnDisable, setbBtnDisable] = useState(false);
    const [unitValue, setUnitValue] = useState(null);
    const [inputVisible, setInputVisible] = useState(false);
    const [customProductUnit, setCustomProductUnit] = useState(null);
    const [selectedValue, setSelectedValue] = useState(null);
    const [productId, setProductId] = useState(null);

    const coverPath = product?.images?.length
        ? product?.images[0]?.image_url
        : Placeholder;

    const handleUnitChange = (e) => {
        if (e) {
            if (e.target.value === "volume") {
                setInputVisible(true);
                setSelectedValue("volume");
                setUnitValue(customProductUnit);
            } else {
                setInputVisible(false);
                setSelectedValue(e.target.value);
                setUnitValue(e.target.value);
            }
        }
    };

    const handleCustomUnit = (e) => {
        if (e) {
            setUnitValue(e.target.value);
            setCustomProductUnit(e.target.value);
        }
    };

    const handleIncrease = (e) => {
        const value = Number(inputValue) + 1;
        setInputValue(value.toFixed(3));
        setbBtnDisable(false);
    };

    const handleDecrease = (e) => {
        if (inputValue > 0.1) {
            const value = Number(inputValue) - 1;
            setInputValue(value.toFixed(3));
        } else {
            setbBtnDisable(true);
        }
    };

    const formatPrice = (price) => {
        return price.toLocaleString("pt-PT", {
            style: "currency",
            currency: "EUR",
            minimumFractionDigits: 2,
        });
    };

    const handleKeyPress = (event) => {
        const allowedKeys = [
            "Backspace",
            "ArrowUp",
            "ArrowDown",
            "ArrowLeft",
            "ArrowRight",
            ".",
        ];
        const inputValue = Number(event.target.value);

        if (
            inputValue === "" ||
            isNaN(inputValue) ||
            (!allowedKeys.includes(event.key) &&
                !/^\d*\.?\d*$/.test(inputValue))
        ) {
            event.preventDefault();
            message.error(
                "O campo quantidade não pode conter letras ou números negativos!"
            );
        } else {
            setInputValue(inputValue.toFixed(3));
            setbBtnDisable(inputValue <= 0.1);
        }
    };

    const keysPress = {
        onEnter: () => {
            let submit = document.getElementById("submit-to-cart");

            if (submit) {
                submit.click();
            }
        },
    };

    useEffect(() => {
        if (product?.id && product?.id !== productId) {
            setProductId(product.id);

            if (product?.sell_unit) {
                if (["KG", "UN", "CX"].includes(product?.sell_unit)) {
                    setUnitValue(product?.sell_unit);
                    setSelectedValue(product?.sell_unit);
                    setCustomProductUnit(null);
                    setInputVisible(false);
                } else {
                    setUnitValue(product?.sell_unit);
                    setCustomProductUnit(product?.sell_unit);
                    setSelectedValue("volume");
                    setInputVisible(true);
                }
            }

            initOnKeyDown(keysPress);
        }
    }, [product]);

    const submitToCart = () => {
        if (!unitValue) {
            message.error("Selecione uma unidade");
            return;
        }

        if (!isNaN(unitValue)) {
            message.error("A unidade/embalagem deve conter letra(s)");
            return;
        }

        if (inputValue <= 0) {
            message.error("Insira uma quantidade válida");
            return;
        }

        if (unitValue && inputValue > 0) {
            addCart({ inputValue, product, unitValue });
        }

        setUnitValue(null);
        setInputValue(1);
        setCustomProductUnit(null);
        setbBtnDisable(false);
        setInputVisible(false);
    };

    return (
        <>
            <Modal
                className="product-modal"
                closable={false}
                open={open}
                onCancel={() => {
                    onCancel();
                }}
                footer={null}
            >
                <div
                    style={{
                        display: "flex",
                        alignItems: "center",
                        flexDirection: "column",
                    }}
                >
                    <img
                        alt="Card Image"
                        src={coverPath}
                        style={{ maxWidth: 200, maxHeigh: 120 }}
                    />
                    {product && (
                        <>
                            <Title level={4} style={{ marginTop: 28 }}>
                                {product.name}
                            </Title>
                            <Text
                                style={{
                                    marginBottom: 8,
                                    fontSize: 16,
                                    opacity: 0.7,
                                }}
                            >
                                ID Primavera: {product?.erp_product_id}
                            </Text>
                            <Text style={{ marginTop: 8, fontSize: 14 }}>
                                {formatPrice(product[typePrice] ?? 0) +
                                    "/" +
                                    product?.sell_unit}
                            </Text>
                            <div>
                                <Radio.Group
                                    className="radio-group--no-break"
                                    size="middle"
                                    onChange={handleUnitChange}
                                    defaultValue={selectedValue}
                                    value={selectedValue}
                                    buttonStyle="solid"
                                    style={{
                                        marginTop: 16,
                                        justifyContent: "center",
                                        display: "flex",
                                        width: "100%",
                                    }}
                                >
                                    <Radio.Button value="UN">
                                        <Text>Unidade</Text>
                                    </Radio.Button>
                                    <Radio.Button value="KG">
                                        <Text>Kg</Text>
                                    </Radio.Button>
                                    <Radio.Button value="CX">
                                        <Text>Caixa</Text>
                                    </Radio.Button>
                                    <Radio.Button value="volume">
                                        <Text>Embalagem</Text>
                                    </Radio.Button>
                                </Radio.Group>
                                {inputVisible && (
                                    <Input
                                        maxLength={35}
                                        showCount
                                        placeholder="Insira uma embalagem"
                                        onChange={handleCustomUnit}
                                        value={customProductUnit}
                                        size="large"
                                        style={{
                                            width: "100%",
                                            margin: "8px 0px 16px 0px",
                                        }}
                                    />
                                )}
                            </div>
                            <div
                                style={{
                                    marginTop: 16,
                                    maxWidth: 200,
                                    height: 70,
                                }}
                            >
                                <InputNumber
                                    size="large"
                                    min={1}
                                    max={999999}
                                    maxLength={6}
                                    onKeyUp={handleKeyPress}
                                    controls={false}
                                    showCount={true}
                                    value={inputValue}
                                    defaultValue={1}
                                    addonAfter={
                                        <Button
                                            type="text"
                                            onClick={handleIncrease}
                                        >
                                            <PlusOutlined />
                                        </Button>
                                    }
                                    addonBefore={
                                        <Button
                                            type="text"
                                            onClick={handleDecrease}
                                            disabled={btnDisable}
                                        >
                                            <MinusOutlined />
                                        </Button>
                                    }
                                />
                            </div>

                            <Button
                                id="submit-to-cart"
                                type="primary"
                                size="large"
                                block
                                onClick={submitToCart}
                                style={{
                                    width: "100%",
                                    maxWidth: 350,
                                    marginTop: 4,
                                    color: "#613400",
                                }}
                            >
                                Adicionar
                            </Button>
                        </>
                    )}
                </div>
            </Modal>
        </>
    );
}

import React, { useState, useEffect } from "react";
import {
    InputNumber,
    Modal,
    Typography,
    Button,
    message,
    Select,
    Form,
} from "antd";

import Placeholder from "../../../img/productPlaceholder.jpg";
import { initOnKeyDown } from "../../libs/utils";

const { Title, Text } = Typography;

export default function DirectSaleProductModal({
    open,
    onCancel,
    product,
    addCart,
}) {
    const [quantity, setQuantity] = useState(1);
    const [volume, setVolume] = useState(null);
    const [batchId, setBatchId] = useState(null);
    const [productId, setProductId] = useState(null);
    const [filteredProductBatchs, setFilteredProductBatchs] = useState([]);

    const coverPath = product?.images?.length
        ? product?.images[0]?.image_url
        : Placeholder;


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
        const quantityAux = Number(event.target.value);

        if (
            quantityAux === "" ||
            isNaN(quantityAux) ||
            (!allowedKeys.includes(event.key) &&
                !/^\d*\.?\d*$/.test(quantityAux))
        ) {
            event.preventDefault();
            message.error(
                "O campo quantidade não pode conter letras ou números negativos!"
            );
        } else {
            setQuantity(quantityAux.toFixed(3));
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
        if (product && Object.keys(product).length > 0 ){
            if (product?.id && product?.id !== productId) {
                setProductId(product.id);
                initOnKeyDown(keysPress);
            }

            if(product?.batches && product?.batches.length > 0){
                let batches =  product?.batches.map((batche, index) => {
                    return {
                        key: batche?.id,
                        value: batche?.id,
                        label: batche?.description,
                        quantity:batche?.quantity
                    };
                }) ?? [];

                let filteredBatches = [];
    
                if (batches?.length > 0) {
                    batches.forEach(item => {
                        if (item?.quantity > 0) {
                            filteredBatches.push(item);
                        }
                    });
                    setFilteredProductBatchs(filteredBatches);
                }
            }
        }
    }, [product]);

    const submitToCart = () => {
        addCart({ quantity, product, volume, batchId });

        setQuantity(1);
        setVolume(null);
        setBatchId(null);
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
                        padding: '12px 32px 12px 32px',
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
                                {formatPrice(product?.avg_price ?? 0) +
                                    "/" +
                                    product?.sell_unit}
                            </Text>
                            <div style={{ display: 'flex', flexDirection: 'column', marginTop: 12 }}>
                                <Form layout="vertical">
                                    <div >
                                        <Form.Item
                                            style={{ minWidth: 135 }}
                                            label="Lote"
                                        >
                                            <Select
                                                size="large"
                                                options={filteredProductBatchs}
                                                onChange={(value) => setBatchId(value)}
                                                allowClear
                                            />
                                        </Form.Item>
                                    </div>
                                    <div style={{ display: 'flex', flexDirection: 'row', width: '100%', gap: 12, marginTop: 12 }}>
                                        <div>
                                            <Form.Item label="Quantidade">
                                                <InputNumber
                                                    style={{ width: "100%" }}
                                                    min={0}
                                                    max={999999}
                                                    defaultValue={1}
                                                    maxLength={6}
                                                    size="large"
                                                    addonAfter={product?.sell_unit} 
                                                    onStep={(value) => setQuantity(value)}
                                                    onKeyUp={(event) => handleKeyPress(event)}
                                                    type="number"
                                                    inputMode="decimal"
                                                />
                                            </Form.Item>
                                        </div>
                                        <div>
                                            <Form.Item label="Volume">
                                                <InputNumber
                                                    style={{ width: "100%" }}
                                                    min={0}
                                                    max={999999}
                                                    maxLength={6}
                                                    size="large"
                                                    onStep={(value) => setVolume(value.toString())}
                                                    onKeyUp={(event) => setVolume(event?.target?.value)}
                                                    type="number"
                                                    inputMode="numeric"
                                                />
                                            </Form.Item>
                                        </div>
                                    </div>
                                </Form>
                            </div>
                            <Button
                                block
                                id="submit-to-cart"
                                type="primary"
                                size="large"
                                onClick={submitToCart}
                                style={{
                                    width: "100%",
                                    marginTop: 12,
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

import React, { useEffect, useState } from "react";
import { useMutation, useQuery } from "react-query";
import ShoppingCart from "../components/shoppingCart";
import ProductList from "../components/productList";
import CartProductList from "../components/cartProductList";
import PurchaseSummary from "../components/purchaseSummary";
import { Button, Modal, message } from "antd";
import { ExclamationCircleFilled, ShoppingCartOutlined } from "@ant-design/icons";
import {
    getOrderData,
    saveNewOrder,
    updateOrderAndProducts,
} from "../libs/apis";
import { checkTypePrice } from "../libs/utils";
const { confirm } = Modal;

export default function NewOrder() {
    const clientFromCall = localStorage.getItem("clientFromCall");
    const [orderId, setOrderId] = useState(null);
    const [clientData, setClientData] = useState(null);
    const [clientOrder, setClientOrder] = useState(null);
    const [callerPhone, setCallerPhone] = useState(null);
    const [orderNotes, setOrderNotes] = useState(null);
    const [cartProducts, setCartProducts] = useState([]);
    const [newOrderData, setNewOrderData] = useState([]);
    const [defaultDiscount, setDefaultDiscount] = useState(0);
    const [typePrice, setSetTypePrice] = useState(checkTypePrice(0));
    const [orderStorage, setOrderStorage] = useState(
        localStorage.getItem("newOrder")
    );
    const [purchaseBox, setPurchaseBox] = useState(false);

    const {
        data: order,
        isSuccess: isSuccessOrder,
        isError: isErrorOrder,
        refetch,
    } = useQuery(
        ["get-orders", orderId],
        async () => {
            if (!orderId) return null;
            return await getOrderData(orderId);
        },
        { refetchOnWindowFocus: false }
    );

    useEffect(() => {
        if (!orderId) {
            const urlParams = new URLSearchParams(window.location.search);
            const orderParam = urlParams.get("order");

            if (orderParam) {
                setOrderId(orderParam);
                refetch();
            } else if (orderStorage) {
                confirm({
                    title: "Existe uma encomenda em andamento!",
                    icon: <ExclamationCircleFilled />,
                    content: "Gostaria de contiuar de onde parou?",
                    okText: "Sim, continuar",
                    cancelText: "Não, começar uma nova",
                    onOk() {
                        setOrderId(orderStorage);
                        refetch();
                    },
                    onCancel() {
                        localStorage.removeItem("newOrder");
                    },
                });
            }
        }
    }, []);

    const {
        data: newOrderMutationData,
        isSuccess: isSuccessMutation,
        isError: isErrorMutation,
        mutate: mutateSaveNewOrderData,
    } = useMutation(["mutationSaveNewOrderData"], saveNewOrder);

    const {
        data: newOrderStoredMutationData,
        isSuccess: isSuccessStoredMutation,
        isError: isErrorStoredMutation,
        mutate: mutateStoredSaveNewOrderData,
    } = useMutation(["mutationSaveNewOrderData"], saveNewOrder);

    const {
        data: updateOrderMutationData,
        isSuccess: isUpdateSuccessMutation,
        isError: isUpdateErrorMutation,
        mutate: mutateUpdateOrderAndProducts,
    } = useMutation(["mutationUpdateSaveNewOrderData"], updateOrderAndProducts);

    const {
        data: updateOrderStoredMutationData,
        isSuccess: isUpdateSuccessStoredMutation,
        isError: isUpdateErrorStoredMutation,
        mutate: mutateStoredUpdateOrderAndProducts,
    } = useMutation(["mutationUpdateSaveNewOrderData"], updateOrderAndProducts);

    useEffect(() => {
        if (isSuccessMutation || isUpdateSuccessMutation) {
            localStorage.removeItem("newOrder");

            const order = newOrderMutationData?.order?.id ?? orderId;
            window.location.href = `/orders/newOrder/checkout?order=${order}`;
        }
    }, [isSuccessMutation, isUpdateSuccessMutation]);

    useEffect(() => {
        if (isSuccessStoredMutation || isUpdateSuccessStoredMutation) {
            const order = newOrderStoredMutationData?.order?.id || orderId;
            localStorage.setItem("newOrder", order);
            setOrderId(order);
            message.success("Os dados foram guardados");
        }
    }, [isSuccessStoredMutation, isUpdateSuccessStoredMutation]);

    useEffect(() => {
        if (isErrorMutation) {
            message.error("Erro ao registar encomenda");
        }
    }, [isErrorMutation]);

    useEffect(() => {
        if (isErrorOrder) {
            message.error("Erro ao encontrar encomenda.");
        }
    }, [isErrorOrder]);

    useEffect(() => {
        if (isSuccessOrder && order) {
            const products = order.order_products.map((item) => {
                return {
                    key: item?.id ? item?.id : null,
                    order_products_id: item?.id ? item?.id : null,
                    product: item.bms_product,
                    unit: item.bms_product.sale_unit,
                    quantity: item.quantity,
                    price: item.sale_price,
                    correctionPrice: item.correction_price_percent,
                    discount: item.discount_percent,
                    avg_price: item.bms_product[typePrice],
                    erp_product_id: item.bms_product.erp_product_id,
                    sale_unit: item.sale_unit,
                    correctionPricePercentage: handleCorrectionPricePercentage(item),
                    edit: true,
                };
            });

            setClientOrder(order.client);
            setCartProducts(products);
        }
    }, [isSuccessOrder]);

    const onAssociateClient = (callerPhone, client) => {
        if (client) {

            if(order && Object.keys(order).length > 0){
                setOrderNotes(order?.description);
            }else{
                setOrderNotes(client?.notes);
            }
            setClientData(client);

            if (client.tipo_preco && Number(client.tipo_preco) > 0) {
                setSetTypePrice(checkTypePrice(Number(client.tipo_preco)));
                setCartProducts(
                    cartProducts.map((item) => {
                        item.price = defaultDiscount
                            ? calculateDiscountPrice(
                                  item.product[typePrice],
                                  defaultDiscount
                              )
                            : item.product[typePrice];
                        item.discount = defaultDiscount ?? 0;
                        item.correctionPrice = 0;
                        item.correctionPricePercentage = 0;

                        return item;
                    })
                );
            }

            if (client.discount_default) {
                setDefaultDiscount(client.discount_default);
                cartProducts.map((cartProduct) => {
                    if (!cartProduct.correctionPricePercentage) {
                        applyPercentDiscount(cartProduct, {
                            value: client.discount_default,
                        });
                    }
                });
            }
        }

        if (callerPhone) {
            setCallerPhone(callerPhone);
        }
    };

    const addCartProduct = ({ inputValue, product, unitValue }) => {
        setCartProducts([
            ...cartProducts,
            {
                product: product,
                quantity: inputValue,
                sale_unit: unitValue,
                unit: product.sale_unit,
                erp_product_id: product.erp_product_id,
                price: defaultDiscount
                    ? calculateDiscountPrice(
                          product[typePrice],
                          defaultDiscount
                      )
                    : product[typePrice],
                discount: defaultDiscount ?? 0,
                correctionPrice: 0,
                correctionPricePercentage:
                    handleCorrectionPricePercentage(product),
            },
        ]);
    };

    const handleCorrectionPricePercentage = (product) => {
        if (!product) {
            return;
        }

        const doesNotHaveDicountPercent =
            !defaultDiscount && product?.discount_percent == 0;

        if (product?.price != product[typePrice] && doesNotHaveDicountPercent) {
            return calculateCorrectionPricePercent(
                product.price,
                product[typePrice]
            );
        }

        if (
            product?.unit_price !== product?.sale_price &&
            doesNotHaveDicountPercent
        ) {
            return calculateCorrectionPricePercent(
                product.unit_price,
                product.sale_price
            );
        }

        return product?.correction_price_percent;
    };

    const removeProductFromCart = (productId, index) => {
        setCartProducts(cartProducts.filter((product, i) => i !== index));
    };

    const calculateDiscountPrice = (price, discount) => {
        return transformPrice(price - (price * discount) / 100);
    };

    const transformPrice = (price) => {
        return parseFloat(price.toFixed(2));
    };

    const applyPercentDiscount = (product, discount, index) => {
        const newCartProducts = cartProducts.map((cartProduct, internalIndex) => {
            if (cartProduct.product.id === product.product.id && internalIndex === index) {
                if (product?.discount) {
                    product.price = product.product[typePrice];
                }

                if (
                    product?.correctionPricePercentage &&
                    product.correctionPricePercentage != null
                ) {
                    product.price = calculateDiscountPrice(
                        product.product[typePrice],
                        product.correctionPricePercentage
                    );
                }

                product.price = calculateDiscountPrice(
                    product.price,
                    discount.value
                );

                product.discount = discount.value;
                return product;
            }

            return cartProduct;
        });

        setCartProducts(newCartProducts);
    };

    const applyCorrectionPrice = (product, newPrice, index) => {
        const newCartProducts = cartProducts.map((cartProduct, internalIndex) => {
            if (cartProduct.product.id === product.product.id && internalIndex === index) {
                if (newPrice == product.product[typePrice]) {
                    product.correctionPricePercentage = null;
                    product.price = transformPrice(product.product[typePrice]);
                    return product;
                }
                product.correctionPricePercentage = transformPrice(
                    calculateCorrectionPricePercent(
                        product.product[typePrice],
                        newPrice
                    )
                );
                product.price = transformPrice(newPrice);

                return product;
            }

            return cartProduct;
        });

        setCartProducts(newCartProducts);
    };

    const handleQuanity = (event, product, action, productIndex, quantity = null) => {
        const newCartProducts = cartProducts.map((cartProduct, index) => {
            if (cartProduct.product.id === product.product.id && productIndex === index) {
                if (action === "increase") {
                    product.quantity = (
                        Number(product.quantity) + Number(quantity)
                    ).toFixed(3);
                } else if (action === "decrease") {
                    product.quantity = (
                        Number(product.quantity) - Number(quantity)
                    ).toFixed(3);
                } else if (action === "custom") {
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
                        inputValue <= 0 ||
                        (!allowedKeys.includes(event.key) &&
                            !/^\d*\.?\d*$/.test(inputValue))
                    ) {
                        event.preventDefault();
                        message.error(
                            "O campo quantidade não pode conter letras ou números negativos!"
                        );
                    } else {
                        product.quantity = parseFloat(inputValue).toFixed(3);
                    }
                }
            }

            return cartProduct;
        });

        setCartProducts(newCartProducts);
    };

    const calculateCorrectionPricePercent = (originalPrice, newPrice) => {
        const discount = originalPrice - newPrice;
        return transformPrice((discount * 100) / originalPrice);
    };

    useEffect(() => {
        if (newOrderMutationData) {
            setNewOrderData(newOrderMutationData);
        }
    }, [newOrderMutationData]);

    const sendToCheckout = (saveInStorage = false) => {
        if (!cartProducts.length) {
            return;
        }

        const orderProducts = cartProducts.map((cartProduct) => {
            const {
                product,
                sale_unit,
                quantity,
                price,
                correctionPricePercentage,
                discount,
                order_products_id,
            } = cartProduct;

            return {
                bms_product: product?.id,
                order_products_id: order_products_id,
                sale_unit,
                quantity,
                price,
                correctionPrice: correctionPricePercentage,
                discount,
            };
        });

        if (orderId && saveInStorage === true) {
            mutateStoredUpdateOrderAndProducts({
                id: orderId ?? null,
                orderNotes: orderNotes,
                bmsClient: clientData?.id,
                orderProducts: orderProducts,
                callerPhone: callerPhone,
            });
        } else if (orderId) {
            mutateUpdateOrderAndProducts({
                id: orderId ?? null,
                orderNotes: orderNotes,
                bmsClient: clientData?.id,
                orderProducts: orderProducts,
                callerPhone: callerPhone,
            });
        } else if (saveInStorage === true) {
            mutateStoredSaveNewOrderData({
                orderNotes: orderNotes,
                bmsClient: clientData?.id,
                orderProducts: orderProducts,
                callerPhone: callerPhone,
            });
        } else {
            mutateSaveNewOrderData({
                orderNotes: orderNotes,
                bmsClient: clientData?.id,
                orderProducts: orderProducts,
                callerPhone: callerPhone,
            });
        }
    };

    const saveChanges = () => {
        sendToCheckout(true);
    };

    const handlePurchaseClick = () => {
        setPurchaseBox(!purchaseBox)
    }

    return (
        <div className="order-list--type-2">
            <div className="order-list__content">
                <ProductList
                    isProductFilters
                    addCartProduct={addCartProduct}
                    typePrice={typePrice}
                />
            </div>
            <div className={`order-list__purchase ${purchaseBox ? 'active' : ''}`}>
                <button type="button" className="purchase__open-button" onClick={handlePurchaseClick}>
                    <ShoppingCartOutlined />
                </button>
                <div className="purchase__scroll">
                    <ShoppingCart
                        order={order}
                        clientOrder={clientOrder}
                        setOrderNotes={setOrderNotes}
                        addCartProduct={addCartProduct}
                        onAssociateClient={onAssociateClient}
                    />
                    <CartProductList
                        items={cartProducts}
                        removeItemFromCart={removeProductFromCart}
                        applyPercentDiscount={applyPercentDiscount}
                        applyCorrectionPrice={applyCorrectionPrice}
                        handleQuanity={handleQuanity}
                        typePrice={typePrice}
                    />
                </div>
                <div className="purchase__bottom">
                    <PurchaseSummary short products={cartProducts} />
                    <div style={{ margin: 16 }}>
                        <Button
                            type="primary"
                            block
                            size="large"
                            disabled={!cartProducts.length || !clientData?.id}
                            onClick={sendToCheckout}
                        >
                            Confirmar encomenda
                        </Button>

                        <Button
                            size="large"
                            block
                            disabled={!cartProducts.length}
                            onClick={saveChanges}
                            style={{ marginTop: 5 }}
                        >
                            Guardar dados
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    );
}

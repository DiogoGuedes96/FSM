import React, { useEffect, useState } from "react";
import {
    Table,
    Space,
    Avatar,
    Form,
    Input,
    Typography,
    message,
    Select,
    InputNumber,
    Button,
    Checkbox,
} from "antd";
import { useQuery, useMutation } from "react-query";
import { getOrderData, updateDirectSaleOrder } from "../libs/apis";
import { calculateOrderTotalValue, calculateProductFinalPrice, convertCurrencyFormat, initOnKeyDown } from "../libs/utils";
import { TableOutlined } from "@ant-design/icons";

import clientIcon from "../../img/client-icon.svg";
import ConfirmOrderModal from "../components/directSale/confirmOrderModal";
import OrderSummary from "../components/directSale/checkout/orderSummary";
import { debounce } from "lodash";

import Placeholder from "../../../assets/img/productPlaceholder.jpg";


const { TextArea } = Input;
const { Text } = Typography;

export default function DirectSaleCheckout() {
    const [order, setOrder] = useState(null);
    const [orderId, setOrderId] = useState(null);
    const [orderTotalValue, setOrderTotalValue] = useState(0);
    const [submitModal, setSubmitModal] = useState(false);
    const [tableMobile, setTableMobile] = useState(true);
    const [messageApi, contextHolder] = message.useMessage();
    const [expandedNotes, setExpandedNotes] = useState({});
    const [withoutRequisition, setWithoutRequisition] = useState(true);
    const [requestValue, setRequestValue] = useState('Sem requisição');

    const [initialValues, setInitialValues] = useState({});
    const [productsForm] = Form.useForm();

    const keysPress = {
        onEnter: () => {
            onConfirm()
        },
    };
    const { data: dataOrderQuery, isSuccess: isSuccessQuery } = useQuery(
        ["orderData", orderId],
        async () => {
            if (!orderId) return null;
            return await getOrderData(orderId);
        },
        { refetchOnWindowFocus: false }
    );

    useEffect(() => {
        if (initialValues) {
            productsForm.setFieldsValue(initialValues);
        }
    }, [initialValues, productsForm]);

    const {
        isLoading: isLoadingMutation,
        mutate: mutateOrder,
    } = useMutation(["updateDirectSaleOrderMutation"], updateDirectSaleOrder, {
        onSuccess: () => {
            window.location.href =
                "/orders/directSale/summary?order=" + orderId + "&success=true";
        },
        onError: () => {
            message.error("Erro ao atualizar o pedido");
        },
    });

    useEffect(() => {
        if (isSuccessQuery) {
            const productsOrder = dataOrderQuery.order_products.map(
                (orderProduct) => {
                    return {
                        images:
                            orderProduct.bms_product.images.length > 0
                                ? orderProduct.bms_product.images[0].image_url
                                : null,
                        batches:
                            orderProduct?.bms_product?.batches?.length > 0
                                ? orderProduct.bms_product.batches
                                : null,
                        id: orderProduct.id,
                        name: orderProduct.name,
                        quantity: orderProduct.quantity,
                        unit: orderProduct.unit,
                        sale_unit: orderProduct.sale_unit,
                        conversion: orderProduct.conversion,
                        volume: orderProduct.volume,
                        price: orderProduct.unit_price,
                        iva: orderProduct.bms_product.iva,
                        unit_price: orderProduct.unit_price,
                        sale_price: orderProduct?.sale_price,
                        discount_value: orderProduct.discount_value,
                        notes: orderProduct.notes,
                        stock: orderProduct?.bms_product?.current_stock,
                        bms_product_batch: orderProduct?.bms_product_batch,
                        bms_product: orderProduct?.bms_product,
                        erp_product_id:
                            orderProduct?.bms_product?.erp_product_id,
                    };
                }
            );

            const initialProductsAux = productsOrder.map((p) => {
                return {
                    [`volume-${p.id}`]: p.volume ?? null,
                    [`conversion-${p.id}`]: p.conversion ?? null,
                    [`notes-${p.id}`]: p.notes ?? null,
                    [`batch-${p.id}`]: p.bms_product_batch ?? null,
                };
            });

            const initialProducts = {};
            initialProductsAux.forEach((product) => {
                Object.assign(initialProducts, product);
            });

            if(dataOrderQuery?.request_number){
                if(dataOrderQuery?.request_number !== 'Sem requisição'){
                    setWithoutRequisition(false);
                    setRequestValue(dataOrderQuery?.request_number);
                }
            }

            setInitialValues({ request: dataOrderQuery?.request_number, description: dataOrderQuery?.description, ...initialProducts });

            setOrder({
                ...dataOrderQuery,
                products: productsOrder,
            });

            setOrderTotalValue(calculateOrderTotalValue(productsOrder));
        }
    }, [isSuccessQuery]);

    const onConfirm = () => {
        const values = productsForm.getFieldsValue();

        const splitValues = {
            products: order.products.map((product) => {
                return {
                    id: product.id,
                    bms_product: product.bms_product,
                    unavailability:
                        values[`unavailability-${product.id}`] ?? null,
                    volume: values[`volume-${product.id}`] ?? null,
                    quantity: values[`quantity-${product.id}`] ?? null,
                    conversion: values[`conversion-${product.id}`] ?? null,
                    notes: values[`notes-${product.id}`]?.target?.value ?? null,
                    batch: values[`batch-${product.id}`] ?? null,
                };
            })
        };

        productsForm.validateFields(['description', 'request']).then((response) => {
            if (validateProduct(splitValues.products)) {
                return;
            }

            setSubmitModal(true);
        })
    };

    const validateProduct = (products) => {
        let hasError = false;

        products.forEach((product, key) => {
            const invalidFields = checkFields(product);
            if (invalidFields.length > 0) {
                const nameProduct =
                    order?.order_products.find((p) => p.id === product.id)
                        ?.name ?? "";

                invalidFields.forEach((property) => {
                    hasError = true;
                    messageApi.open({
                        type: "error",
                        content: `O campo ${property} é obrigatório para o produto ${nameProduct}.`,
                    });
                });
            }
        });

        return hasError;
    };

    const checkFields = (product) => {
        const invalidFields = [];
        if (product.volume === null || product.volume === undefined) {
            if (product?.bms_product?.active == 1) {
                invalidFields.push("Volume");
            }
        }

        if (
            product.conversion === null ||
            product.conversion === undefined
        ) {
            invalidFields.push("Quantidade");
        }

        order.products.forEach((orderProduct) => {
            if (orderProduct.id == product.id) {
                if (orderProduct?.batches?.length > 0) {
                    if (product?.batch == null) {
                        invalidFields.push("Lote");
                    }
                }
            }
        });

        return invalidFields;
    };

    useEffect(() => {
        if (!orderId) {
            const urlParams = new URLSearchParams(window.location.search);
            const orderParam = urlParams.get("order");
            if (orderParam) {
                setOrderId(orderParam);
            }
        }

        initOnKeyDown(keysPress);
    }, []);

    const submitFinishOrder = (status) => {
        const values = productsForm.getFieldsValue();

        const orderData = {
            products: order.products.map((product) => {
                return {
                    id: product.id,
                    bms_product: product.bms_product,
                    volume: values[`volume-${product.id}`] ?? null,
                    quantity: values[`conversion-${product.id}`] ?? null,
                    conversion: values[`conversion-${product.id}`] ?? null,
                    notes: expandedNotes[product.id] ?? null,
                    batch: values[`batch-${product.id}`] ?? null,
                };
            }),
        };

        const orderFields = {
            id: order?.id,
            status: status,
            notes: values?.description,
            request: requestValue,
        }

        mutateOrder({
            orderId: orderId,
            data: { values: orderFields, orderData: orderData },
        });
    };

    const handleOpenSubmitOrderModal = () => {
        onConfirm();
    }

    const handleMobileTable = () => {
        setTableMobile(!tableMobile);
    };

    const columns = [
        {
            width: '25%',
            title: "Descrição",
            dataIndex: "product",
            key: "product",
            render: (_, record) => {
                return (
                    <div
                        className="responsive-table__title"
                        data-label="Descrição"
                    >
                        <div
                            className="responsive-table__cell"
                            style={{
                                display: "flex",
                                alignItems: "center",
                                gap: "0.5rem",
                            }}
                        >
                            <img
                                style={{
                                    objectFit: "cover",
                                    objectPosition: "center",
                                    maxWidth: 55,
                                    maxHeight: 55,
                                }}
                                alt="Product Image"
                                src={record.image ?? Placeholder}
                            />
                            <Text
                                style={{
                                    wordBreak: "inherit",
                                    minWidth: "150px",
                                }}
                            >
                                {record.name}
                            </Text>
                        </div>
                    </div>
                );
            },
        },
        {
            width: '10%',
            title: "ID Primavera",
            dataIndex: "erp_product_id",
            key: "erp_product_id",
            render: (_, record) => {
                return (
                    <div
                        className="responsive-table__title"
                        data-label="ID Primavera"
                    >
                        <div className="responsive-table__cell">
                            {record.erp_product_id}
                        </div>
                    </div>
                );
            },
        },
        {
            width: "15%",
            title: "Lote",
            dataIndex: "batch",
            key: "batch",
            render: (_, record) => {
                let batches = record?.batches.map((batche, index) => {
                    return {
                        key: batche?.id,
                        value: batche?.id,
                        label: batche?.description,
                        quantity: batche?.quantity
                    };
                }) ?? [];
                let filteredBatches = [];

                if (batches?.length > 0) {
                    batches.forEach(item => {
                        if (item?.quantity > 0) {
                            filteredBatches.push(item);
                        }
                    });
                }

                return (
                    <div className="responsive-table__title" data-label="Lote">
                        <div className="responsive-table__cell responsive-table__cell-select">
                            <Form.Item
                                name={`batch-${record.id}`}
                                style={{ minWidth: 135 }}
                            >
                                <Select
                                    size="large"
                                    options={filteredBatches}
                                    allowClear
                                />
                            </Form.Item>
                        </div>
                    </div>
                );
            },
        },
        {
            width: "13%",
            title: "Quantidade",
            dataIndex: "convertion",
            key: "convertion",
            render: (_, record) => {
                return (
                    <div
                        className="responsive-table__title"
                        data-label="Quantidade"
                    >
                        <div className="responsive-table__cell responsive-table__cell-input-1">
                            <Form.Item name={`conversion-${record.id}`}>
                                <InputNumber
                                    style={{ width: "100%" }}
                                    min={0}
                                    max={999999}
                                    maxLength={6}
                                    size="large"
                                    suffix={record.unit}
                                    onKeyUp={(e) => handleKeyPress(e, 'Quantidade')}
                                    onChange={(e) => debouncedCalcTotalValue(record, e)}
                                    className="responsive-table__field"
                                    type="number"
                                    inputMode="decimal"
                                />
                            </Form.Item>
                        </div>
                    </div>
                );
            },
        },
        {
            width: "13%",
            title: "Volume",
            dataIndex: "volume",
            key: "volume",
            render: (_, record) => {
                return (
                    <div
                        className="responsive-table__title"
                        data-label="Volume"
                    >
                        <div className="responsive-table__cell responsive-table__cell-input-1">
                            <Form.Item name={`volume-${record.id}`}>
                                <InputNumber
                                    style={{ width: "100%" }}
                                    min={0}
                                    max={999999}
                                    maxLength={6}
                                    size="large"
                                    suffix={record.unit}
                                    onKeyUp={(e) => handleKeyPress(e, 'Volume')}
                                    className="responsive-table__field"
                                    type="number"
                                    inputMode="numeric"
                                />
                            </Form.Item>
                        </div>
                    </div>
                );
            },
        },
        {
            width: "10%",
            title: "Preço UN",
            dataIndex: "price",
            key: "price",
            render: (_, record) => {
                const totalDiscountValue = record.discount_value;

                return (
                    <div
                        className="responsive-table__title"
                        data-label="Preço UN"
                    >
                        <div className="responsive-table__cell">
                            <span>
                                {convertCurrencyFormat(record.unit_price ?? 0)} /{" "}
                                {record.unit}
                            </span>
                        </div>
                    </div>
                );
            },
        },
        {
            width: "9%",
            title: "Desconto",
            dataIndex: "discounts",
            key: "discounts",
            render: (_, record) => {
                const totalDiscountValue = record.discount_value;

                return (
                    <div
                        className="responsive-table__title"
                        data-label="Desconto"
                    >
                        <div className="responsive-table__cell">
                            {<span>{convertCurrencyFormat(totalDiscountValue)}</span>}
                        </div>
                    </div>
                );
            },
        },
        {
            width: "9%",
            title: "Valor Total",
            dataIndex: "totalValue",
            key: "totalValue",
            render: (_, record) => {
                return (
                    <div
                        className="responsive-table__title"
                        data-label="Valor total"
                    >
                        <div className="responsive-table__cell">
                            <span>
                                {calculateProductFinalPrice(record)?.toFixed(2)}
                            </span>
                        </div>
                    </div>
                );
            },
        },
    ];

    const handleQuantityChange = (record, value) => {
        const products = order?.products.map((product) => {
            if (product.id === record?.id) {
                return {
                    ...record,
                    conversion: value,
                    quantity: value
                };
            }
            return product;
        });

        setOrderTotalValue(calculateOrderTotalValue(products));

        setOrder({ ...order, products: products });
    }

    const saveNotesToProduct = (record, event) => {
        const products = order?.products.map((product) => {
            if (product.id === record?.id) {
                product.notes = event.target.value;
            }
            return product;
        });

        setExpandedNotes(prevState => ({
            ...prevState,
            [record.id]: event.target.value
        }));

        setOrder({ ...order, products: products });
    };

    const handleRequestNumberChange = (value) => {
        setRequestValue(value);
    };

    const debouncedCalcTotalValue = debounce(handleQuantityChange, 1000);
    const debouncedProductNotes = debounce(saveNotesToProduct, 1000);
    const debouncedOrderRequestNumber = debounce(handleRequestNumberChange, 1000);

    const handleKeyPress = (event, label) => {
        const allowedKeys = [
            "Backspace",
            "ArrowUp",
            "ArrowDown",
            "ArrowLeft",
            "ArrowRight",
            "Tab",
        ];
        const validNumberRegex = /^[0-9.]$/;

        if (
            !validNumberRegex.test(event.key) &&
            !allowedKeys.includes(event.key)
        ) {
            event.preventDefault();
            message.error(
                `O campo ${label} só aceita valores numéricos, inteiros ou decimais!`
            );
            return false;
        }
    };

    const handleCheckboxChange = (e) => {
        setWithoutRequisition(e.target.checked);
        if (e.target.checked) {
            setRequestValue("Sem requisição");
        }else {
            setRequestValue("");
        }
    };

    function Top() {
        return (
            <>
                <div style={{ display: "flex", justifyContent: "flex-end", width: "100%", marginBottom: 8 }}>
                    <Button
                        className={`orders__change-table-button ${tableMobile ? "active" : ""}`}
                        title="Mudar visualização para tabela padrão"
                        type="button"
                        onClick={handleMobileTable}
                    >
                        <TableOutlined />
                    </Button>
                </div>
                <Table
                    className={`orders__responsive-table ${tableMobile ? "mobile-on" : ""}`}
                    dataSource={order?.products}
                    columns={columns}
                    pagination={false}
                    rowKey="id"
                    expandable={{
                        expandedRowRender: (record) => {
                            return (
                                <>
                                    <Form.Item
                                        name={`notes-${record.id}`}
                                        valuePropName="checked"
                                    >
                                        <TextArea
                                            size="large"
                                            placeholder="Observações"
                                            maxLength="255"
                                            showCount
                                            defaultValue={expandedNotes[record.id] ?? record?.notes ?? ""}
                                            onChange={(e) => debouncedProductNotes(record, e)}
                                        />
                                    </Form.Item>
                                </>
                            );
                        },
                        expandRowByClick: true,
                        showExpandColumn: false,
                    }}
                />
            </>
        )
    }

    function ClientInfo() {
        return (
            <>
                <div>

                    <div
                        style={{
                            display: "flex",
                            alignItems: "center",
                            justifyContent: "space-between",
                        }}
                    >
                        <div
                            style={{
                                display: "flex",
                                alignItems: "center",
                                gap: 16,
                            }}
                        >
                            <img
                                alt="Client Image"
                                src={clientIcon}
                            ></img>
                            <Text
                                style={{
                                    fontSize: 20,
                                    fontWeight: 600,
                                    display: "flex",
                                    flexDirection: "column",
                                }}
                            >
                                <span>{order?.client?.name ?? "Sem Cliente"}</span>
                                <span
                                    style={{
                                        fontWeight: 400,
                                        fontSize: 16,
                                        color: "#000000",
                                        opacity: 0.4,
                                    }}
                                >
                                    ID Primavera:{" "}
                                    {order?.client?.erp_client_id ?? "n/a"}
                                </span>
                            </Text>
                        </div>
                    </div>
                </div>
            </>
        );
    }

    function Bottom() {
        return (
            <div style={{
                marginTop: 24,
                padding: 24,
                display: "flex",
                flexDirection: "row",
                justifyContent: "space-between",
                background: "#ffffff",
                gap: 32,
                borderRadius: 8
            }}>
                <div style={{ width: '100%', height: '100%' }}>
                    <ClientInfo />
                    <Form.Item
                        style={{ marginTop: 15 }}
                        label="Nº Requisição"
                        name="request"
                        rules={[
                            ({ getFieldValue }) => ({
                                validator: (_, value) => {
                                    if (withoutRequisition) {
                                        return Promise.resolve();
                                    } else {
                                        if (!value || !value.trim()) {
                                            return Promise.reject("Insira o Nº da requisição");
                                        }
                                        return Promise.resolve();
                                    }
                                },
                            }),
                        ]}
                    >
                        <Checkbox
                            checked={withoutRequisition}
                            onChange={handleCheckboxChange}
                        >
                            Sem Requisição
                        </Checkbox>
                        {
                            !withoutRequisition &&
                            <Input
                                style={{ marginTop: 8 }}
                                disabled={withoutRequisition}
                                maxLength={255}
                                placeholder="Insira o Nº da requisição"
                                defaultValue={withoutRequisition ? "Sem requisição" : requestValue}
                                onChange={(e) => debouncedOrderRequestNumber(e?.target?.value)}
                                showCount
                            />
                        }
                    </Form.Item>
                </div>
                <div style={{ width: '100%', height: '100%', alignSelf: 'end' }}>
                    <Form.Item
                        label="Notas para a Encomenda"
                        name="description"
                        rules={[
                            {
                                required: true,
                                message: "Insira as notas para a encomenda",
                            },
                            {
                                validator: (_, value) => {
                                    if (value.length && !value.trim()) {
                                        return Promise.reject(
                                            "Insira as notas para a encomenda"
                                        );
                                    }
                                    return Promise.resolve();
                                },
                            },
                        ]}
                    >
                        <TextArea
                            className={"typing"}
                            rows={4}
                            maxLength={1024}
                            showCount={true}
                        />
                    </Form.Item>
                </div>
                <OrderSummary
                    order={order}
                    orderTotalValue={orderTotalValue}
                    handleSubmitOrder={handleOpenSubmitOrderModal}
                />
            </div>
        )
    }

    return (
        <>
            {contextHolder}
            <div style={{ padding: 24 }}>
                <Form form={productsForm} layout="vertical">
                    <Top />
                    <Bottom />
                </Form>
                <ConfirmOrderModal
                    title="Resumo da encomenda"
                    description="Escolha uma das opções para continuar o processo da finalização da encomenda"
                    open={submitModal}
                    onConfirmAndInvoice={() => submitFinishOrder('delivering')}
                    sendToDraft={() => submitFinishOrder('draft')}
                    onCancel={() => setSubmitModal(false)}
                    confirmLoading={isLoadingMutation}
                />
            </div>
        </>
    );
}

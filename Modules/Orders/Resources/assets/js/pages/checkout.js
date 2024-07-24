import React, { useEffect, useState, useRef } from "react";
import {
    Table,
    Space,
    Avatar,
    Form,
    Radio,
    Input,
    Card,
    Typography,
    DatePicker,
    Select,
    Checkbox,
    Button,
    message,
    Badge,
} from "antd";
import { useQuery, useMutation } from "react-query";
import { getOrderData, updateOrder, getAllzones } from "../libs/apis";
import { convertCurrencyFormat, initOnKeyDown } from "../libs/utils";
import { TagOutlined, PlusOutlined, UserOutlined } from "@ant-design/icons";
import ClientBar from "../components/clientBar";
import PurchaseSummary from "../components/purchaseSummary";
import dayjs from "dayjs";
import "dayjs/locale/pt";

import clientIcon from "../../img/client-icon.svg";
import moment from "moment";

import { debounce } from "lodash";


const { TextArea } = Input;
const { Text } = Typography;

const deliveryPeriodOptions = [
    { value: "morning", label: "Manhã" },
    { value: "evening", label: "Tarde" },
];

const dateFormat = "DD/MM/YYYY";

const styleAddressRadio = {
    border: "2px solid #E8E8E8",
    background: "#FAFAFA",
    borderRadius: 10,
    padding: 16,
    marginBottom: 8,
    width: "100%",
};

const styleAddressRadioChecked = {
    border: "2px solid #FFC53D",
    background: "#FFFBE639",
    borderRadius: 10,
    padding: 16,
    marginBottom: 8,
    width: "100%",
};

const columns = [
    {
        width: 50,
        render: (_, record) => {
            return (
                <Space>
                    {record.images ? (
                        <Avatar
                            shape="square"
                            size="large"
                            src={record.images}
                        />
                    ) : (
                        <Avatar
                            shape="square"
                            size="large"
                            icon={<TagOutlined />}
                        />
                    )}
                </Space>
            );
        },
    },
    {
        title: "Descrição",
        dataIndex: "name",
        key: "name",
    },
    {
        title: "ID Primavera",
        dataIndex: "erp_product_id",
        key: "erp_product_id",
    },
    {
        title: "Quantidade",
        dataIndex: "quantity",
        key: "quantity",
        render: (_, record) => {
            return (
                <Space>
                    {
                        <span>
                            {record.quantity} {record.sale_unit}
                        </span>
                    }
                </Space>
            );
        },
    },
    {
        title: "Preço UN",
        dataIndex: "price",
        key: "price",
        width: 130,
        render: (_, record) => {
            return (
                <Space>
                    {
                        <span>
                            {convertCurrencyFormat(record.unit_price ?? 0)} /{" "}
                            {record.unit}
                        </span>
                    }
                </Space>
            );
        },
    },
    {
        title: "Desconto",
        dataIndex: "discounts",
        key: "discounts",
        render: (_, record) => {
            const totalDiscountValue = record.discount_value;

            return (
                <Space>
                    {<span>{convertCurrencyFormat(totalDiscountValue)}</span>}
                </Space>
            );
        },
    },
    {
        title: "Stock",
        dataIndex: "stock",
        key: "stock",
        width: 160,
        render: (_, record) => {
            const totalQuantity =
            record?.batches?.length > 0
                ? record?.batches?.reduce((total, batch) => {
                      const value = batch?.quantity ? batch?.quantity : 0;
    
                      return parseFloat((total + value).toFixed(2));
                  }, 0)
                : 0;

            return (
                <Space>
                    {totalQuantity > 0 ? (
                        <Badge
                            key="available"
                            color="green"
                            text={
                                <Text
                                    style={{ fontSize: 16, color: "#00000073" }}
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
                                    style={{ fontSize: 16, color: "#00000073" }}
                                >
                                    Sem Stock
                                </Text>
                            }
                        />
                    )}
                </Space>
            );
        },
    },
];

export default function Checkout() {
    const [orderId, setOrderId] = useState(null);
    const [order, setOrder] = useState(null);
    const [initialValuesFinishOrderForm, setInitialValuesFinishOrderForm] =
        useState({});
    const [submitFinishOrderForm] = Form.useForm();
    const [openClientBar, setOpenClientBar] = useState(false);
    const [selectedValue, setSelectedValue] = useState("");
    const [addresses, setAddresses] = useState("");
    const [currentZone, setCurrentZone] = useState("");
    const [zoneNotFound, setZoneNotFound] = useState(false);
    const [newZone, setNewZone] = useState("");
    const [zoneList, setZoneList] = useState([]);
    const [drawer, setDrawer] = useState(false);
    const [withoutRequisition, setWithoutRequisition] = useState(false);
    const [requestValue, setRequestValue] = useState('');

    const { data: zonesData } = useQuery(["allZones"], getAllzones, {
        refetchOnWindowFocus: false,
    });

    useEffect(() => {
        if (zonesData && zonesData.length) {
            const options = zonesData.map((zoneData) => ({
                id: zoneData.id,
                value: zoneData.description,
            }));

            setZoneList(options);
        }
    }, [zonesData]);

    const keysPress = {
        onEnter: () => {
            submitFinishOrderForm.submit();
        },
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

    const { data: dataOrderQuery, isSuccess: isSuccessQuery } = useQuery(
        ["orderData", orderId],
        async () => {
            if (!orderId) return null;
            return await getOrderData(orderId);
        },
        { refetchOnWindowFocus: false }
    );

    useEffect(() => {
        if (isSuccessQuery) {
            let initialZone = null;

            if (dataOrderQuery?.zone) {
                initialZone = {
                    id: dataOrderQuery?.zone.id,
                    value: dataOrderQuery?.zone.description,
                };
            } else if (dataOrderQuery?.client?.addresses[0]?.zone) {
                initialZone = {
                    id: dataOrderQuery?.client?.addresses[0]?.zone.id,
                    value: dataOrderQuery?.client?.addresses[0]?.zone
                        .description,
                };
            }

            let initial = {
                request: dataOrderQuery.request_number !== 'Sem requisição' ?  dataOrderQuery.request_number : '',
                priority: dataOrderQuery.priority ? true : false,
                notes: dataOrderQuery.description,
                address: dataOrderQuery.delivery_address,
                zona: initialZone,
                caller_phone: dataOrderQuery.caller_phone,
                delivery_period: dataOrderQuery.delivery_period,
                delivery_date: dataOrderQuery.delivery_date
                    ? dayjs(
                          moment(dataOrderQuery.delivery_date).format(
                              dateFormat
                          ),
                          "DD/MM/YYYY"
                      ).add(1, "hour")
                    : dayjs(moment().format(dateFormat), "DD/MM/YYYY").add(
                          1,
                          "hour"
                      ),
            };

            if(dataOrderQuery?.request_number){
                if(dataOrderQuery?.request_number !== 'Sem requisição'){
                    setWithoutRequisition(false);
                    setRequestValue(dataOrderQuery?.request_number);
                }else{
                    setWithoutRequisition(true);
                }
            }

            setInitialValuesFinishOrderForm(initial);

            const { client } = dataOrderQuery;

            if (client) {
                const addresses =
                    client?.addresses.length > 0 ? client?.addresses : [];

                setAddresses(addresses);

                const addressSelected = addresses.find((address) => {
                    return address.selected_delivery_address === true;
                });

                const addressClient = addressSelected ?? addresses[0];

                if (addressClient?.id) {
                    initial.addressId = addressClient.id ?? null;
                    initial.address = addressClient.id;
                }

                if (dataOrderQuery?.bms_client_address_id) {
                    initial.addressId =
                        dataOrderQuery.bms_client_address_id ?? null;
                    initial.address = dataOrderQuery.bms_client_address_id;
                }

                initial.notes = initial.notes ?? client?.notes;

                let clientAddressZone;
                if (addressClient?.zone?.id) {
                    clientAddressZone = {
                        id: addressClient.zone.id,
                        value: addressClient.zone.description,
                    };
                }

                if (initial.zona) {
                    if (
                        !zoneList.find((zona) => initial?.zona?.id === zona?.id)
                    ) {
                        if (clientAddressZone?.id != initial?.zona?.id) {
                            setZoneList([
                                ...zoneList,
                                clientAddressZone,
                                initial.zona,
                            ]);
                            setCurrentZone(initial?.zona?.id);
                        } else {
                            setZoneList([...zoneList, clientAddressZone]);
                            setCurrentZone(addressClient?.zone?.id);
                        }
                    }
                } else {
                    setZoneList([...zoneList, clientAddressZone]);
                    if (addressClient?.zone?.id) {
                        setCurrentZone(addressClient?.zone.id);
                    }
                }
            }

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
                        price: orderProduct.unit_price,
                        iva: orderProduct.bms_product.iva,
                        unit_price: orderProduct.unit_price,
                        discount_value: orderProduct.discount_value,
                        notes: orderProduct.notes,
                        stock: orderProduct?.bms_product?.current_stock,
                        erp_product_id:
                            orderProduct?.bms_product?.erp_product_id,
                    };
                }
            );

            setOrder({
                ...dataOrderQuery,
                products: productsOrder,
            });
        }
    }, [isSuccessQuery]);

    useEffect(() => {
        if (initialValuesFinishOrderForm) {
            submitFinishOrderForm.setFieldsValue(initialValuesFinishOrderForm);
        }
    }, [initialValuesFinishOrderForm, submitFinishOrderForm]);

    const showDrawer = () => setOpenClientBar(true);
    const onCloseClientBar = () => setOpenClientBar(false);

    const {
        data: dataMutation,
        isSuccess: isSuccessMutation,
        isError: isErrorMutation,
        mutate: mutateOrder,
    } = useMutation(["updateOrderMutation"], updateOrder);

    useEffect(() => {
        if (isSuccessMutation) {
            window.location.href =
                "/orders/newOrder/summary?order=" + orderId + "&success=true";
        }
    }, [isSuccessMutation]);

    useEffect(() => {
        if (isErrorMutation) {
            message.error("Erro ao atualizar o pedido");
        }
    }, [isErrorMutation]);

    const submitFinishOrder = (values) => {
        const orderData = {
            id: order?.id,
            products: order.products.map((product) => ({
                id: product.id,
                notes: product.notes,
            })),
        };

        if (zoneNotFound) {
            values.zona = { value: newZone };
        } else {
            let zoneExists = null;

            if (typeof values.zona == "number") {
                zoneExists = zoneList.find((zona) => values.zona === zona?.id);
            } else if (typeof values.zona == "object") {
                zoneExists = zoneList.find(
                    (zona) => values.zona.id === zona?.id
                );
            }

            values.zona = {
                value: zoneExists ? zoneExists.value : values.zona,
            };

            values.request = requestValue;
        }

        mutateOrder({
            orderId: orderId,
            data: { values: values, orderData: orderData },
        });
    };

    const handleRadioChange = (event) => {
        addresses.map((address) => {
            if (address.id === event.target.value && address?.zone?.id) {
                setInitialValuesFinishOrderForm({
                    ...initialValuesFinishOrderForm,
                    addressId: address.id,
                    zona: address.zone.description,
                });
                setCurrentZone(address.zone.id);
                setSelectedValue(event.target.value);
            }
        });
    };

    const handleRequestNumberChange = (value) => {
        setRequestValue(value);
    };

    const debouncedOrderRequestNumber = debounce(handleRequestNumberChange, 1000);

    const saveNotesToProduct = (e, key) => {
        const products = order?.products.map((product) => {
            if (product.id === key) {
                product.notes = e.target.value;
            }
            return product;
        });

        setOrder({ ...order, products: products });
    };

    const disabledDate = (current) => {
        return (
            current &&
            (current < moment().startOf("day") ||
                current > moment().add(5, "y"))
        );
    };

    function AddressForm({}) {
        return (
            <>
                {order?.client?.addresses.length > 0 ? (
                    <Form.Item
                        label="Endereços"
                        name="addressId"
                        rules={[
                            {
                                required: true,
                                message: "Selecione um endereço para entrega",
                            },
                        ]}
                    >
                        <Radio.Group
                            onChange={handleRadioChange}
                            value={selectedValue}
                            style={{ width: "100%" }}
                        >
                            {order?.client?.addresses.map((address, index) => (
                                <Radio
                                    key={index}
                                    value={address.id}
                                    style={
                                        selectedValue === address.id
                                            ? styleAddressRadioChecked
                                            : styleAddressRadio
                                    }
                                >
                                    {address.address}{" "}
                                    {address?.address2 &&
                                        `, ${address.address2}`}{" "}
                                    {address.postal_code_address}{" "}
                                    {address.postal_code}
                                </Radio>
                            ))}
                        </Radio.Group>
                    </Form.Item>
                ) : (
                    <Form.Item
                        label="Morada"
                        name="address"
                        rules={[
                            {
                                required: true,
                                message: "Insira uma morada para entrega",
                            },
                        ]}
                    >
                        <TextArea
                            placeholder="Morada para entrega"
                            rows={2}
                            maxLength={100}
                            showCount={true}
                        />
                    </Form.Item>
                )}
            </>
        );
    }

    const handleCheckboxChange = (e) => {
        setWithoutRequisition(e.target.checked);
        if (e.target.checked) {
            setRequestValue("Sem requisição");
        }else {
            setRequestValue("");
        }
    };

    function ClientInfo({}) {
        return (
            <>
                {order?.client ? (
                    <div>
                        <Card
                            bordered={false}
                            style={{ background: "#FAFAFA", marginBottom: 16 }}
                        >
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
                                        // style={{ cursor: "pointer" }}//This page does not have the right data to display this component
                                        // onClick={() => showDrawer()}
                                    ></img>
                                    <Text
                                        style={{
                                            fontSize: 20,
                                            fontWeight: 600,
                                            display: "flex",
                                            flexDirection: "column",
                                            // cursor: "pointer",
                                        }}
                                        // onClick={() => showDrawer()} //This page does not have the right data to display this component
                                    >
                                        <span>{order?.client?.name}</span>
                                        {order?.client?.erp_client_id && (
                                            <span
                                                style={{
                                                    fontWeight: 400,
                                                    fontSize: 16,
                                                    color: "#000000",
                                                    opacity: 0.4,
                                                }}
                                            >
                                                ID Primavera:{" "}
                                                {order?.client?.erp_client_id}
                                            </span>
                                        )}
                                    </Text>
                                </div>
                            </div>
                        </Card>
                        <ClientBar
                            open={openClientBar}
                            onClose={onCloseClientBar}
                            client={order?.client}
                        />
                    </div>
                ) : (
                    <div
                        style={{
                            display: "flex",
                            alignItems: "center",
                            gap: 16,
                            marginBottom: 24,
                        }}
                    >
                        {order?.caller_phone ? (
                            <div style={{ display: "flex", gap: 16 }}>
                                <img alt="Client Image" src={clientIcon} />
                                <Text
                                    style={{
                                        fontSize: 20,
                                        fontWeight: 600,
                                        cursor: "pointer",
                                    }}
                                >
                                    {order?.caller_phone}
                                </Text>
                            </div>
                        ) : (
                            <Form.Item
                                style={{ marginBottom: 0 }}
                                name="caller_phone"
                                label="Telefone do Cliente"
                                rules={[
                                    {
                                        required: true,
                                        message: "Insira o telefone do cliente",
                                    },
                                    {
                                        pattern: /^[0-9]*$/,
                                        message:
                                            "Apenas são permitidos números no contacto telefónico.",
                                    },
                                    {
                                        min: 9,
                                        max: 12,
                                        message:
                                            "O contacto telefónico deve conter 9 dígitos.",
                                    },
                                    {
                                        validator: (_, value) => {
                                            if (value.length === 9) {
                                                if (/^(\d)\1*$/.test(value)) {
                                                    return Promise.reject(
                                                        "O contacto telefónico é inválido."
                                                    );
                                                }
                                            }
                                            return Promise.resolve();
                                        },
                                    },
                                ]}
                            >
                                <div style={{ display: "flex", gap: 16 }}>
                                    <img alt="Client Image" src={clientIcon} />
                                    <Input
                                        placeholder="Telefone do cliente"
                                        size="large"
                                        maxLength={9}
                                        minLength={8}
                                    />
                                </div>
                            </Form.Item>
                        )}
                    </div>
                )}
            </>
        );
    }

    const handleDrawerButtonClick = () => {
        setDrawer(!drawer)
    }

    return (
        <div className="checkout__main">
            <div className="checkout__content">
                <Table
                    pagination={false}
                    rowKey="id"
                    dataSource={order?.products}
                    columns={columns}
                    expandable={{
                        expandedRowRender: (record) => {
                            return (
                                <Form initialValues={{ notes: record.notes }}>
                                    <Form.Item
                                        name="notes"
                                        style={{ margin: 0 }}
                                    >
                                        <TextArea
                                            size="large"
                                            placeholder="Observações"
                                            maxLength="255"
                                            showCount
                                            onKeyUp={(event) =>
                                                saveNotesToProduct(
                                                    event,
                                                    record.id
                                                )
                                            }
                                        />
                                    </Form.Item>
                                </Form>
                            );
                        },
                        expandRowByClick: true,
                        showExpandColumn: false,
                    }}
                />
            </div>
            <div className={`checkout__drawer ${drawer ? 'active' : ''}`}>
                <button type="button" className="checkout__drawer-button" onClick={handleDrawerButtonClick}><UserOutlined style={{ fontSize: '20px' }} /></button>
                <div className="checkout__drawer-scroll">
                    <Form
                        form={submitFinishOrderForm}
                        initialValues={initialValuesFinishOrderForm}
                        layout="vertical"
                        onFinish={submitFinishOrder}
                    >
                        <ClientInfo />
                        <AddressForm />
                        <Form.Item
                            name="zona"
                            label="Zona"
                            rules={[
                                {
                                    required: !zoneNotFound,
                                    message: "Selecione a zona",
                                },
                            ]}
                        >
                            <Select
                                placeholder="Selecione a zona"
                                disabled={zoneNotFound}
                                options={zoneList.filter((item) => {
                                    if (item && item?.value && item?.id) {
                                        return {
                                            label: item.value,
                                            value: item.id,
                                        };
                                    }
                                })}
                            ></Select>
                        </Form.Item>

                        <div style={{ display: "flex", flexDirection: "column" }}>

                        <Checkbox
                            checked={zoneNotFound}
                            onChange={(e) => setZoneNotFound(e.target.checked)}
                        >
                            Não encontrei a zona
                        </Checkbox>

                        {zoneNotFound && (
                            <Form.Item
                                style={{ marginTop: 15 }}
                                name="nova_zona"
                                label="Nova Zona"
                                rules={[
                                    {
                                        required: true,
                                        message: "Insira o valor da zona",
                                    },
                                    {
                                        validator: (_, value) => {
                                            if (
                                                value &&
                                                value.length &&
                                                !value.trim()
                                            ) {
                                                return Promise.reject(
                                                    "Insira o valor da zona"
                                                );
                                            }

                                            return Promise.resolve();
                                        },
                                    },
                                ]}
                            >
                                <Input
                                    value={newZone}
                                    onChange={(e) => setNewZone(e.target.value)}
                                    placeholder="Insira uma nova zona"
                                    maxLength={50}
                                    showCount={true}
                                />
                            </Form.Item>
                        )}

                        {
                            !withoutRequisition &&
                            <Form.Item
                                style={{ marginTop: 15, marginBottom: 0 }}
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
                                <Input
                                    disabled={withoutRequisition}
                                    maxLength={255}
                                    placeholder="Insira o Nº da requisição"
                                    defaultValue={withoutRequisition ? "Sem requisição" : requestValue}
                                    onChange={(e) => debouncedOrderRequestNumber(e?.target?.value)}
                                    showCount
                                />
                            </Form.Item>
                        }

                        <Checkbox
                            style={{ marginTop: 12, marginBottom: 12, marginInlineStart:0 }}
                            checked={withoutRequisition}
                            onChange={handleCheckboxChange}
                        >
                            Sem Requisição
                        </Checkbox>
                        </div>

                        <Form.Item
                            label="Notas para a Encomenda"
                            name="notes"
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
                        <label>Entrega</label>
                        <div style={{ display: "flex", gap: 16 }}>
                            <Form.Item
                                name="delivery_date"
                                style={{ width: "100%" }}
                                rules={[
                                    {
                                        required: true,
                                        message: "Insira a data de entrega",
                                    },
                                ]}
                            >
                                <DatePicker
                                    placeholder="Data"
                                    size="large"
                                    format={dateFormat}
                                    disabledDate={disabledDate}
                                    showToday={false}
                                    style={{ width: "100%" }}
                                />
                            </Form.Item>

                            <Form.Item
                                name="delivery_period"
                                style={{ width: "100%" }}
                                rules={[
                                    {
                                        required: true,
                                        message: "Selecione o período de entrega",
                                    },
                                ]}
                            >
                                <Select
                                    allowClear
                                    size="large"
                                    placeholder="Período"
                                    options={deliveryPeriodOptions}
                                    style={{ width: "100%" }}
                                />
                            </Form.Item>
                        </div>

                        <Form.Item
                            name="priority"
                            valuePropName="checked"
                            style={{
                                background: "#F5F5F5",
                                fontWeight: "bold",
                                paddingLeft: "1rem",
                            }}
                        >
                            <Checkbox>Encomenda Prioritária</Checkbox>
                        </Form.Item>

                        <PurchaseSummary products={order?.products} short={false} />

                        <Form.Item>
                            <Button
                                block
                                size="large"
                                type="primary"
                                htmlType="submit"
                                style={{ color: "#000" }}
                            >
                                Confirmar encomenda
                            </Button>
                        </Form.Item>
                    </Form>
                </div>
            </div>
        </div>
    );
}

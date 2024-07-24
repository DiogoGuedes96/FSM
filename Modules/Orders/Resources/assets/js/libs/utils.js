import moment from "moment";

const STATUS_PENDING = "pending";
const STATUS_PARTIALLY_SHIPPED = "partially_shipped";
const STATUS_IN_PREPARATION = "preparing";
const STATUS_IN_DELIVERY = "delivering";
const STATUS_COMPLETED = "completed";
const STATUS_CANCELED = "canceled";
const STATUS_DRAFT = "draft";

const LABEL_STATUS_PENDING = "Pendente";
const LABEL_STATUS_PARTIALLY_SHIPPED = "Parcialmente expedida";
const LABEL_STATUS_IN_PREPARATION = "Preparação";
const LABEL_STATUS_IN_DELIVERY = "Distribuição";
const LABEL_STATUS_COMPLETED = "Concluida";
const LABEL_STATUS_CANCELED = "Cancelada";
const LABEL_STATUS_DRAFT = "Rascunho";

const COLOR_STATUS_PENDING = "#FAAD14";
const COLOR_STATUS_PARTIALLY_SHIPPED = "#FAAD14";
const COLOR_STATUS_IN_PREPARATION = "#237804";
const COLOR_STATUS_IN_DELIVERY = "#000000";
const COLOR_STATUS_COMPLETED = "#1890FF";
const COLOR_STATUS_CANCELED = "#F5222D";
const COLOR_STATUS_DRAFT = "#707F8D";
const COLOR_STATUS_DIRECT_SALE = "#EB2F96";

const getColorStatus = (status, isDirectSale = false) => {
    switch (status) {
        case STATUS_PENDING:
            return COLOR_STATUS_PENDING;
        case STATUS_PARTIALLY_SHIPPED:
            return COLOR_STATUS_PARTIALLY_SHIPPED;
        case STATUS_IN_PREPARATION:
            return COLOR_STATUS_IN_PREPARATION;
        case STATUS_IN_DELIVERY:
            return COLOR_STATUS_IN_DELIVERY;
        case STATUS_COMPLETED:
            return COLOR_STATUS_COMPLETED;
        case STATUS_CANCELED:
            return COLOR_STATUS_CANCELED;
        case STATUS_DRAFT:
            if (isDirectSale) {
                return COLOR_STATUS_DIRECT_SALE;
            }
            return COLOR_STATUS_DRAFT;
        default:
            return "#000000";
    }
};

const getLabelStatus = (status) => {
    switch (status) {
        case STATUS_PENDING:
            return LABEL_STATUS_PENDING;
        case STATUS_PARTIALLY_SHIPPED:
            return LABEL_STATUS_PARTIALLY_SHIPPED;
        case STATUS_IN_PREPARATION:
            return LABEL_STATUS_IN_PREPARATION;
        case STATUS_IN_DELIVERY:
            return LABEL_STATUS_IN_DELIVERY;
        case STATUS_COMPLETED:
            return LABEL_STATUS_COMPLETED;
        case STATUS_CANCELED:
            return LABEL_STATUS_CANCELED;
        case STATUS_DRAFT:
            return LABEL_STATUS_DRAFT;
        default:
            return "";
    }
};

const convertCurrencyFormat = (price) => {
    return convertToTwoDecimalPlaces(price).toLocaleString("pt-PT", {
        style: "currency",
        currency: "EUR",
        minimumFractionDigits: 2,
    });
};

const convertToTwoDecimalPlaces = (price) => {
    return parseFloat(price.toFixed(2));
};

const formatOrdersToSideList = (data) => {
    return data.map((item) => {
        const products =
            item.order_products.length > 0
                ? item.order_products.slice(0, 3)
                : [];

        return {
            key: item.id,
            id: item.id,
            status: item.status,
            isDirectSale: item.isDirectSale,
            priority: item.priority ?? null,
            delivery_date: item.delivery_date ?? null,
            delivery_period: item.delivery_period ?? null,
            zone: item?.zone?.description ?? null, //TODO add zone label later
            client_name: item?.client?.name ?? null,
            caller_phone:
                item?.caller_phone ??
                item?.client?.phone_1 ??
                item?.client?.phone_2 ??
                item?.client?.phone_3 ??
                item?.client?.phone_4 ??
                item?.client?.phone_5 ??
                null,
            products: products.map((product) => ({
                key: product.id,
                id: product.id,
                product: product.name,
                quantity: product.quantity,
                sale_unit: product.sale_unit,
            })),
        };
    });
};

const formatOrderToFullList = (orders) => {
    return orders.map((order, index) => {
        const {
            id,
            client,
            erp_invoice_id,
            caller_phone,
            delivery_address,
            primavera_invoices,
            status,
            delivery_date,
        } = order;

        const address =
            client?.addresses?.find(
                (address) => address.selected_delivery_address === true
            ) ?? client?.addresses?.length > 0
                ? client?.addresses[0]
                : null;

        const invoiceType =
            primavera_invoices?.childrens?.length > 0
                ? primavera_invoices.childrens[0].doc_type
                : primavera_invoices?.doc_type;

        const invoiceId = primavera_invoices?.id
            ? primavera_invoices?.childrens?.length > 0
                ? formatInvoiceName(primavera_invoices.childrens[0])
                : formatInvoiceName(primavera_invoices)
            : null;

        return {
            id: id,
            key: index,
            status: status,
            // invoice_id: erp_invoice_id,
            client: client?.name ?? null,
            nif: client?.tax_number ?? null,
            invoice_type: invoiceType ?? null,
            invoice_id: invoiceId,
            caller_phone:
                caller_phone ??
                client?.phone_1 ??
                client?.phone_2 ??
                client?.phone_3 ??
                client?.phone_4 ??
                client?.phone_5,
            order_address: delivery_address,
            client_address: address,
            delivery_date: delivery_date,
        };
    });
};

const formatInvoiceName = (invoice) => {
    const { doc_type, doc_series, number } = invoice;

    let formattedInvoiceName = doc_type;
    if (doc_series) {
        formattedInvoiceName += ` ${doc_series}`;
        if (number) {
            formattedInvoiceName += `/${number}`;
        }
    } else if (number) {
        formattedInvoiceName += ` ${number}`;
    }

    return formattedInvoiceName;
};

const formatOrderToOpenDetails = (order) => {
    const {
        id,
        client,
        created_at,
        caller_phone,
        delivery_address,
        writen_date,
        delivery_date,
        request_number,
        order_writer,
        order_preparer,
        delivery_period,
        priority,
        order_products,
        status,
        parent_order,
        description,
        zone,
        isDirectSale,
    } = order;

    const address =
        client?.addresses.find(
            (address) => address.selected_delivery_address === true
        ) ?? client?.addresses.length > 0
            ? client?.addresses[0]
            : null;

    const clientData = client
        ? {
              id: client.id,
              name: client.name,
              phone:
                  client.phone_1 ??
                  client.phone_2 ??
                  client.phone_3 ??
                  client.phone_4 ??
                  client.phone_5,
              primaveraId: client.erp_client_id,
              address: address,
              notes: client.notes,
          }
        : null;

    const products = order_products.map((product) => ({
        id: product.id,
        bms_product: product.bms_product,
        product: product.name,
        quantity: product.quantity,
        unit_price: product.unit_price,
        sale_unit: product.sale_unit,
        sale_price: product.sale_price,
        discount: product.discount_value,
        iva: product.iva ?? 0,
        total: product.total_liquid_price,
        batches:
            product?.bms_product?.batches.map((batches, index) => {
                return {
                    key: batches?.id,
                    value: batches?.id,
                    label: batches?.description,
                    quantity: batches?.quantity,
                };
            }) ?? [],
        unit: product.unit,
        unavailability: product.unavailability,
        notes: product.notes,
        volume: product.volume,
        erp_product_id: product.bms_product.erp_product_id,
        conversion: product.conversion,
        image:
            product.bms_product.images.length > 0
                ? product.bms_product.images[0].image_url
                : null,
        order_product_batch: product?.product_batch?.description ?? null,
    }));

    return {
        id,
        created_at,
        caller_phone,
        delivery_address,
        writen_date,
        delivery_date,
        delivery_period,
        request_number,
        order_writer,
        order_preparer,
        priority,
        products,
        status,
        parent_order,
        description,
        zona: zone?.description ?? null,
        client: clientData,
        isDirectSale,
    };
};

const getUnavailableProducts = (products, orderProducts) => {
    return products.reduce((filteredProducts, product) => {
        const productOrder = orderProducts.find(
            (productOrder) => productOrder.id == product.id
        );

        if (productOrder && product.unavailability) {
            filteredProducts.push({
                product: productOrder.product,
                quantity: productOrder.quantity,
                unit: productOrder.sale_unit ?? productOrder.unit,
                unavailability: product.unavailability,
                id: product.id,
            });
        }

        return filteredProducts;
    }, []);
};

const getAvailableProducts = (products, orderProducts) => {
    return products.reduce((filteredProducts, product) => {
        const productOrder = orderProducts.find(
            (productOrder) => productOrder.id == product.id
        );

        if (productOrder && !product.unavailability) {
            filteredProducts.push({
                product: productOrder.product,
                quantity: product.quantity,
                unit: productOrder.sale_unit ?? productOrder.unit,
                unavailability: product.unavailability,
                id: product.id,
            });
        }

        return filteredProducts;
    }, []);
};

const initOnKeyDown = (keysPress) => {
    const keyDownHandler = (event) => {
        if (
            !document.activeElement.classList.contains("typing") &&
            !document.activeElement.parentElement.classList.contains("typing")
        ) {
            if (keysPress[`on${event.key}`]) {
                event.preventDefault();
                keysPress[`on${event.key}`]();
            }
        }
    };

    document.addEventListener("keydown", keyDownHandler);

    return () => {
        document.removeEventListener("keydown", keyDownHandler);
    };
};

const formatDatePt = (date) => {
    const newDate = moment(date).format("DD/MM/YYYY") ?? "";

    return newDate;
};

const checkTypePrice = (typePrice) => {
    switch (typePrice) {
        case 0:
            return "pvp_1";
        case 1:
            return "pvp_2";
        case 2:
            return "pvp_3";
        case 3:
            return "pvp_4";
        case 4:
            return "pvp_5";
        case 5:
            return "pvp_6";
        default:
            return "pvp_1";
    }
};

const calculateOrderTotalValue = (orderProducts) => {
    let totalPrice = 0;

    orderProducts?.forEach((product) => {
        totalPrice += calculateProductFinalPrice(product);
    });

    if (isNaN(totalPrice)) {
        totalPrice = 0;
    }

    return parseFloat(totalPrice.toFixed(2));
};

const calculateProductFinalPrice = (product) => {
    const productSalePrice = parseFloat(product?.sale_price);
    const conversion = parseFloat(product?.conversion);

    const productPrice = productSalePrice * conversion;

    if (isNaN(productPrice)) {
        productPrice = "0";
    }

    return parseFloat(productPrice.toFixed(2));
};

export {
    formatDatePt,
    convertCurrencyFormat,
    convertToTwoDecimalPlaces,
    formatOrderToOpenDetails,
    formatOrdersToSideList,
    getAvailableProducts,
    getUnavailableProducts,
    formatOrderToFullList,
    STATUS_DRAFT,
    STATUS_PENDING,
    STATUS_PARTIALLY_SHIPPED,
    STATUS_IN_PREPARATION,
    STATUS_IN_DELIVERY,
    STATUS_COMPLETED,
    STATUS_CANCELED,
    getColorStatus,
    getLabelStatus,
    initOnKeyDown,
    checkTypePrice,
    calculateOrderTotalValue,
    calculateProductFinalPrice,
};

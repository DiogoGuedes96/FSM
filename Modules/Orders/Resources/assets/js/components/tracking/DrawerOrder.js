import { Divider, Drawer, Form, Spin, message } from "antd";
import React, { useEffect, useState } from "react";
import OrdersDetailHeader from "./OrderDetailHeaderj";
import OrdersDetailInfo from "./OrderDetailInfos";
import TableProductsGeneric from "./TableProductsGeneric";
import ModalDuplicateOrder from "./ModalDuplicateOrder";
import { getOrderData, getPrintInvoicePdf, duplicateOrder } from "../../libs/apis";
import { QueryClient, useMutation, useQuery } from "react-query";
import { formatOrderToOpenDetails } from "../../libs/utils";

export default function DrawerOrder({ order, close }) {
    const [formOrder] = Form.useForm();
    const [orderDetails, setOrderDetails] = useState(null)
    const [open, setOpen] = useState(false)
    const [loading, setLoading] = useState(true)
    const queryClient = new QueryClient();

    const { data, isSuccess } = useQuery(['get-order-details', order],
        () => getOrderData(order),
        {
            refetchOnWindowFocus: false
        }
    )

    const [isModalDuplicateVisible, setIsModalDuplicateVisible] = useState(false);

    const { mutate: mutationPrint } =
        useMutation(['get-order-print-invoice', order], getPrintInvoicePdf)

    useEffect(() => {
        if (isSuccess) {
            const order = formatOrderToOpenDetails(data);

            setOrderDetails(order)
            setLoading(false)
            setOpen(true)
        }
    }, [isSuccess])

    useEffect(() => {
        if (order) {
            queryClient.invalidateQueries(['get-order-details', order]);
        }
    }, [order])

    const onPrint = () => {
        mutationPrint(order);
    }

    const onDuplicateOrder = () => {
        setIsModalDuplicateVisible(true);
    }

    const handleModalDuplicateCancel = () => {
        setIsModalDuplicateVisible(false);
    };

    const submitDuplicateOrder = (values) => {
        duplicateOrder({
            orderId: orderDetails.id,
            data: values
        })
        .then(response => window.location.href = `/orders/newOrder?order=${response.order.id}`)
        .catch(error => {
            if (error.response.status != 422) {
                message.error('Erro ao duplicar encomenda!')
            }
        });
    };

    return (
        <Drawer
            width={1024}
            onClose={() => {
                setOpen(false)
                setOrderDetails(null)
                close()
            }}
            open={open}
        >
            {loading
                ? <div className="container-spin"><Spin size="large" /></div>
                : <>
                    <OrdersDetailHeader order={orderDetails} firstButton={onDuplicateOrder} secondButton={onPrint} detail={true} />
                    <OrdersDetailInfo order={orderDetails} form={formOrder} />

                    {isModalDuplicateVisible && (
                        <ModalDuplicateOrder
                            open={isModalDuplicateVisible}
                            onCancel={handleModalDuplicateCancel}
                            order={orderDetails}
                            duplicateOrder={(values) => submitDuplicateOrder(values) }
                        />
                    )}

                    <Divider />
                    <TableProductsGeneric products={orderDetails.products.map(p => ({ ...p, key: p.id }))} form={formOrder} />
                </>
            }
        </Drawer>
    )
}

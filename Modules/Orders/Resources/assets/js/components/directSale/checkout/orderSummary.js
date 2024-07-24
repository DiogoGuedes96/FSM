import React from "react";
import {
    Typography,
    Button,
} from "antd";

const { Text, Title } = Typography; 

export default function OrderSummary({order, orderTotalValue, handleSubmitOrder}) {

    return (
        <>
            <div style={{ width: '100%', display: 'flex', flexDirection: 'column', justifyContent: 'space-between', paddingBottom: 12 }}>
                <div>
                    <div style={{ display: 'flex', flexDirection: 'row', justifyContent: 'space-between', width: '100%' }} >
                        <Text style={{ margin: 0 }} >Resumo da compra</Text>
                        <Text style={{ margin: 0, fontWeight: 400, color: '#595959' }}>{order?.order_products?.length ?? 0} {order?.order_products && order?.order_products?.length === 1 ? 'artigo' : 'artigos'}</Text>
                    </div>
                    <div style={{ display: 'flex', flexDirection: 'row', justifyContent: 'space-between', width: '100%', marginTop: 12 }} >
                        <Title level={5} style={{ margin: 0 }} >Preço Total</Title>
                        <Title level={5} style={{ margin: 0, fontWeight: 400, color: '#595959' }}>{orderTotalValue + ' €'} </Title>
                    </div>
                </div>
                <Button
                    type="primary"
                    block
                    size="large"
                    onClick={() => handleSubmitOrder()}
                >
                    Finalizar Encomenda
                </Button>
            </div>
        </>
    );
}

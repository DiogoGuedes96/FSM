import React from "react";
import { Typography } from 'antd';

const { Title } = Typography;

export default function PurchaseSummary({ products }) {
    const titleStyle = {
        margin: 0,
    }

    return (
        <div
            style={{
                display: 'flex',
                alignItems: 'flex-start',
                flexDirection: 'column',
                margin: 16,
            }}
        >
            <div style={{ display: 'flex', justifyContent: 'space-between', width: '100%', marginBottom: 8 }}>
                <Title level={5} style={titleStyle} >Resumo da compra</Title>
                <Title level={5} style={{ ...titleStyle, fontWeight: 400, color: '#595959' }}>{products?.length ?? 0} {products && products.length === 1 ? 'artigo' : 'artigos'}</Title>
            </div>
        </div>
    )
}
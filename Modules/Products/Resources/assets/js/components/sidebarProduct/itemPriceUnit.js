import React from "react";
import { Form, Input, InputNumber, Typography } from 'antd';

function ItemPriceUnit({ index = 0, defaultPrice = false }) {
    const formatPrice = (price) => {
        return Math.round(price * 100) / 100

    }

    const parseCurrency = (value) => {
        const parsedValue = parseFloat(value.replace(/[^\d.-]/g, ''));

        if (isNaN(parsedValue)) {
            return null;
        }

        return parsedValue;
    };

    return (
        <React.Fragment key={index}>
            <div style={{ display: "flex", width: '100%' }}>
                <Form.Item 
                    label="Unidade" 
                    name={`unit-${index}`} 
                    style={{ width: '70%', marginRight: 8 }} 
                    rules={[{ 
                        required: true,
                        message: `O campo unidade é obrigatório` 
                    }]}>
                    <Input
                        disabled={defaultPrice}
                        style={{ width: '100%' }} />
                </Form.Item>
                <Form.Item 
                    label="Preço" 
                    name={`price-${index}`} 
                    rules={[{ 
                        required: true, 
                        message: `O campo preço é obrigatório` 
                    }]}>
                    <InputNumber
                        disabled={defaultPrice}
                        prefix="EUR"
                        style={{ width: '100%' }}
                        formatter={formatPrice}
                        parser={parseCurrency} />
                </Form.Item>
                <Form.Item 
                    name={`default-${index}`}>
                    <Input
                        type="hidden"
                        disabled={defaultPrice} />
                </Form.Item>
            </div>
        </React.Fragment>
    );
}
export default ItemPriceUnit;
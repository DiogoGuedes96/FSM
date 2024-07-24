import { Typography, List, Row, Col } from "antd";
import React from "react";

const { Title, Text } = Typography;

export default function ModalTrackingList({ title, products }) {
    return (
        <>
            <Title level={5}>{title}</Title>
            <List
                size="small"
                style={{ backgroundColor: '#FAFAFA' }}
                dataSource={products}
                renderItem={(item) => <List.Item>
                    <Row style={{
                        display: 'flex',
                        justifyContent: 'space-between',
                        alignItems: 'center',
                        width: '100%'
                    }}>
                        <Col span={18}>
                            <Text>{item.product}</Text>
                        </Col>
                        <Col span={4} style={{ textAlign: 'end' }}>
                            <Text>{item.quantity} {item.unit}</Text>
                        </Col>
                    </Row>
                </List.Item>}
            />
        </>
    );
};

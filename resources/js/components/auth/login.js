import React from 'react';
import { createRoot } from 'react-dom/client';
import { BrowserRouter } from "react-router-dom";
import { ConfigProvider } from 'antd';
import { Col, Row } from 'antd';

import Left from "./left";
import Right from "./right";

export default function Login() {
    return (
        <Row align='middle'>
            <Col id='login-left' span={24} lg={{span: 14}}>
                <Left />
            </Col>
            <Col id='login-right' span={0} lg={{span: 10}}>
                <Right />
            </Col>
        </Row>
    );
}

if (document.getElementById('login')) {
    createRoot(document.getElementById('login')).render(
        <BrowserRouter>
            <ConfigProvider
                theme={{
                    token: {
                    colorPrimary: '#FAAD14',
                    },
                }}
            >
            <Login />
            </ConfigProvider>
        </BrowserRouter>
    );
}
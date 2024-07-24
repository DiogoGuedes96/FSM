import React from 'react';
import { Layout as LayoutAnt, theme } from 'antd';
const { Content } = LayoutAnt;

export default function ContentCustom({content}) {
    const {
        token: { colorBgContainer },
    } = theme.useToken();

    return (
        <Content
            style={{
                minHeight: 280,
                background: '#F5F5F5',
            }}
            >
            {content}
        </Content>
    )
}
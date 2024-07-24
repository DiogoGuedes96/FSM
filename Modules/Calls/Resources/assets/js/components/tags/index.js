import { Tag } from "antd";
import React from "react";

export default function Tags({tag}) {

    let color = '#389E0D'
    switch (tag) {
        case 'pendente':
            color = '#FFC53D'
            break;
        case 'concluida':
            color = '#389E0D'
            break;
        case 'negada':
            color = '#F5222D'
            break;
        default:
            color = '#389E0D'
            break;
    }

    return (
        <Tag color={color} key={tag}>
            {tag.toUpperCase()}
        </Tag>
    );
}
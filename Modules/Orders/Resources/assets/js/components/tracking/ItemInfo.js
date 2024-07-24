import { Typography } from "antd";
import React from "react";

const { Text } = Typography;

export default function ItemInfo({ title, value, span = 12, style = { width: '100%' } }) {
    return (
        <div span={span} style={style}>
           <Text strong>{title}:</Text> <Text>{value}</Text>
        </div>
    );
}
import React, { useState } from "react";
import { Input, Typography } from "antd";

const { TextArea } = Input;
const { Text } = Typography;
export default function ClientNotes({ notes = null, handleInputOrderNotes }) {
    const [clientNotes, setClientNotes] = useState(notes);

    return (
        <div style={{ margin: "16px 16px" }}>
            <Text style={{ fontSize: 16, fontWeight: 600 }}>
                Notas de Encomenda
            </Text>
            <TextArea
                maxLength={1024}
                showCount
                className="typing"
                defaultValue={clientNotes}
                style={{ marginTop: 16 }}
                rows={6}
                placeholder="Adicione uma nota para a encomenda"
                onChange={(event) => handleInputOrderNotes(event.target.value)}
            />
        </div>
    );
}

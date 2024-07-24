import { Modal, Button } from "antd";
import React from "react";

export default function ConfirmOrderModal({
    title,
    description,
    open,
    onConfirmAndInvoice,
    sendToDraft,
    onCancel,
    confirmLoading,
}) {
    return (
        <>
            {open && (
                <Modal
                    title={title}
                    open={open}
                    onCancel={onCancel}
                    footer={
                        <div
                            style={{
                                display: "flex",
                                flexDirection: "column",
                                gap: 8,
                            }}
                        >
                            <Button
                                onClick={onConfirmAndInvoice}
                                loading={confirmLoading}
                                size="large"
                                type="primary"
                            >
                                Finalizar e enviar para faturar
                            </Button>
                            <Button
                                onClick={sendToDraft}
                                loading={confirmLoading}
                                type="default"
                                style={{ marginInlineStart: 0 }}
                                size="large"
                            >
                                Enviar para rascunho
                            </Button>
                            <Button
                                style={{
                                    marginInlineStart: 0,
                                    color: '#FAAD14'
                                }}
                                onClick={onCancel}
                                loading={confirmLoading}
                                size="large"
                                type="secondary"
                            >
                                Cancelar
                            </Button>
                        </div>
                    }
                >
                {description}
                </Modal>
            )}
        </>
    );
}

import { Modal, Button } from "antd";
import React from "react";

export default function ModalTracking({
    title,
    open,
    onConfirm,
    onConfirmModalAndPrint,
    secundaryBtn,
    onCancel,
    confirmLoading,
    data,
    okText,
    okSecondText = false,
    cancelText,
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
                                flexDirection: "column-reverse",
                                gap: 8,
                            }}
                        >
                            <Button
                                onClick={secundaryBtn ?? onCancel}
                                loading={confirmLoading}
                                size="large"
                            >
                                {cancelText}
                            </Button>
                            <Button
                                style={{ marginInlineStart: 0 }}
                                type={!okSecondText ? "primary" : "default"}
                                onClick={onConfirm}
                                loading={confirmLoading}
                                size="large"
                            >
                                {okText}
                            </Button>
                            {okSecondText && (
                                <Button
                                    style={{
                                        marginInlineStart: 0,
                                        color: "#000",
                                    }}
                                    type="primary"
                                    onClick={onConfirmModalAndPrint}
                                    loading={confirmLoading}
                                    size="large"
                                >
                                    {okSecondText}
                                </Button>
                            )}
                        </div>
                    }
                >
                    {data}
                </Modal>
            )}
        </>
    );
}

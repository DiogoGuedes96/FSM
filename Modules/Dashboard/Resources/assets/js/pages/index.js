import React from "react";
import { createRoot } from "react-dom/client";
import { ConfigProvider } from "antd";
import { QueryClient, QueryClientProvider } from "react-query";
import Layout from "../components/layout";
import { BrowserRouter } from "react-router-dom";
import Dashboard from "./Dashboard";
import ptPT from 'antd/lib/locale/pt_PT';


const queryClient = new QueryClient();
const container = document.getElementById("dashboard");
const root = createRoot(container);

root.render(
    <QueryClientProvider client={queryClient}>
        <BrowserRouter>
            <ConfigProvider
                locale={ptPT}
                theme={{
                    token: {
                        colorPrimary: "#FAAD14",
                    },
                }}
            >
                <Layout header="Dashboard">
                    <Dashboard />
                </Layout>
            </ConfigProvider>
        </BrowserRouter>
    </QueryClientProvider>
);

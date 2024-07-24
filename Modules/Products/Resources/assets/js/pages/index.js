import React from "react";
import { createRoot } from 'react-dom/client';
import { ConfigProvider } from 'antd';
import { QueryClient, QueryClientProvider } from 'react-query'

import Layout from '../components/layout';
import ManagerProductsPage from "./ManagerProductsPage";
import SiderCustom from "../components/layout/Sidercustom";
import { Layout as LayoutAnt } from 'antd';
import ptPT from 'antd/lib/locale/pt_PT';


const queryClient = new QueryClient()

export default function Products() {
    return (
        <Layout header="Produtos">
            <ManagerProductsPage />
        </Layout>
    );
}

if (document.getElementById('products')) {
    const container = document.getElementById('products');
    const root = createRoot(container);
    root.render(
        <QueryClientProvider client={queryClient}>
            <ConfigProvider
                locale={ptPT}
                theme={{
                    token: {
                        colorPrimary: '#FAAD14'
                    },
                }}
            >
                <Products />
            </ConfigProvider>
        </QueryClientProvider>
    );
}
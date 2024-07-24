import React from "react";
import { createRoot } from 'react-dom/client';
import { ConfigProvider } from 'antd';
import { QueryClient, QueryClientProvider } from 'react-query'
import Layout from '../components/layout';
import SiderCustom from "../components/layout/Sidercustom";
import { Layout as LayoutAnt } from 'antd';
import AsteriskCalls from './AsteriskCalls'
import ptPT from 'antd/lib/locale/pt_PT';

const queryClient = new QueryClient()

export default function Calls() {
    return (
        <Layout header="Central Telefónica">
            <AsteriskCalls />
        </Layout>
    );
}

if (document.getElementById('calls')) {
    const container = document.getElementById('calls');
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
                <Calls />
            </ConfigProvider>
        </QueryClientProvider>
    );
}
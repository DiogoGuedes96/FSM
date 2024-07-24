import React from "react";
import { createRoot } from 'react-dom/client';
import { ConfigProvider } from 'antd';
import { QueryClient, QueryClientProvider } from 'react-query'
import Layout from '../components/layout';
import NewOrder from './NewOrder.js'
import NewOrderSummary from './newOrderSummary.js'
import Checkout from './checkout.js'
import { BrowserRouter } from "react-router-dom";
import Tracking from "./tracking";
import SiderCustom from "../components/layout/Sidercustom";
import { Layout as LayoutAnt } from 'antd';
import ptPT from 'antd/lib/locale/pt_PT';
import DirectSale from "./directSale.js";
import DirectSaleCheckout from "./directSaleCheckout.js";
import DirectSaleSummary from "./directSaleSummary.js";

let pageRender = null;
const pages = ['orders', 'orders-summary', 'orders-checkout', 'orders-tracking', 'direct-sale', 'direct-sale-checkout', 'direct-sale-summary'];
pages.forEach((page) => {
    if (document.getElementById(page)) {
        pageRender = page;
    }
});

if (pageRender) {
    const queryClient = new QueryClient();
    const container = document.getElementById(pageRender);
    const root = createRoot(container);
    root.render(
        <QueryClientProvider client={queryClient}>
            <BrowserRouter>
                <ConfigProvider
                    locale={ptPT}
                    theme={{
                        token: {
                            colorPrimary: '#FAAD14'
                        },
                    }}
                >
                    {pageRender === 'orders' && <Layout header="Nova Encomenda"><NewOrder /></Layout>}
                    {pageRender === 'orders-summary' && <Layout header="Nova Encomenda"><NewOrderSummary /></Layout>}
                    {pageRender === 'orders-checkout' && <Layout header="Nova Encomenda"><Checkout /></Layout>}
                    {pageRender === 'direct-sale' && <Layout header="Venda Direta"><DirectSale /></Layout>}
                    {pageRender === 'direct-sale-checkout' && <Layout header="Venda Direta"><DirectSaleCheckout /></Layout>}
                    {pageRender === 'direct-sale-summary' && <Layout header="Venda Direta"><DirectSaleSummary /></Layout>}
                    {pageRender === 'orders-tracking' && <Layout backgroundColor="#F5F5F5" header="Acompanhamento de Encomendas"><Tracking /></Layout>}
                </ConfigProvider>
            </BrowserRouter>
        </QueryClientProvider>
    );
}

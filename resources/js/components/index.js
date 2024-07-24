import React from 'react';
import { createRoot } from 'react-dom/client';
import { BrowserRouter } from "react-router-dom";
import { ConfigProvider } from 'antd';
import { QueryClient, QueryClientProvider } from 'react-query'
import ptPT from 'antd/lib/locale/pt_PT';

import Layout from './layout';

const queryClient = new QueryClient()

if (document.getElementById('app')) {
    createRoot(document.getElementById('app')).render(
        <QueryClientProvider client={queryClient}>
            <BrowserRouter>
                <ConfigProvider
                    locale={ptPT}
                    theme={{
                        token: {
                            colorPrimary: '#FAAD14',
                        },
                    }}
                >

                    <Layout header="Homepage" />
                </ConfigProvider>
            </BrowserRouter>
        </QueryClientProvider>
    );
}
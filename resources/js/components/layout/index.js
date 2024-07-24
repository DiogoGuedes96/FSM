import React, { useEffect, useState } from 'react';
import HeaderCustom from './Headercustom'
import FooterCustom from './Footercustom'
import ContentCustom from './Contentcustom'
import { useQuery } from 'react-query';
import { getUser } from 'bmslibs/apis.js';
import SiderCustom from './Sidercustom';
import { Layout as LayoutAnt } from 'antd';

export default function Layout({ children, header }) {
    const [user, setUser] = useState(localStorage.getItem('user') ? JSON.parse(localStorage.getItem('user')) : null);

    const { data: dataUser, isSuccess: isSuccessDataUser, isError: isErrorDataUser } = useQuery(
        ['getUser'], getUser, {
        refetchOnWindowFocus: false,
        enabled: !user?.token || user.canAccess.length === 0,
    })

    const generateRandomToken = () => {
        const array = new Uint8Array(32);
        window.crypto.getRandomValues(array);

        let browserToken = '';
        for (let i = 0; i < array.length; i++) {
            browserToken += ('0' + array[i].toString(16)).slice(-2);
        }

        return browserToken;
    };

    useEffect(() => {
        if (isSuccessDataUser && dataUser?.user) {
            localStorage.setItem('user', JSON.stringify(dataUser.user))
            setUser(dataUser.user)
        }

        localStorage.getItem('browserToken') ?? localStorage.setItem('browserToken', generateRandomToken());
    }, [isSuccessDataUser, dataUser])

    useEffect(() => {
        if (isErrorDataUser) {
            localStorage.removeItem('user')
            localStorage.removeItem('browserToken')
            window.location.replace('/login')
        }
    }, [isErrorDataUser])

    return (
        <>
            {user &&
                <LayoutAnt style={{ minHeight: '100vh' }}>
                    <SiderCustom modules={user?.canAccess} user={user} />
                    <LayoutAnt>
                        <HeaderCustom title={header} user={user} />
                        <ContentCustom content={children} />
                        <FooterCustom />
                    </LayoutAnt>
                </LayoutAnt>
            }
        </>
    );
}
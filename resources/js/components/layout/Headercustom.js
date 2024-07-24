import React, { useEffect, useState } from 'react'
import { Layout as LayoutAnt, theme } from 'antd';
import { Typography, Button, Badge, message, Tooltip } from 'antd';
import { useQuery } from 'react-query'
import { LogoutOutlined, CalendarOutlined, ClockCircleOutlined } from '@ant-design/icons';

import { listCurrentMinute } from "bmslibs/apiSchedule";
import ModalAlertEvent from "./schedule/ModalAlertEvent";

const { Title, Text } = Typography;
const { Header } = LayoutAnt;

export default function HeaderCustom({ title, user }) {
    const [eventRemembers, setEventRemembers] = useState(null);
    const [haveRemembers, setHaveRemembers] = useState(false);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [remindersButtonPermissions, setRemindersButtonPermissions] = useState(false);
    
    useEffect(() => {
        const module = user?.canAccess?.find(module => module.module === 'scheduling');

        if (module){
            if (module.permissions) {
                const permissions = JSON.parse(module.permissions);

                if (permissions ){
                    if(permissions['accessRemindersBtn'] === false || permissions['accessRemindersBtn'] === undefined || permissions['accessRemindersBtn'] === null){
                        setRemindersButtonPermissions(false);
                    }else {
                        setRemindersButtonPermissions(true);
                    }
                };
            }
        }
    }, [user])

    const {
        token: { colorBgContainer },
    } = theme.useToken();

    const { data: listByMinuteData, isSuccess: isSuccessListByMinute } =
        useQuery("listCurrentMinute", () => listCurrentMinute(false), {
            refetchInterval: 60000,
            refetchOnWindowFocus: false,
            onSuccess: (listByMinuteData) => {
                if (listByMinuteData?.remembers?.length) {
                    handleIsSuccessListByMinute(listByMinuteData.remembers);
                }
            },
        });

    const handleReminderButton = () => {
        if (eventRemembers && eventRemembers?.length > 0) {
            setIsModalOpen(true);
        } else{
            setIsModalOpen(false);
            message.error('Não foram encontradas tarefas pendentes!');
        }
    }

    const handleIsSuccessListByMinute = (eventRemembersData) => {
        if (eventRemembersData?.length) {//If there is data form API
            if(eventRemembers?.length){ //There are already events, need to concatnate the two arrays
                const combinedReminders = [...eventRemembers, ...eventRemembersData];

                // Remove duplicates based on ID
                const uniqueReminders = Array.from(new Set(combinedReminders.map(remember => remember.id)))
                    .map(id => combinedReminders.find(remember => remember.id === id));
    
                setEventRemembers(uniqueReminders);
                setHaveRemembers(true);

            } else{ //There are no events, only need to set
                setEventRemembers(eventRemembersData);
                setHaveRemembers(true);
            }
        }
    }

    useEffect(() => {
        if (eventRemembers?.length) {            
            const unreadEventRemembers = eventRemembers.filter(item => item.read_at === null);

            if (unreadEventRemembers.length > 0 && !isModalOpen) {
                setIsModalOpen(true);
            }
        }
    }, [eventRemembers]);
    
    const closeModalAlertEvent = (lastOne = false) => {
        setIsModalOpen(false);

        if (lastOne){
            setHaveRemembers(false);
            setEventRemembers(null);
        }
    };

    const handleLogout = () => {
        localStorage.removeItem('browserToken');
        localStorage.removeItem('user');
    }
    
    return (
        <Header className='header-bar'>
            <Title style={{ marginTop: 0, marginBottom: 0 }} level={4}>{title}</Title>
            <div className='header-bar__options'>
                {remindersButtonPermissions && 
                <>
                    {haveRemembers ?
                        <>
                            <span style={{ marginRight: 16 }}>
                                <Badge
                                    count={
                                        <ClockCircleOutlined
                                            style={{
                                                color: '#f5222d',
                                            }}
                                        />
                                    }
                                >
                                    <Tooltip placement="bottom" title="Tarefas Pendentes">
                                        <Button size="large" onClick={() => handleReminderButton()}>
                                            <CalendarOutlined style={{ fontSize: 18 }} />
                                        </Button>
                                    </Tooltip>
                                </Badge>
                            </span>
                        </>
                        :
                        <>
                            <span style={{ marginRight: 16 }}>
                                <Tooltip placement="bottom" title="Tarefas Pendentes" >
                                    <Button size="large" onClick={() => handleReminderButton()}>
                                        <CalendarOutlined style={{ fontSize: 18 }} />
                                    </Button>
                                </Tooltip>
                            </span>
                        </>
                    }
                    </>
                }
                <div className='header-bar__profile'>
                    <Text style={{ paddingRight: 10 }}>{user?.name}</Text>
                    <a href="/logout" onClick={() => handleLogout()}>
                        <LogoutOutlined /> sair
                    </a>
                </div>
            </div>
            <ModalAlertEvent
                open={eventRemembers !== null && isModalOpen}
                event={eventRemembers}
                close={closeModalAlertEvent}
            />
        </Header>
    )
}
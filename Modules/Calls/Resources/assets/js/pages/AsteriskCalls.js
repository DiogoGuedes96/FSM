import React, { useState } from "react";
import { useQuery, useMutation } from "react-query";
import { Tabs, Badge, Typography } from 'antd';
import ActiveCalls from '../components/activeCalls'
import AllCalls from '../components/allCalls'
import MissedCalls from '../components/missedCalls'
import { getCountMissedCalls } from "../libs/apis";
import './base.scss'

const { Text } = Typography;

export default function AsteriskCalls() {
  const [coutMissedCalls, setCoutMissedCalls] = useState(0);
  const [activeTab, setActiveTab] = useState(1);

  useQuery(
      ["missed-calls-count-total"],
      () => getCountMissedCalls(),
      {
          onSuccess: (data) => {
            setCoutMissedCalls(data?.calls ?? 0);
          },
      }
  );

  const { mutate: mutateGetCountMissedCalls } = useMutation(["mutation-missed-calls-count-total"], getCountMissedCalls, {
    onSuccess: (data) => {
      setCoutMissedCalls(data?.calls ?? 0);
    },
  });

  const handleTabChange = (key) => {
    setActiveTab(key)
    mutateGetCountMissedCalls()
  }

  const tabsItems = [
    {
      key: '1',
      label: `Chamadas ativas`,
      children: 
        activeTab == 1 &&
          <ActiveCalls />,
    },
    {
      key: '2',
      label: `Histórico de chamadas`,
      children:
        activeTab == 2 && 
        <AllCalls />,
    },
    {
      key: '3',
      label:
        coutMissedCalls > 0 ? (
          <Text>
            Chamadas Perdidas
            <Badge
              style={{ margin: '0px 0px 12px 4px' }}
              color="red"
              count={coutMissedCalls}
            />
          </Text>
        ) : (
          `Chamadas Perdidas`
        ),
      children: 
        activeTab == 3 &&
          <MissedCalls setCoutMissedCalls={setCoutMissedCalls}/>
    },
  ];

  return (
    <div>
      <Tabs 
        className="calls__tabs"
        defaultActiveKey="1" 
        items={tabsItems}
        style={{zIndex: -1}}
        onChange={(key) => handleTabChange(key)}
      />
    </div>
  )
}
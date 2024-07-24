import React, { useEffect, useState } from "react";
import {
    Select,
    Table,
    DatePicker,
    Col,
    Row,
    Typography
} from "antd";
import "dayjs/locale/pt";

const { Text } = Typography;

export default function LastSalesBar({
    values = [],
    handleFilterDays,
    handleFilterDateRange,
}) {
    const [organizeDataTable, setOrganizeDataTable] = useState();

    const mainColunms = [
        {
            title: "Descrição",
            dataIndex: "name",
            key: "name",
            align:'left',
            showSorterTooltip: {
                title: "Clique para alterar a ordem",
            },
            sorter: (a, b) => a.name.localeCompare(b.name),
        },
        {
            title: "Encomendas",
            dataIndex: "bms_product_count",
            key: "bms_product_count",
            showSorterTooltip: {
                title: "Clique para alterar a ordem",
            },
            align:'right',
            sorter: (a, b) => a?.bms_product_count - b?.bms_product_count,
        },
        {
            title: "Total",
            key: "bms_product_total_quantity",
            align:'right',
            defaultSortOrder: 'descend',
            showSorterTooltip: {
                title: "Clique para alterar a ordem",
            },
            sorter: (a, b) => a?.bms_product_total_quantity - b?.bms_product_total_quantity,
            render: (record) => {
                return (
                  <Text>{ record?.bms_product_total_quantity + ' ' + record?.unit } </Text>
                );
            }
        },
    ];

    const expandedRowRender = (record) => {
        const columns = [
            {
                title: "Data",
                align:'left',
                dataIndex: "order_at",
                sorter: (a, b) => {
                    const dateA = new Date(a.order_at);
                    const dateB = new Date(b.order_at);

                    return dateA.getTime() - dateB.getTime();
                },
                showSorterTooltip: {
                    title: "Clique para alterar a ordem",
                },
                render: (record) => {
                    return (
                        <Text>{formatDate(record)}</Text>
                    );
                }
            },
            {
                title: "Quantidade",
                align: "right",
                sorter: (a, b) => a?.quantity - b?.quantity,
                showSorterTooltip: {
                    title: "Clique para alterar a ordem",
                },
                render: (record) => {
                    let conversion = record?.conversion ? record?.conversion : 0;
                    
                    return (
                      <Text>{ conversion + ' ' + record?.unit } </Text>
                    );
                }
            },
        ];

        const data = values.filter(item => item.bms_product === record.bms_product)
                           .map(info => ({
                               ...info
                           }));

        return <Table columns={columns} dataSource={data} pagination={false} />;
    };

    const selectDays = [
        {
            value: 7,
            label: "7 dias",
        },
        {
            value: 15,
            label: "15 dias",
        },
        {
            value: 30,
            label: "1 mês",
        },
    ];

    const formatDate = (dateString) => {
        const date = new Date(dateString);
        const day = date.getDate().toString().padStart(2, "0");
        const month = (date.getMonth() + 1).toString().padStart(2, "0");
        const year = date.getFullYear().toString();

        return `${day}/${month}/${year}`;
    };

    const countBMSProducts = ((array, bmsProduct) => {
        let count = 0;
        let totalQuantity = 0;

        array.forEach(obj => {
          if (obj.hasOwnProperty('bms_product') && obj['bms_product'] === bmsProduct) {
            
            if(obj?.conversion != null){
                totalQuantity += parseFloat(obj?.conversion);
            }
            
            count++;
          }
        });
        
        return [count, totalQuantity];
    });

    const hasDuplicate = (array, item) => {
        return array.some(existingItem => {
            return existingItem.bms_product === item.bms_product && existingItem.bms_product_count === item.bms_product_count;
        });
    };

    useEffect(() => {
        const getDataWithCount = values.map(item => {
            let bmsProduct = item?.bms_product;
            return {
                key: bmsProduct,
                unit: item?.unit,
                bms_product: bmsProduct,
                bms_product_count: countBMSProducts(values, bmsProduct)[0],
                bms_product_total_quantity: countBMSProducts(values, bmsProduct)[1],
                name: item?.name
            };
        });

        const organizeValues = [];

        getDataWithCount.map(item => {
            if (!hasDuplicate(organizeValues, item)) {
                organizeValues.push(item);
            }
        });

        setOrganizeDataTable(organizeValues);
    }, [values]);

    return ( 
        <div>
            <Row gutter={[16,16]} style={{ paddingBottom: 16 }}>
                <Col flex={2}>
                    <Select
                        style={{ width: "100%" }}
                        defaultValue="7"
                        size="large"
                        onChange={handleFilterDays}
                        options={selectDays}
                    />
                </Col>
                <Col flex={3}>
                    <DatePicker.RangePicker
                        style={{ width: "100%" }}
                        size="large"
                        format={"DD/MM/YYYY"}
                        onChange={handleFilterDateRange}
                    />
                </Col>
            </Row>
            <Table
                columns={mainColunms}
                expandable={{ expandedRowRender, defaultExpandedRowKeys: ['0'] }}
                dataSource={organizeDataTable}
                pagination={true}
            />
        </div>
    );
}

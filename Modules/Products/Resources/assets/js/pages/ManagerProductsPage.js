import React, { useEffect, useState } from "react";
import { Button, Table, Input, Space, Drawer, Avatar, Select } from "antd";
import SidebarProducts from "../components/sidebarProduct";
import {
    getProducts,
    getSyncBatchs,
    getSyncClients,
    getSyncProducts,
    getUnitsProducts,
} from "../libs/apis";
import { QueryClient, useQuery, useMutation } from "react-query";
import { UserOutlined } from "@ant-design/icons";
import { set } from "lodash";
import "./base.scss"

const { Search } = Input;

export default function ManagerProductsPage() {
    const [selected, setSelectedRows] = useState([]);
    const [loading, setLoading] = useState(false);
    const [search, setSearch] = useState("");
    const [searchLoading, setSearchLoading] = useState(false);
    const [open, setOpen] = useState(false);
    const [items, setItems] = useState([]);
    const [page, setPage] = useState(1);
    const [total, setTotal] = useState(1);
    const [perPage, setPerPage] = useState(20);
    const [filters, setFilters] = useState([]);
    const [itemSelected, setItemSelected] = useState(null);
    const [sortOrder, setSortOrder] = useState(null);
    const [sorter, setSorter] = useState(null);
    const [filterSelected, setFilterSelected] = useState(null);
    const [status, setStatus] = useState(null);
    const [reloadQuery, setReloadQuery] = useState(0);
    const [loadingProducts, setLoadingProducts] = useState(false);
    const [loadingBatchs, setLoadingBatchs] = useState(false);
    const [loadingClientes, setLoadingClientes] = useState(false);
    const queryClient = new QueryClient();

    const { data: dataProducts } = useQuery(
        [
            "products",
            page,
            perPage,
            search,
            sorter,
            filterSelected,
            status,
            reloadQuery,
        ],
        async () => {
            return await getProducts(
                page,
                perPage,
                search,
                sorter,
                filterSelected,
                status
            );
        }
    );

    const { data: dataUnitProducts } = useQuery(
        ["units-products", reloadQuery],
        getUnitsProducts,
        {
            refetchOnWindowFocus: false,
        }
    );

    const { mutate: mutateSycnProducts } = useMutation(
        ["sync-products"],
        getSyncProducts,
        {
            onSuccess: () => {
                setLoadingProducts(false);
            },
        }
    );

    const { mutate: mutateSycnBatchs } = useMutation(
        ["sync-batchs"],
        getSyncBatchs,
        {
            onSuccess: () => {
                setLoadingBatchs(false);
            },
        }
    );

    const { mutate: mutateSycnClients } = useMutation(
        ["sync-clients"],
        getSyncClients,
        {
            onSuccess: () => {
                setLoadingClientes(false);
            },
        }
    );

    const handleTableChange = (pagination, filters, sorter) => {
        if (filters?.unit?.length > 0) {
            setFilterSelected(filters.unit);
            queryClient.invalidateQueries("products");
            handleSorter(sorter);

            return;
        } else if (filters?.unit == null || filters?.unit?.length === 0) {
            setFilterSelected(null);
            queryClient.invalidateQueries("products");
        }
        handleSorter(sorter);
    };

    const handleSorter = (sorter) => {
        if (sorter.field) {
            const { field, order } = sorter;
            const sortDirection =
                order === "ascend"
                    ? "asc"
                    : order === "descend"
                    ? "desc"
                    : null;

            setSorter({ field, sortDirection });
            setSortOrder(order);
            queryClient.invalidateQueries("products");
            return;
        }
    };

    function handlePageChange(newPage) {
        setPage(newPage);
        queryClient.invalidateQueries("products");
    }

    function handleSizePageChange(current, size) {
        setPerPage(size);
        setPage(1);
        queryClient.invalidateQueries("products");
    }

    const onSearch = (value) => {
        if (value) {
            setSearchLoading(true);
            setSearch(value);
            setPage(1);
            queryClient.invalidateQueries("products");
        }
    };

    useEffect(() => {
        if (dataUnitProducts && dataUnitProducts.length > 0) {
            setFilters(
                dataUnitProducts.map((unit) => ({ text: unit, value: unit }))
            );
        }
    }, [dataUnitProducts]);

    useEffect(() => {
        if (dataProducts && dataProducts.data.length > 0) {
            setTotal(dataProducts.total);

            const newData = dataProducts.data.map((item, index) => {
                let batches = 0;
                if (item?.batches?.length > 0) {
                    batches = item.batches.reduce((acc, batch) => {
                        return acc + batch.quantity;
                    }, 0);
                }

                return {
                    key: index,
                    id: item.id,
                    name: item.name.trim(),
                    erp_product_id: item.erp_product_id,
                    unit: item.sell_unit,
                    pvp_1: isNumber(item.pvp_1) ? item.pvp_1 : 0,
                    images: item?.images.length
                        ? item.images[0].image_url
                        : null,
                    active: item.active,
                    current_stock: batches,
                };
            });

            setItems(newData);
            setSearchLoading(false);
        }

        if (dataProducts && dataProducts.data.length === 0) {
            setItems([]);
            setSearchLoading(false);
        }
    }, [dataProducts]);

    const columns = [
        {
            width: 50,
            render: (_, record) => {
                return (
                    <Space>
                        {record.images ? (
                            <Avatar
                                shape="square"
                                size="large"
                                src={record.images}
                            />
                        ) : (
                            <Avatar
                                shape="square"
                                size="large"
                                icon={<UserOutlined />}
                            />
                        )}
                    </Space>
                );
            },
        },
        {
            title: "Descrição",
            dataIndex: "name",
            sorter: true,
            showSorterTooltip: {
                title: "Clique para alterar a ordem",
            },
        },
        {
            title: "Artigo",
            dataIndex: "erp_product_id",
            sorter: true,
            showSorterTooltip: {
                title: "Clique para alterar a ordem",
            },
        },
        {
            title: "Unidade",
            dataIndex: "unit",
            filters: filters,
            render: (_, record) => {
                return (
                    <Space>
                        <span>{record?.unit}</span>
                    </Space>
                );
            },
        },
        {
            title: "Preço",
            dataIndex: "pvp_1",
            sorter: true,
            showSorterTooltip: {
                title: "Clique para alterar a ordem",
            },
            render: (_, record) => {
                return (
                    <Space>
                        <span>
                            {formatPrice(
                                record?.pvp_1 && isNumber(record.pvp_1)
                                    ? record.pvp_1
                                    : 0
                            )}
                        </span>
                    </Space>
                );
            },
        },
        {
            title: "Em stock",
            dataIndex: "current_stock",
            sorter: true,
            showSorterTooltip: {
                title: "Clique para alterar a ordem",
            },
            render: (_, record) => {
                return (
                    <Space>
                        <span>{record?.current_stock ? "Sim" : "Não"}</span>
                    </Space>
                );
            },
        },
        {
            title: "Status",
            dataIndex: "active",
            sorter: true,
            showSorterTooltip: {
                title: "Clique para alterar a ordem",
            },
            render: (_, record) => {
                return (
                    <Space>
                        <span>{record?.active == 1 ? "Ativo" : "Inativo"}</span>
                    </Space>
                );
            },
        },
        {
            title: "Ação",
            key: "action",
            render: (_, record) => (
                <Space>
                    <Button
                        onClick={() => editProduct(record.id, record)}
                        type="link"
                    >
                        Editar
                    </Button>
                </Space>
            ),
        },
    ];

    function isNumber(value) {
        return !isNaN(value);
    }

    const isFloat = (number) => {
        return !!(number % 1);
    };

    const formatPrice = (price) => {
        return price.toLocaleString("pt-PT", {
            style: "currency",
            currency: "EUR",
            minimumFractionDigits: 2,
        });
    };

    const rowSelection = {
        onChange: (selectedRowKeys, selectedRows) => {
            if (selectedRowKeys.length === 0) {
                setSelectedRows([]);
                return;
            }

            setSelectedRows(selectedRowKeys);
        },
    };

    const editProduct = (id, item) => {
        if (typeof id != "number" && selected.length > 0) {
            setLoading(true);
            setOpen(true);
        } else {
            setSelectedRows([id]);
            setItemSelected(item);
            setOpen(true);
        }
    };

    const onCloseDrawer = (isSuccess) => {
        setSelectedRows([]);
        setItemSelected(null);
        setLoading(false);
        setOpen(false);
        setReloadQuery((prevValue) => prevValue + 1);

        if (isSuccess) {
            queryClient.invalidateQueries("products");
            queryClient.invalidateQueries("units-products");
        }
    };

    const onStatusChange = (value) => {
        setStatus(value || null);
        queryClient.invalidateQueries("products");
    };

    const hasSelected = selected.length > 0;

    const syncProducts = async () => {
        setLoadingProducts(true);
        mutateSycnProducts();
    };

    const syncBatchs = async () => {
        setLoadingBatchs(true);
        mutateSycnBatchs();
    };

    const syncClients = async () => {
        setLoadingClientes(true);
        mutateSycnClients();
    };

    return (
        <div className="products__bar">
            <Space
                style={{
                    display: "flex",
                    justifyContent: "space-between",
                    marginBottom: 16,
                }}
            >
                <Space className="products__element">
                    <Button
                        onClick={syncProducts}
                        disabled={loadingBatchs || loadingClientes}
                        loading={loadingProducts}
                        size="large"
                    >
                        Sincronizar produtos
                    </Button>
                    <Button
                        onClick={syncBatchs}
                        disabled={loadingProducts || loadingClientes}
                        loading={loadingBatchs}
                        size="large"
                    >
                        Sincronizar lotes
                    </Button>
                    <Button
                        onClick={syncClients}
                        disabled={loadingProducts || loadingBatchs}
                        loading={loadingClientes}
                        size="large"
                    >
                        Sincronizar clientes
                    </Button>
                </Space>
                <Space>
                    {hasSelected
                        ? `
                        ${selected.length}
                        ${
                            selected.length > 1
                                ? "Produtos selecionados"
                                : "Produto selecionado"
                        }`
                        : ""}
                </Space>
                <Space className="products__element">
                    <Select
                        placeholder="Filtrar Status"
                        onChange={onStatusChange}
                        size="large"
                        allowClear
                        options={[
                            { value: "activo", label: "Ativo" },
                            { value: "inactivo", label: "Inativo" },
                        ]}
                    />

                    <Search
                        placeholder="Pesquisa"
                        allowClear
                        onSearch={onSearch}
                        loading={searchLoading}
                        size="large"
                    />
                    <Button
                        type="primary"
                        onClick={editProduct}
                        disabled={!hasSelected}
                        loading={loading}
                        size="large"
                    >
                        Editar
                    </Button>
                </Space>
            </Space>
            <Table
                className="products__table"
                key={reloadQuery}
                rowKey="id"
                rowSelection={rowSelection}
                columns={columns}
                dataSource={items}
                sortDirections={["ascend", "descend"]}
                sortOrder={sortOrder}
                onChange={handleTableChange}
                pagination={{
                    current: page,
                    total: total,
                    pageSize: perPage,
                    onChange: handlePageChange,
                    showSizeChanger: true,
                    onShowSizeChange: handleSizePageChange,
                }}
            />
            {open && (
                <SidebarProducts
                    open={open}
                    onClose={onCloseDrawer}
                    ids={selected}
                    itemSelected={itemSelected}
                />
            )}
        </div>
    );
}

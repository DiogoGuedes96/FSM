import React, { useState, useEffect } from "react";
import { useQuery, useMutation } from "react-query";
import { Button, Input, Dropdown, Space, Select } from "antd";
import { getAllCategories } from "../../libs/apis";
import "./base.scss"

export default function ProductFilters({ onClickFilters, onSearch }) {
    const { Search } = Input;
    const [filterFamily, setFilterFamily] = useState(null);
    const [filterSubFamily, setFilterSubFamily] = useState(null);
    const [filterGroup, setFilterGroup] = useState(null);
    const [filterStock, setFilterStock] = useState("inStock");
    const [itemsFruits, setItemsFruits] = useState([]);
    const [itemsVegetables, setItemsVegetables] = useState([]);
    const [itemsOthers, setItemsOthers] = useState([]);

    const { data: allCategoriesData, isLoading: isLoadingAllCategories } =
        useQuery(["allCategories"], getAllCategories, {
            refetchOnWindowFocus: false,
        });
    const stockItems = [
        {
            key: "inStock",
            label: <div onClick={() => handleChange("inStock")}>Em Stock</div>,
        },
        {
            key: "outStock",
            label: (
                <div onClick={() => handleChange("outStock")}>Sem Stock</div>
            ),
        },
    ];

    useEffect(() => {
        if (allCategoriesData?.categories) {
            const itemsFruitsArr = [];
            const itemsVegetablesArr = [];
            const itemsOthersArr = [];
            const categories = allCategoriesData?.categories;
            const fruitsGroup = categories.find(
                (category) => category.group === "fruits"
            );
            const vegetablesGroup = categories.find(
                (category) => category.group === "vegetables"
            );
            const othersGroup = categories.find(
                (category) => category.group === "others"
            );

            if (fruitsGroup) {
                fruitsGroup?.categories.map((value, i) => {
                    itemsFruitsArr.push({
                        key: value.erpCategoryCode,
                        label: (
                            <span
                                onClick={() =>
                                    onFilterChange(
                                        value.erpCategoryCode,
                                        null,
                                        null,
                                        "inStock"
                                    )
                                }
                            >
                                {value.name}
                            </span>
                        ),
                        children: value?.subCategories.map((sub, i) => {
                            return {
                                key: sub.erpSubCategoryCode,
                                label: (
                                    <span
                                        onClick={() =>
                                            onFilterChange(
                                                value.erpCategoryCode,
                                                sub.erpSubCategoryCode,
                                                null,
                                                "inStock"
                                            )
                                        }
                                    >
                                        {sub.name}
                                    </span>
                                ),
                            };
                        }),
                    });
                });
                setItemsFruits(itemsFruitsArr);
            }

            if (vegetablesGroup) {
                vegetablesGroup?.categories.map((value, i) => {
                    itemsVegetablesArr.push({
                        key: value.erpCategoryCode,
                        label: (
                            <span
                                onClick={() =>
                                    onFilterChange(
                                        value.erpCategoryCode,
                                        null,
                                        null,
                                        "inStock"
                                    )
                                }
                            >
                                {value.name}
                            </span>
                        ),
                        children: value?.subCategories.map((sub, i) => {
                            return {
                                key: sub.erpSubCategoryCode,
                                label: (
                                    <span
                                        onClick={() =>
                                            onFilterChange(
                                                value.erpCategoryCode,
                                                sub.erpSubCategoryCode,
                                                null,
                                                "inStock"
                                            )
                                        }
                                    >
                                        {sub.name}
                                    </span>
                                ),
                            };
                        }),
                    });
                });
                setItemsVegetables(itemsVegetablesArr);
            }

            if (othersGroup) {
                othersGroup?.categories.map((value, i) => {
                    itemsOthersArr.push({
                        key: value.erpCategoryCode,
                        label: (
                            <span
                                onClick={() =>
                                    onFilterChange(
                                        value.erpCategoryCode,
                                        null,
                                        null,
                                        "inStock"
                                    )
                                }
                            >
                                {value.name}
                            </span>
                        ),
                        children: value?.subCategories.map((sub, i) => {
                            return {
                                key: sub.erpSubCategoryCode,
                                label: (
                                    <span
                                        onClick={() =>
                                            onFilterChange(
                                                value.erpCategoryCode,
                                                sub.erpSubCategoryCode,
                                                null,
                                                "inStock"
                                            )
                                        }
                                    >
                                        {sub.name}
                                    </span>
                                ),
                            };
                        }),
                    });
                });
                setItemsOthers(itemsOthersArr);
            }
        }
    }, [allCategoriesData]);

    const onFilterChange = (category, subCategory, group, stock) => {
        onClickFilters(category, subCategory, group, stock);
        setFilterFamily(category);
        setFilterSubFamily(subCategory);
        setFilterGroup(group);
        setFilterStock(stock);
    };

    const handleChange = (value) => {
        if (value === "outStock") {
            setFilterStock("outStock");
            onFilterChange(
                filterFamily,
                filterSubFamily,
                filterGroup,
                "outStock"
            );
        } else if (value === "inStock") {
            setFilterStock("inStock");
            onFilterChange(
                filterFamily,
                filterSubFamily,
                filterGroup,
                "inStock"
            );
        }
    };

    const onSearchChange = (value) => {
        setFilterFamily(null);
        setFilterSubFamily(null);
        setFilterGroup(null);
        setFilterStock("inStock");
        onSearch(value, "inStock");
    };

    return (
        <div className="order-list__filter">
            {itemsFruits.length > 0 &&
                itemsVegetables.length > 0 &&
                itemsOthers.length > 0 && (
                    <>
                        <Dropdown
                            menu={{
                                items: itemsFruits,
                            }}
                        >
                            <Space>
                                <Button
                                    type="primary"
                                    size="large"
                                    block
                                    onClick={() =>
                                        onFilterChange(
                                            null,
                                            null,
                                            "fruits",
                                            "inStock"
                                        )
                                    }
                                    style={{
                                        width: 100,
                                        marginRight: 8,
                                        marginBottom: 8,
                                        color: "#613400",
                                    }}
                                >
                                    Frutas
                                </Button>
                            </Space>
                        </Dropdown>
                        <Dropdown
                            menu={{
                                items: itemsVegetables,
                            }}
                        >
                            <Space>
                                <Button
                                    type="primary"
                                    size="large"
                                    block
                                    onClick={(e) =>
                                        onFilterChange(
                                            null,
                                            null,
                                            "vegetables",
                                            "inStock"
                                        )
                                    }
                                    style={{
                                        width: 100,
                                        marginRight: 8,
                                        marginBottom: 8,
                                        color: "#613400",
                                    }}
                                >
                                    Verduras
                                </Button>
                            </Space>
                        </Dropdown>
                        <Dropdown
                            menu={{
                                items: itemsOthers,
                            }}
                        >
                            <Space>
                                <Button
                                    type="primary"
                                    size="large"
                                    block
                                    onClick={(e) =>
                                        onFilterChange(
                                            null,
                                            null,
                                            "others",
                                            "inStock"
                                        )
                                    }
                                    style={{
                                        width: 100,
                                        marginRight: 8,
                                        marginBottom: 8,
                                        color: "#613400",
                                    }}
                                >
                                    Outros
                                </Button>
                            </Space>
                        </Dropdown>
                        <Dropdown
                            menu={{
                                selectedKeys: filterStock,
                                selectable: true,
                                items: stockItems,
                            }}
                        >
                            <Space>
                                <Button
                                    type="primary"
                                    size="large"
                                    block
                                    style={{
                                        width: 100,
                                        marginRight: 8,
                                        marginBottom: 8,
                                        color: "#613400",
                                    }}
                                >
                                    Stock
                                </Button>
                            </Space>
                        </Dropdown>
                        <Search
                            className="search-products"
                            placeholder="Escreva aqui o que procura ..."
                            allowClear
                            onSearch={(value) => onSearchChange(value)}
                            size="large"
                            style={{
                                width: "100%",
                            }}
                        />
                    </>
                )}
        </div>
    );
}

import React, { useState, useEffect } from "react";
import { useQuery, useMutation } from "react-query";
import ProductFilters from "../productFilters";
import ProductModal from "../productModal";

import InfiniteScroll from "react-infinite-scroll-component";
import { Card, Row, Col, Empty } from "antd";

import ProductCard from "./productCard.js";
import { getAllProducts, getFilteredProducts } from "../../libs/apis";
import DirectSaleProductModal from "../directSale/directSaleProductModal.js";

export default function ProductList({
    isProductFilters,
    addCartProduct,
    typePrice = 0,
    isDirectSale = false,
}) {
    const { data: allProductsData, isLoading: isLoadingAllProducts } = useQuery(
        ["allProducts", typePrice],
        getAllProducts,
        {
            refetchOnWindowFocus: false,
        }
    );

    const {
        data: mutationData,
        isLoading: isLoadingMutation,
        mutate: mutateFilteredProducts,
    } = useMutation(["mutationFilteredProducts"], getFilteredProducts);

    const [productsData, setProductsData] = useState();
    const [productsCount, setProductsCount] = useState(0);
    const [filterFamily, setFilterFamily] = useState(null);
    const [filterSubFamily, setFilterSubFamily] = useState(null);
    const [filterGroup, setFilterGroup] = useState(null);
    const [filterSearch, setFilterSearch] = useState(null);
    const [filterStock, setFilterStock] = useState("inStock");
    const [currentPage, setCurrentPage] = useState(1);
    const [hasMoreProducts, setHasMoreProducts] = useState(true);

    const [isModalVisible, setIsModalVisible] = useState(false);
    const [modalProduct, setModalProduct] = useState(false);

    const onCardClick = (modalData) => {
        setModalProduct(modalData);
        setIsModalVisible(true);
    };

    const handleModalCancel = () => {
        setIsModalVisible(false);
        setModalProduct(modalProduct);
    };

    const loadMore = () => {
        setCurrentPage(currentPage + 1);
        mutateFilteredProducts({
            group: filterGroup,
            family: filterFamily,
            filterSubFamily: filterSubFamily,
            searchInput: filterSearch,
            stock: filterStock,
            page: currentPage + 1,
            stock: filterStock,
        });
    };

    const onClickFilters = (
        category = null,
        subCategory = null,
        group = null,
        stock = null
    ) => {
        setFilterFamily(null);
        setFilterSubFamily(null);
        setFilterGroup(null);

        if (category) setFilterFamily(category);
        if (subCategory) setFilterSubFamily(subCategory);
        if (group) setFilterGroup(group);
        if (stock) setFilterStock(stock);

        setHasMoreProducts(true);
        setCurrentPage(1);
        mutateFilteredProducts({
            group: group,
            family: category,
            filterSubFamily: subCategory,
            searchInput: filterSearch,
            stock: stock,
            page: currentPage,
        });
    };

    const onSearch = (value, stock) => {
        setFilterFamily(null);
        setFilterSubFamily(null);
        setFilterGroup(null);
        setFilterStock(stock);

        setHasMoreProducts(true);
        setCurrentPage(1);
        setFilterSearch(value);
        mutateFilteredProducts({
            family: null,
            filterSubFamily: null,
            searchInput: value,
            stock: stock,
            page: currentPage,
        });
    };

    useEffect(() => {
        if (allProductsData) {
            setProductsData(allProductsData?.products);
            setProductsCount(allProductsData?.products?.length);
        }
    }, [allProductsData]);

    useEffect(() => {
        if (mutationData) {
            if (currentPage === 1) {
                setProductsData(mutationData?.products);
                setProductsCount(mutationData?.products?.length);
            } else {
                setProductsData(productsData.concat(mutationData?.products));
                setProductsCount(
                    mutationData?.products?.length + productsData.length
                );
                setCurrentPage(mutationData?.pagination?.currentPage);

                if (productsCount >= mutationData?.pagination?.total) {
                    setHasMoreProducts(false);
                }
            }
        }
    }, [mutationData]);

    return (
        <>
            <ProductFilters
                onClickFilters={onClickFilters}
                onSearch={onSearch}
            />
            {productsData?.length <= 0 && (
                <Empty
                    style={{
                        width: "100%",
                        marginTop: "30vh",
                        padding: "10px",
                    }}
                    description={
                        "Não existem produtos para os filtros aplicados!"
                    }
                />
            )}
            <InfiniteScroll
                style={{ padding: 24 }}
                dataLength={productsCount ?? 0}
                next={loadMore}
                hasMore={hasMoreProducts}
                scrollableTarget="product-row"
            >
                <Row
                    id="product-row"
                    gutter={[24, 24]}
                    style={{
                        height: "calc(100vh - 160px)",
                        overflow: "scroll",
                    }}
                >
                    {productsData &&
                        productsData.map((value, i) => {
                            return (
                                <Col
                                    key={value?.product?.name}
                                    xs={24}
                                    sm={12}
                                    md={8}
                                    xxl={6}
                                >
                                    {isLoadingMutation ? (
                                        <Card key={"loading"} loading={true} />
                                    ) : (
                                        <ProductCard
                                            key={value?.product?.id}
                                            onCardClick={onCardClick}
                                            product={value}
                                            typePrice={typePrice}
                                        />
                                    )}
                                </Col>
                            );
                        })}
                        {isModalVisible && (
                            isDirectSale ? (
                                <DirectSaleProductModal
                                    open={isModalVisible}
                                    onCancel={handleModalCancel}
                                    product={modalProduct}
                                    addCart={(props) => {
                                        handleModalCancel();
                                        addCartProduct(props);
                                    }}
                                />
                            ) : (
                                <ProductModal
                                    open={isModalVisible}
                                    onCancel={handleModalCancel}
                                    product={modalProduct}
                                    addCart={(props) => {
                                        handleModalCancel();
                                        addCartProduct(props);
                                    }}
                                />
                            )
                        )}
                </Row>
            </InfiniteScroll>
        </>
    );
}


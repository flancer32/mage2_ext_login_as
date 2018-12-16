<?php

/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Flancer32\LoginAs\Cli\Cmd\Init\Catalog\A;

/**
 * Create link between product & category for development/demo instances.
 */
class Link
{
    /** @var  \Magento\Catalog\Api\CategoryLinkRepositoryInterface */
    private $repoCatLink;
    /** @var \Magento\Catalog\Api\ProductRepositoryInterface */
    private $repoProd;
    /** @var \Magento\Catalog\Api\Data\CategoryProductLinkInterfaceFactory */
    private $factLink;

    public function __construct(
        \Magento\Catalog\Api\Data\CategoryProductLinkInterfaceFactory $factLink,
        \Magento\Catalog\Api\ProductRepositoryInterface\Proxy $repoProd,
        \Magento\Catalog\Api\CategoryLinkRepositoryInterface\Proxy $repoCatLink
    ) {
        $this->factLink = $factLink;
        $this->repoProd = $repoProd;
        $this->repoCatLink = $repoCatLink;
    }


    public function exec($catId, $prodId)
    {
        /* perform action */
        $prod = $this->repoProd->getById($prodId);
        $catsExist = $prod->getCategoryIds();
        if (empty($catsExist)) {
            $prodLink = $this->factLink->create();
            $prodLink->setCategoryId($catId);
            $sku = $prod->getSku();
            $prodLink->setSku($sku);
            $prodLink->setPosition(1);
            $this->repoCatLink->save($prodLink);
        }
    }
}
<?php

/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Flancer32\LoginAs\Cli\Cmd\Init\Catalog;

use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Create link between product & category for development/demo instances.
 */
class Link
{
    /** Context variables. */
    const CTX_CAT_ID = \Flancer32\LoginAs\Cli\Cmd\Init\Catalog::CTX_CAT_ID;
    const CTX_PROD_ID = \Flancer32\LoginAs\Cli\Cmd\Init\Catalog::CTX_PROD_ID;

    /** @var   \Magento\Framework\ObjectManagerInterface */
    protected $manObj;
    /** @var \Magento\Catalog\Api\ProductRepositoryInterface */
    protected $repoProd;
    /** @var  \Magento\Catalog\Api\CategoryLinkRepositoryInterface */
    protected $repoCatLink;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Magento\Catalog\Api\ProductRepositoryInterface\Proxy $repoProd,
        \Magento\Catalog\Api\CategoryLinkRepositoryInterface\Proxy $repoCatLink
    ) {
        $this->manObj = $manObj;
        $this->repoProd = $repoProd;
        $this->repoCatLink = $repoCatLink;
    }


    public function exec(\Flancer32\Lib\Data $ctx)
    {
        /* get working variables from context */
        $catId = $ctx->get(self::CTX_CAT_ID);
        $prodId = $ctx->get(self::CTX_PROD_ID);
        /* perform action */
        $prod = $this->repoProd->getById($prodId);
        $catsExist = $prod->getCategoryIds();
        if (empty($catsExist)) {
            $prodLink = $this->manObj->create(\Magento\Catalog\Api\Data\CategoryProductLinkInterface::class);
            $prodLink->setCategoryId($catId);
            $sku = $prod->getSku();
            $prodLink->setSku($sku);
            $prodLink->setPosition(1);
            $this->repoCatLink->save($prodLink);
        }
    }
}
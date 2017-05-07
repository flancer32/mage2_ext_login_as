<?php

/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Flancer32\LoginAs\Cli\Cmd\Init\Catalog;

use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Create product for development/demo instances.
 */
class Product
{
    /** Context variable to return ID of the found/created demo product. */
    const CTX_PROD_ID = \Flancer32\LoginAs\Cli\Cmd\Init\Catalog::CTX_PROD_ID;
    /** attributes of the demo product.  */
    const DEF_PROD_NAME = 'Demo Product';
    const DEF_PROD_PRICE = '12.34';
    const DEF_PROD_SKU = 'demo001';
    const DEF_PROD_WEIGHT = '0.5';
    const DEF_PROD_QTY = 1024;
    /** @var   \Magento\Framework\ObjectManagerInterface */
    protected $manObj;
    /** @var \Magento\Catalog\Api\AttributeSetRepositoryInterface */
    protected $repoAttrSet;
    /** @var \Magento\Catalog\Api\ProductRepositoryInterface */
    protected $repoProd;
    /** @var \Magento\CatalogInventory\Api\StockItemRepositoryInterface */
    protected $repoStockItem;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Magento\Catalog\Api\AttributeSetRepositoryInterface $repoAttrSet,
        \Magento\Catalog\Api\ProductRepositoryInterface $repoProd,
        \Magento\CatalogInventory\Api\StockItemRepositoryInterface $repoStockItem
    ) {
        $this->manObj = $manObj;
        $this->repoAttrSet = $repoAttrSet;
        $this->repoProd = $repoProd;
        $this->repoStockItem = $repoStockItem;
    }


    /**
     * Create simple product.
     *
     * @return int ID of the created product.
     */
    public function create()
    {
        /**
         * Retrieve attribute set ID.
         */
        /** @var \Magento\Framework\Api\SearchCriteriaInterface $crit */
        $crit = $this->manObj->create(\Magento\Framework\Api\SearchCriteriaInterface::class);
        /** @var \Magento\Eav\Model\Entity\Attribute\Set $attrSet */
        $list = $this->repoAttrSet->getList($crit);
        $items = $list->getItems();
        $attrSet = reset($items);
        $attrSetId = $attrSet->getId();
        /**
         * Create simple product.
         */
        /** @var  $product ProductInterface */
        $product = $this->manObj->create(\Magento\Catalog\Api\Data\ProductInterface::class);
        $product->setSku(self::DEF_PROD_SKU);
        $product->setName(self::DEF_PROD_NAME);
        $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
        $product->setPrice(self::DEF_PROD_PRICE);
        $product->setWeight(self::DEF_PROD_WEIGHT);
        $product->setAttributeSetId($attrSetId);
        $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
        $product->setUrlKey(self::DEF_PROD_SKU); // use SKU as URL Key
        $saved = $this->repoProd->save($product);
        /* create inventory data */
        /** @var \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem */
        $stockItem = $this->manObj->create(\Magento\CatalogInventory\Api\Data\StockItemInterface::class);
        $stockItem->setQty(self::DEF_PROD_QTY);
        $stockItem->setIsInStock(true);
        $this->repoStockItem->save($stockItem);
        /* return product ID */
        $result = $saved->getId();
        return $result;
    }

    public function exec(\Flancer32\Lib\Data $ctx)
    {
        $found = null;
        try {
            $found = $this->repoProd->get(self::DEF_PROD_SKU, true);
            $prodId = $found->getId();
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            /* do nothing */
        }
        if (!$found) {
            $prodId = $this->create();
        }
        $ctx->set(self::CTX_PROD_ID, $prodId);
    }
}
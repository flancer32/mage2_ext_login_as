<?php

/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Flancer32\LoginAs\Cli\Cmd\Init\Catalog\A;

use Magento\Catalog\Model\Product\Attribute\Source\Status as ProdStatus;
use Magento\Catalog\Model\Product\Type as ProdType;

/**
 * Create product for development/demo instances.
 */
class Product
{
    /** attributes of the demo product.  */
    private const DEF_PROD_NAME = 'Demo Product';
    private const DEF_PROD_PRICE = '12.34';
    private const DEF_PROD_QTY = 1024;
    public const DEF_PROD_SKU = 'demo001';
    private const DEF_PROD_WEIGHT = '0.5';

    /** @var \Magento\Framework\Api\Search\SearchCriteriaInterfaceFactory */
    private $factCrit;
    /** @var \Magento\CatalogInventory\Api\StockItemCriteriaInterfaceFactory */
    private $factCritStockItem;
    /** @var \Magento\Catalog\Api\Data\ProductInterfaceFactory */
    private $factProd;
    /** @var \Magento\Catalog\Api\AttributeSetRepositoryInterface */
    private $repoAttrSet;
    /** @var \Magento\Catalog\Api\ProductRepositoryInterface */
    private $repoProd;
    /** @var \Magento\CatalogInventory\Api\StockItemRepositoryInterface */
    private $repoStockItem;

    public function __construct(
        \Magento\Framework\Api\Search\SearchCriteriaInterfaceFactory $factCrit,
        \Magento\CatalogInventory\Api\StockItemCriteriaInterfaceFactory $factCritStockItem,
        \Magento\Catalog\Api\Data\ProductInterfaceFactory $factProd,
        \Magento\Catalog\Api\AttributeSetRepositoryInterface $repoAttrSet,
        \Magento\Catalog\Api\ProductRepositoryInterface\Proxy $repoProd,
        \Magento\CatalogInventory\Api\StockItemRepositoryInterface\Proxy $repoStockItem
    ) {
        $this->factCrit = $factCrit;
        $this->factCritStockItem = $factCritStockItem;
        $this->factProd = $factProd;
        $this->repoAttrSet = $repoAttrSet;
        $this->repoProd = $repoProd;
        $this->repoStockItem = $repoStockItem;
    }

    /**
     * Create simple product.
     *
     * @return int ID of the created product.
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     */
    private function createProduct()
    {
        /**
         * Retrieve attribute set ID.
         */
        /** @var \Magento\Framework\Api\SearchCriteriaInterface $crit */
        $crit = $this->factCrit->create();
        /** @var \Magento\Eav\Model\Entity\Attribute\Set $attrSet */
        $list = $this->repoAttrSet->getList($crit);
        $items = $list->getItems();
        $attrSet = reset($items);
        $attrSetId = $attrSet->getId();
        /**
         * Create simple product.
         */
        /** @var  $product \Magento\Catalog\Model\Product */
        $product = $this->factProd->create();
        $product->setSku(self::DEF_PROD_SKU);
        $product->setName(self::DEF_PROD_NAME);
        $product->setStatus(ProdStatus::STATUS_ENABLED);
        $product->setPrice(self::DEF_PROD_PRICE);
        $product->setWeight(self::DEF_PROD_WEIGHT);
        $product->setAttributeSetId($attrSetId);
        $product->setTypeId(ProdType::TYPE_SIMPLE);
        $product->setUrlKey(self::DEF_PROD_SKU); // use SKU as URL Key
        $saved = $this->repoProd->save($product);
        $prodId = $saved->getId();
        /* return product ID */
        return $prodId;
    }

    /**
     * Create inventory data.
     *
     * @param int $prodId
     */
    private function createStockItem($prodId)
    {
        /** @var \Magento\CatalogInventory\Api\StockItemCriteriaInterface $crit */
        $crit = $this->factCritStockItem->create();
        $crit->setProductsFilter($prodId);
        $found = $this->repoStockItem->getList($crit);
        $items = $found->getItems();
        /** @var \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem */
        $stockItem = reset($items);
        $stockItem->setQty(self::DEF_PROD_QTY);
        $stockItem->setIsInStock(true);
        $this->repoStockItem->save($stockItem);
    }

    public function exec()
    {
        $found = null;
        try {
            $found = $this->repoProd->get(self::DEF_PROD_SKU, true);
            $result = $found->getId();
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            /* do nothing */
        }
        if (!$found) {
            $result = $this->createProduct();
            $this->createStockItem($result);
        }
        return $result;
    }
}
<?php

/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Flancer32\LoginAs\Cli\Cmd\Init\Catalog;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use \Flancer32\LoginAs\Cli\Cmd\Init\Users\Create as SubCreate;
use \Flancer32\LoginAs\Cli\Cmd\Init\Users\Check as SubCheck;
use \Flancer32\LoginAs\Config as Cfg;

/**
 * Create category for development/demo instances.
 */
class Category
{
    /** Context variable to return ID of the found/created demo category. */
    const CTX_CAT_ID = \Flancer32\LoginAs\Cli\Cmd\Init\Catalog::CTX_CAT_ID;
    /** Name of the demo category.  */
    const DEF_CAT_NAME = 'Demo';
    /** @var   \Magento\Framework\ObjectManagerInterface */
    protected $manObj;
    /** @var  \Magento\Catalog\Model\Category\Tree */
    protected $modTree;
    /** @var   \Magento\Catalog\Api\CategoryRepositoryInterface */
    protected $repoCategory;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Magento\Catalog\Model\Category\Tree\Proxy $modTree,
        \Magento\Catalog\Api\CategoryRepositoryInterface $repoCategory
    ) {
        $this->manObj = $manObj;
        $this->modTree = $modTree;
        $this->repoCategory = $repoCategory;
    }

    /**
     * Create new Magento category with given $name.
     *
     * @param string $name
     * @return int ID of the created category
     */
    public function createMageCategory($name)
    {
        /** @var  $category \Magento\Catalog\Api\Data\CategoryInterface */
        $category = $this->manObj->create(\Magento\Catalog\Api\Data\CategoryInterface::class);
        $category->setName($name);
        $category->setIsActive(true);
        $saved = $this->repoCategory->save($category);
        $result = $saved->getId();
        return $result;
    }

    public function exec($ctx)
    {
        $isDemoCatFound = false;
        $rootNode = $this->modTree->getRootNode();
        $children = $rootNode->getAllChildNodes();
        /** @var \Magento\Framework\Data\Tree\Node $child */
        foreach ($children as $child) {
            $name = $child->getName();
            if ($name == self::DEF_CAT_NAME) {
                $isDemoCatFound = true;
                $catId = $child->getId();
            }
        }
        if (!$isDemoCatFound) {
            $catId = $this->createMageCategory(self::DEF_CAT_NAME);
        }
        $ctx->set(self::CTX_CAT_ID, $catId);
    }
}
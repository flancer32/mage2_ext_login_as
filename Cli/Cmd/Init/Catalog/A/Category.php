<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Flancer32\LoginAs\Cli\Cmd\Init\Catalog\A;

/**
 * Create category for development/demo instances.
 */
class Category
{
    /** Name of the demo category.  */
    private const DEF_CAT_NAME = 'Demo';
    /** @var \Magento\Catalog\Model\CategoryFactory */
    private $factCat;
    /** @var  \Magento\Catalog\Model\Category\Tree */
    private $modTree;
    /** @var   \Magento\Catalog\Api\CategoryRepositoryInterface */
    private $repoCategory;

    public function __construct(
        \Magento\Catalog\Model\CategoryFactory $factCat,
        \Magento\Catalog\Model\Category\Tree\Proxy $modTree,
        \Magento\Catalog\Api\CategoryRepositoryInterface $repoCategory
    ) {
        $this->factCat = $factCat;
        $this->modTree = $modTree;
        $this->repoCategory = $repoCategory;
    }

    /**
     * Create new Magento category with given $name.
     *
     * @param string $name
     * @return int ID of the created category
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function createMageCategory($name)
    {
        /** @var  $category \Magento\Catalog\Api\Data\CategoryInterface */
        $category = $this->factCat->create();
        $category->setName($name);
        $category->setIsActive(true);
        $saved = $this->repoCategory->save($category);
        $result = $saved->getId();
        return $result;
    }

    /**
     * Find demo category or create new one.
     *
     * @return int
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function exec()
    {
        $isDemoCatFound = false;
        $rootNode = $this->modTree->getRootNode();
        $children = $rootNode->getAllChildNodes();
        /** @var \Magento\Framework\Data\Tree\Node $child */
        foreach ($children as $child) {
            $name = $child->getName();
            if ($name == self::DEF_CAT_NAME) {
                $isDemoCatFound = true;
                $result = $child->getId();
            }
        }
        if (!$isDemoCatFound) {
            $result = $this->createMageCategory(self::DEF_CAT_NAME);
        }
        return $result;
    }
}
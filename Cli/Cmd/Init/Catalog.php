<?php

/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Flancer32\LoginAs\Cli\Cmd\Init;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use \Flancer32\LoginAs\Config as Cfg;
use \Flancer32\LoginAs\Cli\Cmd\Init\Catalog\Category as SubCategory;
use \Flancer32\LoginAs\Cli\Cmd\Init\Catalog\Product as SubProduct;

/**
 * Create categories and products inside.
 */
class Catalog
    extends \Flancer32\LoginAs\Cli\Cmd\Base
{
    const CTX_CAT_ID = 'categoryId';
    const CTX_PROD_ID = 'productId';
    /** @var  \Flancer32\LoginAs\Cli\Cmd\Init\Catalog\Category */
    protected $subCategory;
    /** @var  \Flancer32\LoginAs\Cli\Cmd\Init\Catalog\Product */
    protected $subProduct;
    /** @var  \Flancer32\LoginAs\Cli\Cmd\Init\Catalog\Link */
    protected $subLink;

    public function __construct(
        \Flancer32\LoginAs\Cli\Cmd\Init\Catalog\Category $subCategory,
        \Flancer32\LoginAs\Cli\Cmd\Init\Catalog\Product $subProduct,
        \Flancer32\LoginAs\Cli\Cmd\Init\Catalog\Link $subLink
    ) {
        parent::__construct(self::class);
        $this->subCategory = $subCategory;
        $this->subProduct = $subProduct;
        $this->subLink = $subLink;
    }

    protected function configure()
    {
        parent::configure();
        $this->setName('fl32:init:catalog');
        $this->setDescription("Create test categories & products for 'Flancer32_LoginAs' module.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Create test categories & products for \'Flancer32_LoginAs\' module.<info>');
        $ctx = new \Flancer32\Lib\Data();
        $this->subCategory->exec($ctx);
        $this->subProduct->exec($ctx);
        $this->subLink->exec($ctx);
        $output->writeln('<info>Command is completed.<info>');
    }

}
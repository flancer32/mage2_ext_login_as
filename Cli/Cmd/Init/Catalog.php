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
    /** @var  \Flancer32\LoginAs\Cli\Cmd\Init\Catalog\Category */
    protected $subCategory;
    /** @var  \Flancer32\LoginAs\Cli\Cmd\Init\Catalog\Product */
    protected $subProduct;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Flancer32\LoginAs\Cli\Cmd\Init\Catalog\Category $subCategory,
        \Flancer32\LoginAs\Cli\Cmd\Init\Catalog\Product $subProduct
    ) {
        parent::__construct($manObj);
        $this->subCategory = $subCategory;
        $this->subProduct = $subProduct;
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
        $catId = $ctx->get(SubCategory::CTX_CAT_ID);
        $this->subProduct->exec($ctx);
        $output->writeln('<info>Command is completed.<info>');
    }

}
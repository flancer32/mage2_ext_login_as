<?php

/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Flancer32\LoginAs\Cli\Cmd\Init;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Create categories and products inside.
 */
class Catalog
    extends \Flancer32\Base\App\Cli\Base
{
    private const DESC = 'Create test categories & products for \'Flancer32_LoginAs\' module.';
    private const NAME = 'fl32:init:catalog';

    /** @var  \Flancer32\LoginAs\Cli\Cmd\Init\Catalog\A\Category */
    private $aCategory;
    /** @var  \Flancer32\LoginAs\Cli\Cmd\Init\Catalog\A\Link */
    private $aLink;
    /** @var  \Flancer32\LoginAs\Cli\Cmd\Init\Catalog\A\Product */
    private $aProduct;

    public function __construct(
        \Flancer32\LoginAs\Cli\Cmd\Init\Catalog\A\Category $aCategory,
        \Flancer32\LoginAs\Cli\Cmd\Init\Catalog\A\Product $aProduct,
        \Flancer32\LoginAs\Cli\Cmd\Init\Catalog\A\Link $aLink
    ) {
        parent::__construct(self::NAME, self::DESC);
        $this->aCategory = $aCategory;
        $this->aProduct = $aProduct;
        $this->aLink = $aLink;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Create test categories & products for \'Flancer32_LoginAs\' module.<info>');
        $catId = $this->aCategory->exec();
        $prodId = $this->aProduct->exec();
        $this->aLink->exec($catId, $prodId);
        $output->writeln('<info>Command is completed.<info>');
    }

}
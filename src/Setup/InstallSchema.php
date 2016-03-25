<?php
/**
 * Create DB schema for the module.
 *
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Flancer32\LoginAs\Setup;

use Magento\Framework\DB\Ddl\Table as Ddl;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{

    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    )
    {
        $installer = $setup;
        $installer->startSetup();
//        $conn = $installer->getConnection();
        $installer->endSetup();
    }

}
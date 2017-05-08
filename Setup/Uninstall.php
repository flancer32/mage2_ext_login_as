<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Flancer32\LoginAs\Setup;

class Uninstall
    implements \Magento\Framework\Setup\UninstallInterface
{
    /** @var \Magento\Framework\App\ResourceConnection */
    protected $resource;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->resource = $resource;
    }

    public function uninstall(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $setup->startSetup();
        $conn = $setup->getConnection();

        /* Active */
        $entityAlias = \Flancer32\LoginAs\Repo\Data\Entity\Active::ENTITY_NAME;
        $tbl = $this > $this->resource->getTableName($entityAlias);
        $conn->dropTable($tbl);

        /* Log */
        $entityAlias = \Flancer32\LoginAs\Repo\Data\Entity\Log::ENTITY_NAME;
        $tbl = $this > $this->resource->getTableName($entityAlias);
        $conn->dropTable($tbl);


        $setup->endSetup();
    }
}
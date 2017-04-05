<?php
/**
 * Create DB schema for the module.
 *
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Flancer32\LoginAs\Setup;

class InstallSchema
    implements \Magento\Framework\Setup\InstallSchemaInterface
{
    /** @var \Flancer32\Lib\Repo\Setup\Dem\Tool */
    protected $toolDem;

    public function __construct(
        \Flancer32\Lib\Repo\Setup\Dem\Tool $toolDem
    ) {
        $this->toolDem = $toolDem;
    }

    public function install(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();
        $this->processDem();
        $installer->endSetup();
    }

    protected function processDem()
    {
        /** Read and parse JSON schema. */
        $pathToFile = __DIR__ . '/../etc/dem.json';
        $pathToNode = '/dBEAR/package/Flancer32/package/LoginAs';
        $demPackage = $this->toolDem->readDemPackage($pathToFile, $pathToNode);

        /* Active */
        $entityAlias = \Flancer32\LoginAs\Repo\Data\Entity\Active::ENTITY_NAME;
        $demEntity = $demPackage->get('/entity/Active');
        $this->toolDem->createEntity($entityAlias, $demEntity);

    }
}
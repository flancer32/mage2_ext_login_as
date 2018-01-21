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
    /** @var \Flancer32\Lib\Repo\Api\Helper\Dem */
    private $hlpDem;

    public function __construct(
        \Flancer32\Lib\Repo\Api\Helper\Dem $hlpDem
    ) {
        $this->hlpDem = $hlpDem;
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

    private function processDem()
    {
        /** Read and parse JSON schema. */
        $pathToFile = __DIR__ . '/../etc/dem.json';
        $pathToNode = '/dBEAR/package/Flancer32/package/LoginAs';
        $demPackage = $this->hlpDem->readDemPackage($pathToFile, $pathToNode);

        /* Active */
        $entityAlias = \Flancer32\LoginAs\Repo\Data\Entity\Active::ENTITY_NAME;
        $demEntity = $demPackage->get('/entity/Active');
        $this->hlpDem->createEntity($entityAlias, $demEntity);

        /* Log */
        $entityAlias = \Flancer32\LoginAs\Repo\Data\Entity\Log::ENTITY_NAME;
        $demEntity = $demPackage->get('/entity/Log');
        $this->hlpDem->createEntity($entityAlias, $demEntity);

    }
}
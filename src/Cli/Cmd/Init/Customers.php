<?php

/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Flancer32\LoginAs\Cli\Cmd\Init;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Create test customers for development deployment.
 */
class Customers
    extends \Symfony\Component\Console\Command\Command
{
    /** @var \Magento\Framework\ObjectManagerInterface */
    protected $manObj;
    /** @var \Magento\Customer\Api\CustomerRepositoryInterface */
    protected $repoCust;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Magento\Customer\Api\CustomerRepositoryInterface $repoCust
    ) {
        /* object manager is used in __construct/configure */
        $this->manObj = $manObj;
        $this->repoCust = $repoCust;
        parent::__construct();
    }

    protected function configure()
    {
        parent::configure();
        $this->setName('init:customers');
        $this->setDescription("Create test customers for 'Flancer32_LoginAs' module.");
        /* Magento related config (Object Manager) */
        /** @var \Magento\Framework\App\State $appState */
        $appState = $this->manObj->get(\Magento\Framework\App\State::class);
        try {
            /* area code should be set only once */
            $appState->getAreaCode();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            /* exception will be thrown if no area code is set */
            $areaCode = \Magento\Framework\App\Area::AREA_FRONTEND;
            $appState->setAreaCode($areaCode);
            /** @var \Magento\Framework\ObjectManager\ConfigLoaderInterface $configLoader */
            $configLoader = $this->manObj->get(\Magento\Framework\ObjectManager\ConfigLoaderInterface::class);
            $config = $configLoader->load($areaCode);
            $this->manObj->configure($config);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var \Magento\Customer\Api\Data\CustomerInterface $cust */
        $cust = $this->manObj->create(\Magento\Customer\Api\Data\CustomerInterface::class);
        $cust->setEmail('alex@flancer64.com');
        $cust->setFirstname('Alex');
        $cust->setLastname('Gusev');
        $this->repoCust->save($cust);

        $cust = $this->manObj->create(\Magento\Customer\Api\Data\CustomerInterface::class);
        $cust->setEmail('alex@flancer32.com');
        $cust->setFirstname('John');
        $cust->setLastname('Doe');
        $this->repoCust->save($cust);

        $output->writeln('<info>Command is completed.<info>');
    }

}
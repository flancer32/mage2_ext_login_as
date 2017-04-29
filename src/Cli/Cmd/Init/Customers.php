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
    extends \Flancer32\LoginAs\Cli\Cmd\Base
{
    /** @var \Magento\Customer\Api\CustomerRepositoryInterface */
    protected $repoCust;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Magento\Customer\Api\CustomerRepositoryInterface $repoCust
    ) {
        parent::__construct($manObj);
        $this->repoCust = $repoCust;
    }

    protected function configure()
    {
        parent::configure();
        $this->setName('fl32:init:customers');
        $this->setDescription("Create test customers for 'Flancer32_LoginAs' module.");
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
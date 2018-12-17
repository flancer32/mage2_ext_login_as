<?php

/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Flancer32\LoginAs\Cli\Cmd\Init;

/**
 * Create test customers for development deployment.
 */
class Customers
    extends \Flancer32\Base\App\Cli\Base
{
    private const DESC = 'Create test customers for \'Flancer32_LoginAs\' module.';
    private const NAME = 'fl32:init:customers';

    const DEF_CUST_01_EMAIL = 'alex@flancer64.com';
    const DEF_CUST_01_FIRST = 'Alex';
    const DEF_CUST_01_LAST = 'Gusev';
    const DEF_CUST_02_EMAIL = 'alex@flancer32.com';
    const DEF_CUST_02_FIRST = 'John';
    const DEF_CUST_02_LAST = 'Doe';

    /** @var \Magento\Framework\ObjectManagerInterface */
    private $manObj;

    /** @var \Magento\Customer\Api\CustomerRepositoryInterface */
    private $repoCust;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Magento\Customer\Api\CustomerRepositoryInterface\Proxy $repoCust
    ) {
        parent::__construct(self::NAME, self::DESC);
        $this->manObj = $manObj;
        $this->repoCust = $repoCust;
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output)
    {
        $output->writeln("<info>Command '" . $this->getName() . "':<info>");
        /** @var \Magento\Customer\Api\Data\CustomerInterface $cust */
        $cust = $this->manObj->create(\Magento\Customer\Api\Data\CustomerInterface::class);
        $cust->setEmail(self::DEF_CUST_01_EMAIL);
        $cust->setFirstname(self::DEF_CUST_01_FIRST);
        $cust->setLastname(self::DEF_CUST_01_LAST);
        $this->repoCust->save($cust);

        $cust = $this->manObj->create(\Magento\Customer\Api\Data\CustomerInterface::class);
        $cust->setEmail(self::DEF_CUST_02_EMAIL);
        $cust->setFirstname(self::DEF_CUST_02_FIRST);
        $cust->setLastname(self::DEF_CUST_02_LAST);
        $this->repoCust->save($cust);

        $output->writeln('<info>Command \'' . $this->getName() . '\' is completed.<info>');
    }

}
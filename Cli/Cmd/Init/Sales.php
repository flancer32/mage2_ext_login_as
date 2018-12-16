<?php

/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Flancer32\LoginAs\Cli\Cmd\Init;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Create sale order.
 */
class Sales
    extends \Flancer32\Base\App\Cli\Base
{

    private const DESC = 'Create test sale orders for \'Flancer32_LoginAs\' module.';
    private const NAME = 'fl32:init:sales';

    /** @var  \Flancer32\LoginAs\Cli\Cmd\Init\Sales\Create */
    protected $subCreate;

    public function __construct(
        \Flancer32\LoginAs\Cli\Cmd\Init\Sales\Create $aCreate
    ) {
        parent::__construct(self::NAME, self::DESC);
        $this->subCreate = $aCreate;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Create test sale orders for \'Flancer32_LoginAs\' module.<info>');
        $ctx = new \Magento\Framework\DataObject();
        $this->subCreate->exec($ctx);
        $output->writeln('<info>Command is completed.<info>');
    }

}
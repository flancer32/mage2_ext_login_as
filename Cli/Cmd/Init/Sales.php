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
    extends \Flancer32\LoginAs\Cli\Cmd\Base
{
    /** @var  \Flancer32\LoginAs\Cli\Cmd\Init\Sales\Create */
    protected $subCreate;

    public function __construct(
        \Flancer32\LoginAs\Cli\Cmd\Init\Sales\Create $subCreate
    ) {
        parent::__construct(self::class);
        $this->subCreate = $subCreate;
    }

    protected function configure()
    {
        parent::configure();
        $this->setName('fl32:init:sales');
        $this->setDescription("Create test sale orders for 'Flancer32_LoginAs' module.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Create test sale orders for \'Flancer32_LoginAs\' module.<info>');
        $this->checkAreaCode();
        $ctx = new \Flancer32\Lib\Data();
        $this->subCreate->exec($ctx);
        $output->writeln('<info>Command is completed.<info>');
    }

}
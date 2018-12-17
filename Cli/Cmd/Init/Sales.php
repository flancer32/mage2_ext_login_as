<?php

/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Flancer32\LoginAs\Cli\Cmd\Init;

/**
 * Create sale order.
 */
class Sales
    extends \Flancer32\Base\App\Cli\Base
{

    private const DESC = 'Create test sale orders for \'Flancer32_LoginAs\' module.';
    private const NAME = 'fl32:init:sales';

    /** @var  \Flancer32\LoginAs\Cli\Cmd\Init\Sales\A\Create */
    private $aCreate;

    public function __construct(
        \Flancer32\LoginAs\Cli\Cmd\Init\Sales\A\Create $aCreate
    ) {
        parent::__construct(self::NAME, self::DESC);
        $this->aCreate = $aCreate;
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output)
    {
        $output->writeln("<info>Command '" . $this->getName() . "':<info>");
        $this->checkAreaCode();
        $this->aCreate->exec();
        $output->writeln('<info>Command \'' . $this->getName() . '\' is completed.<info>');
    }

}
<?php

/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Flancer32\LoginAs\Cli\Cmd\Logs;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Clean up "Login As" logs.
 */
class Cleanup
    extends \Flancer32\Base\App\Cli\Base
{
    private const DESC = 'Clean up "Login As" logs older then XX days (Options: -d XX, default: from config).';
    private const NAME = 'fl32:logs:cleanup';
    private const OPT_DAYS_DEFAULT = '0';
    private const OPT_DAYS_NAME = 'days';
    private const OPT_DAYS_SHORTCUT = 'd';

    /** @var \Flancer32\LoginAs\Service\ICleanup */
    private $callCleanup;
    /** @var \Flancer32\LoginAs\Helper\Config */
    private $hlpConfig;

    public function __construct(
        \Flancer32\LoginAs\Helper\Config $hlpConfig,
        \Flancer32\LoginAs\Service\ICleanup $callCleanup
    ) {
        parent::__construct(self::NAME, self::DESC);
        $this->hlpConfig = $hlpConfig;
        $this->callCleanup = $callCleanup;
    }

    protected function configure()
    {
        parent::configure();
        $this->addOption(
            self::OPT_DAYS_NAME,
            self::OPT_DAYS_SHORTCUT,
            \Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL,
            "Number of days to leave in logs",
            self::OPT_DAYS_DEFAULT
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* parse and validate input data */
        $days = (int)$input->getOption(self::OPT_DAYS_NAME);
        if ($days <= 0) {
            $days = $this->hlpConfig->getLogsCleanupDaysOld();
        } elseif ($days < \Flancer32\LoginAs\Helper\Config::DEF_LOGS_CLEANUP_MIN_DAYS) {
            $days = \Flancer32\LoginAs\Helper\Config::DEF_LOGS_CLEANUP_MIN_DAYS;
        }
        $output->writeln("<info>Clean up \"Login As\" logs older then '$days' days.<info>");
        $this->checkAreaCode();
        $req = new \Flancer32\LoginAs\Service\Cleanup\Request();
        $req->daysToLeave = $days;
        /** @var \Flancer32\LoginAs\Service\Cleanup\Response $resp */
        $resp = $this->callCleanup->execute($req);
        $delActive = $resp->deletedActive;
        $delLog = $resp->deletedLog;
        $output->writeln("<info>Command is completed. Total '$delLog' log records and '$delActive' active records are deleted.<info>");
    }

}
<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Flancer32\LoginAs\Cron\Logs;

/**
 * Clean up "LoginAs" logs and active registry by cron.
 */
class Cleanup
{
    /** @var \Flancer32\LoginAs\Service\Cleanup */
    private $servCleanup;
    /** @var \Flancer32\LoginAs\Helper\Config */
    private $hlpConfig;
    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Flancer32\LoginAs\Helper\Config $hlpConfig,
        \Flancer32\LoginAs\Service\Cleanup $servCleanup
    ) {
        $this->logger = $logger;
        $this->hlpConfig = $hlpConfig;
        $this->servCleanup = $servCleanup;
    }

    public function execute()
    {
        $enabled = $this->hlpConfig->getLogsCleanupEnabled();
        if ($enabled) {
            $req = new \Flancer32\LoginAs\Service\Cleanup\Request();
            $resp = $this->servCleanup->execute($req);
            $delLog = $resp->deletedLog;
            $delTrans = $resp->deletedTransition;
            if ($delLog > 0) {
                $this->logger->warning("Total '$delLog' log and '$delTrans' transition records are cleaned from 'LoginAs' tables by cron.");
            }
        }
    }
}
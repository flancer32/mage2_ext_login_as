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
    /** @var \Flancer32\LoginAs\Service\ICleanup */
    protected $callCleanup;
    /** @var \Flancer32\LoginAs\Helper\Config */
    protected $hlpConfig;
    /** @var \Psr\Log\LoggerInterface */
    protected $logger;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Flancer32\LoginAs\Helper\Config $hlpConfig,
        \Flancer32\LoginAs\Service\ICleanup $callCleanup
    ) {
        $this->logger = $logger;
        $this->hlpConfig = $hlpConfig;
        $this->callCleanup = $callCleanup;
    }

    public function execute()
    {
        $enabled = $this->hlpConfig->getLogsCleanupEnabled();
        if ($enabled) {
            $req = new \Flancer32\LoginAs\Service\Cleanup\Request();
            $resp = $this->callCleanup->execute($req);
            $delAct = $resp->deletedActive;
            $delLog = $resp->deletedLog;
            if ($delLog > 0) {
                $this->logger->warning("Total '$delLog' log and '$delAct' active records are cleaned from 'LoginAs' tables by cron.");
            }
        }
    }
}
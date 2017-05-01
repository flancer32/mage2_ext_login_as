<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Flancer32\LoginAs\Cron\Logs;

/**
 * Clean up "LoginAs" logs by cron.
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
            $deleted = $resp->deleted;
            if ($deleted > 0) {
                $this->logger->warning("Total '$deleted' records are cleaned from 'LoginAs' log by cron.");
            }
        }
    }
}
<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Flancer32\LoginAs\Service;

use \Flancer32\LoginAs\Repo\Data\Entity\Log as ELog;
use Flancer32\LoginAs\Repo\Data\Entity\Active as EActive;

class Cleanup
    implements ICleanup
{
    /** @var  \Magento\Framework\DB\Adapter\AdapterInterface */
    protected $conn;
    /** @var \Flancer32\LoginAs\Helper\Config */
    protected $hlpConfig;
    /** @var \Psr\Log\LoggerInterface */
    protected $logger;
    /** @var  \Flancer32\LoginAs\Repo\Entity\IActive */
    protected $repoActive;
    /** @var \Flancer32\LoginAs\Repo\Entity\ILog */
    protected $repoLog;
    /** @var \Magento\Framework\App\ResourceConnection */
    protected $resource;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\ResourceConnection $resource,
        \Flancer32\LoginAs\Helper\Config $hlpConfig,
        \Flancer32\LoginAs\Repo\Entity\IActive $repoActive,
        \Flancer32\LoginAs\Repo\Entity\ILog $repoLog
    ) {
        $this->logger = $logger;
        $this->resource = $resource;
        $this->conn = $resource->getConnection();
        $this->hlpConfig = $hlpConfig;
        $this->repoActive = $repoActive;
        $this->repoLog = $repoLog;
    }

    /**
     * Clean up active registry records older than 3 days.
     */
    protected function cleanActive()
    {
        $time = strtotime("-3 days");
        $datePrint = date('Y-m-d H:i:s', $time);
        $this->logger->debug("Clean up 'LoginAs' active registry older than $datePrint.");
        /* remove active registry records where KEY is less then 'YYYYMMDDHHMMSS' */
        $date = date('YmdHis', $time);
        $quoted = $this->conn->quote($date);
        /* add backquotes for `key` named attribute */
        $expr = 'STRCMP(`' . EActive::A_KEY . '`, ' . $quoted . ') < 0'; // '???' < 20170509010100
        $where = new \Flancer32\Lib\Repo\Repo\Query\Expression($expr);
        $result = $this->repoActive->delete($where);
        return $result;
    }

    /**
     * Clean up log records.
     *
     * @param int $days clean logs older than $days
     * @return int number of cleaned records
     */
    protected function cleanLog($days)
    {
        $time = strtotime("-$days day");
        $date = date('Y-m-d H:i:s', $time);
        $this->logger->debug("Clean up 'LoginAs' logs older than $date.");
        /* remove old logs */
        $quoted = $this->conn->quote($date);
        $where = ELog::A_DATE . '<=' . $quoted;
        $result = $this->repoLog->delete($where);
        $this->logger->debug("Total '$result' records are cleand up from 'LoginAs' log.");
        return $result;
    }

    public function execute(\Flancer32\LoginAs\Service\Cleanup\Request $request)
    {
        $result = new \Flancer32\LoginAs\Service\Cleanup\Response();
        $days = (int)$request->daysToLeave;
        if ($days <= 0) {
            $days = $this->hlpConfig->getLogsCleanupDaysOld();
        } elseif ($days < \Flancer32\LoginAs\Helper\Config::DEF_LOGS_CLEANUP_MIN_DAYS) {
            $days = \Flancer32\LoginAs\Helper\Config::DEF_LOGS_CLEANUP_MIN_DAYS;
        }
        $result->deletedActive = $this->cleanActive();
        $result->deletedLog = $this->cleanLog($days);
        return $result;
    }
}
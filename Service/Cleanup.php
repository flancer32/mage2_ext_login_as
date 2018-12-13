<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Flancer32\LoginAs\Service;

use Flancer32\Base\App\Repo\Query\Expression as AnExpress;
use Flancer32\LoginAs\Api\Repo\Data\Log as ELog;
use Flancer32\LoginAs\Api\Repo\Data\Transition as ETrans;
use Flancer32\LoginAs\Helper\Config as HlpCfg;
use Flancer32\LoginAs\Service\Cleanup\Request as ARequest;
use Flancer32\LoginAs\Service\Cleanup\Response as AResponse;

class Cleanup
    implements ICleanup
{
    /** @var  \Magento\Framework\DB\Adapter\AdapterInterface */
    protected $conn;
    /** @var \Flancer32\LoginAs\Api\Repo\Dao\Log */
    protected $daoLog;
    /** @var  \Flancer32\LoginAs\Api\Repo\Dao\Transition */
    protected $daoTrans;
    /** @var \Flancer32\LoginAs\Helper\Config */
    protected $hlpConfig;
    /** @var \Psr\Log\LoggerInterface */
    protected $logger;
    /** @var \Magento\Framework\App\ResourceConnection */
    protected $resource;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\ResourceConnection $resource,
        \Flancer32\LoginAs\Helper\Config $hlpConfig,
        \Flancer32\LoginAs\Api\Repo\Dao\Transition $daoTrans,
        \Flancer32\LoginAs\Api\Repo\Dao\Log $repoLog
    ) {
        $this->logger = $logger;
        $this->resource = $resource;
        $this->conn = $resource->getConnection();
        $this->hlpConfig = $hlpConfig;
        $this->daoTrans = $daoTrans;
        $this->daoLog = $repoLog;
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
        $expr = 'STRCMP(`' . ETrans::KEY . '`, ' . $quoted . ') < 0'; // '???' < 20170509010100
        $where = new AnExpress($expr);
        $result = $this->daoTrans->deleteSet($where);
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
        $where = ELog::DATE . '<=' . $quoted;
        $result = $this->daoLog->deleteSet($where);
        $this->logger->debug("Total '$result' records are cleand up from 'LoginAs' log.");
        return $result;
    }

    public function execute($request)
    {
        assert($request instanceof ARequest);
        $days = (int)$request->daysToLeave;
        if ($days <= 0) {
            $days = $this->hlpConfig->getLogsCleanupDaysOld();
        } elseif ($days < HlpCfg::DEF_LOGS_CLEANUP_MIN_DAYS) {
            $days = HlpCfg::DEF_LOGS_CLEANUP_MIN_DAYS;
        }
        $result = new AResponse();
        $result->deletedActive = $this->cleanActive();
        $result->deletedLog = $this->cleanLog($days);
        return $result;
    }
}
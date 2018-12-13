<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Flancer32\LoginAs\Repo\Query\Grid\Logged;

use Flancer32\Base\App\Repo\Query\Expression as AnExpress;
use Flancer32\LoginAs\Api\Repo\Dao\Log as DaoLog;
use Flancer32\LoginAs\Api\Repo\Data\Log as ELog;
use Flancer32\LoginAs\Config as Cfg;

class Builder
{
    /**
     * Tables aliases.
     */
    const AS_TBL_ADMIN = 'adm'; // default connection
    const AS_TBL_CUSTOMER = 'cust';
    const AS_TBL_LOG = 'log';

    /**
     * Attributes aliases.
     */
    const A_ADMIN = 'Admin';
    const A_CUSTOMER = 'Customer';
    const A_DATE_LOGGED = 'DateLogged';
    const A_ID = 'Id';
    const A_ID_ADMIN = 'AdminId';
    const A_ID_CUST = 'CustId';
    const A_TOTAL = 'Total';

    /** @var  \Magento\Framework\DB\Adapter\AdapterInterface */
    protected $conn;
    /** @var \Magento\Framework\App\ResourceConnection */
    protected $resource;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->resource = $resource;
        $this->conn = $resource->getConnection();
    }

    public function getCountQuery()
    {
        $result = $this->getSelectQuery();
        $columns = $result->getPart('columns');
        $exp = 'COUNT(' . self::AS_TBL_LOG . '.' . ELog::ID . ')';
        $expTotal = new AnExpress($exp);
        $colTotal = [
            self::AS_TBL_LOG,
            $expTotal,
            self::A_TOTAL
        ];
        /* Total column shold be the first */
        array_unshift($columns, $colTotal);
        $result->setPart('columns', $columns);
        return $result;
    }

    public function getSelectQuery()
    {
        $asAdm = self::AS_TBL_ADMIN;
        $asCust = self::AS_TBL_CUSTOMER;
        $asLog = self::AS_TBL_LOG;
        $result = $this->conn->select();
        /* SELECT FROM fl32_loginas_log */
        $tbl = $this->resource->getTableName(DaoLog::ENTITY_NAME);
        $as = $asLog;
        $cols = [
            self::A_ID => ELog::ID,
            self::A_ID_CUST => ELog::CUSTOMER_REF,
            self::A_ID_ADMIN => ELog::USER_REF,
            self::A_DATE_LOGGED => ELog::DATE
        ];
        $result->from([$as => $tbl], $cols);
        /* LEFT JOIN customer_entity */
        $tbl = $this->resource->getTableName(Cfg::ENTITY_CUSTOMER);
        $as = $asCust;
        $on = $asCust . '.' . Cfg::E_CUSTOMER_A_ENTITY_ID . '=' . $asLog . '.' . ELog::CUSTOMER_REF;
        $first = $as . '.' . Cfg::E_CUSTOMER_A_FIRSTNAME;
        $last = $as . '.' . Cfg::E_CUSTOMER_A_LASTNAME;
        $email = $as . '.' . Cfg::E_CUSTOMER_A_EMAIL;
        $expValue = "CONCAT($first, ' ', $last, ' <', $email, '>')";
        $exp = new AnExpress($expValue);
        $cols = [
            self::A_ID_CUST => Cfg::E_CUSTOMER_A_ENTITY_ID,
            self::A_CUSTOMER => $exp
        ];
        $result->joinLeft([$as => $tbl], $on, $cols);
        /* LEFT JOIN admin_user */
        $tbl = $this->resource->getTableName(Cfg::ENTITY_ADMIN_USER);
        $as = $asAdm;
        $on = $asAdm . '.' . Cfg::E_ADMIN_USER_A_USER_ID . '=' . $asLog . '.' . ELog::USER_REF;
        $first = $as . '.' . Cfg::E_ADMIN_USER_A_FIRSTNAME;
        $last = $as . '.' . Cfg::E_ADMIN_USER_A_LASTNAME;
        $email = $as . '.' . Cfg::E_ADMIN_USER_A_EMAIL;
        $expValue = "CONCAT($first, ' ', $last, ' <', $email, '>')";
        $exp = new AnExpress($expValue);
        $cols = [
            self::A_ID_ADMIN => Cfg::E_ADMIN_USER_A_USER_ID,
            self::A_ADMIN => $exp
        ];
        $result->joinLeft([$as => $tbl], $on, $cols);
        return $result;
    }

}
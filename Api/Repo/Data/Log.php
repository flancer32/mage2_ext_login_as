<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Flancer32\LoginAs\Api\Repo\Data;

/**
 * Log for login events.
 */
class Log
    extends \Magento\Framework\DataObject
{
    const CUSTOMER_REF = 'customer_ref';
    const DATE = 'date';
    const ID = 'id';
    const USER_REF = 'user_ref';

    /** @return int */
    public function getCustomerRef()
    {
        $result = (int)parent::getData(self::CUSTOMER_REF);
        return $result;
    }

    /** @return string */
    public function getDate()
    {
        $result = (string)parent::getData(self::DATE);
        return $result;
    }

    /** @return int */
    public function getId()
    {
        $result = (int)parent::getData(self::ID);
        return $result;
    }

    /** @return int */
    public function getUserRef()
    {
        $result = (int)parent::getData(self::USER_REF);
        return $result;
    }


    /**
     * @param int $data
     * @return void
     */
    public function setCustomerRef($data)
    {
        parent::setData(self::CUSTOMER_REF, $data);
    }

    /**
     * @param string $data
     * @return void
     */
    public function setDate($data)
    {
        parent::setData(self::DATE, $data);
    }

    /**
     * @param int $data
     * @return void
     */
    public function setId($data)
    {
        parent::setData(self::ID, $data);
    }

    /**
     * @param int $data
     * @return void
     */
    public function setUserRef($data)
    {
        parent::setData(self::USER_REF, $data);
    }
}
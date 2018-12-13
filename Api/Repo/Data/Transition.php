<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Flancer32\LoginAs\Api\Repo\Data;

/**
 * Registry to save user-to-customer transitions between admin & front.
 */
class Transition
    extends \Magento\Framework\DataObject
{
    const CUSTOMER_REF = 'customer_ref';
    const KEY = 'key';
    const USER_REF = 'user_ref';

    /** @return int */
    public function getCustomerRef()
    {
        $result = (int)parent::getData(self::CUSTOMER_REF);
        return $result;
    }

    /** @return string */
    public function getKey()
    {
        $result = (string)parent::getData(self::KEY);
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
    public function setKey($data)
    {
        parent::setData(self::KEY, $data);
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
<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Flancer32\LoginAs\Repo\Data\Entity;


class Active
    extends \Flancer32\Lib\Repo\Repo\Data\Def\Entity
    implements \Flancer32\Lib\Repo\Repo\Data\IEntity
{
    const A_CUST_REF = 'customer_ref';
    const A_ID = 'id';
    const A_USER_REF = 'user_ref';
    const ENTITY_NAME = 'fl32_loginas_act';


    public function getPrimaryKeyAttrs()
    {
        return [self::A_ID];
    }

}
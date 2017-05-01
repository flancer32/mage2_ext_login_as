<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Flancer32\LoginAs\Repo\Entity;


class Active
    extends \Flancer32\Lib\Repo\Repo\Def\Entity
    implements \Flancer32\LoginAs\Repo\Entity\IActive
{
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Flancer32\Lib\Repo\Repo\IGeneric $repoGeneric
    ) {
        parent::__construct(
            $resource,
            $repoGeneric,
            \Flancer32\LoginAs\Repo\Data\Entity\Active::class
        );
    }
}
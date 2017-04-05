<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Flancer32\LoginAs\Repo\Entity;

interface IActive
    extends \Flancer32\Lib\Repo\Repo\ICrud
{
    /**
     * @param \Flancer32\LoginAs\Repo\Data\Entity\Active|array $data
     * @return int
     */
    public function create($data);

    /**
     * @param int|array $id
     * @return \Flancer32\LoginAs\Repo\Data\Entity\Active|bool
     */
    public function getById($id);

}
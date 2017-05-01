<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Flancer32\LoginAs\Repo\Entity;

interface ILog
    extends \Flancer32\Lib\Repo\Repo\ICrud
{
    /**
     * @param \Flancer32\LoginAs\Repo\Data\Entity\Log|array $data
     * @return int
     */
    public function create($data);

    /**
     * @param int|array $id
     * @return \Flancer32\LoginAs\Repo\Data\Entity\Log|bool
     */
    public function getById($id);

}
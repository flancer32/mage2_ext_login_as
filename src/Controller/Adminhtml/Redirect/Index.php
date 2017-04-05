<?php

namespace Flancer32\LoginAs\Controller\Adminhtml\Redirect;

/**
 * Register admin user's redirection request in 'active' registry.
 */
class Index
    extends \Magento\Backend\App\Action
{
    const REQ_PARAM_ID = 'id';
    /** @var \Flancer32\LoginAs\Repo\Entity\IActive */
    protected $repoActive;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Flancer32\LoginAs\Repo\Entity\IActive $repoActive
    ) {
        parent::__construct($context);
        $this->repoActive = $repoActive;
    }

    public function execute()
    {
        /* collect redirection parameters and registry activity */
        $custId = $this->_request->getParam(self::REQ_PARAM_ID);
        $user = $this->_auth->getUser();
        $userId = $user->getUserId();
        $key = $this->generateKey($custId, $userId);
        $entity = new \Flancer32\LoginAs\Repo\Data\Entity\Active();
        $entity->setCustomerRef($custId);
        $entity->setUserRef($userId);
        $entity->setKey($key);
        $actId = $this->repoActive->create($entity);
        $this->repoActive->getById($actId);
        /* redirect to frontend using search key for the activity */

    }

    /**
     * Generate unique key to get redirection data as customer.
     *
     * @param $custId
     * @param $userId
     * @return string
     */
    protected function generateKey($custId, $userId)
    {
        $now = date('YmdHis');
        $source = "cust:$custId;user:$userId;at:$now";
        $result = $now . md5($source);
        return $result;
    }
}
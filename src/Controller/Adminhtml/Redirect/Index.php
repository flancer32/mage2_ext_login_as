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
        $custId = $this->_request->getParam(self::REQ_PARAM_ID);
        $user = $this->_auth->getUser();
        $userId = $user->getUserId();
        $entity = new \Flancer32\LoginAs\Repo\Data\Entity\Active();
        $entity->setCustomerRef($custId);
        $entity->setUserRef($userId);
        $actId = $this->repoActive->create($entity);
        $saved = $this->repoActive->getById($actId);
    }
}
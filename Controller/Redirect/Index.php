<?php

namespace Flancer32\LoginAs\Controller\Redirect;

use Flancer32\LoginAs\Api\Repo\Data\Log as ELog;

class Index
    extends \Magento\Framework\App\Action\Action
{
    public const REQ_PARAM_KEY = 'key';
    /** @var \Flancer32\LoginAs\Api\Repo\Dao\Log */
    private $daoLog;
    /** @var \Flancer32\LoginAs\Api\Repo\Dao\Transition */
    private $daoTrans;
    /** @var \Magento\Customer\Api\CustomerRepositoryInterface */
    private $repoCust;
    /** @var \Magento\Customer\Model\Session */
    private $session;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $session,
        \Magento\Customer\Api\CustomerRepositoryInterface $repoCust,
        \Flancer32\LoginAs\Api\Repo\Dao\Transition $daoTrans,
        \Flancer32\LoginAs\Api\Repo\Dao\Log $daoLog
    ) {
        parent::__construct($context);
        $this->session = $session;
        $this->repoCust = $repoCust;
        $this->daoTrans = $daoTrans;
        $this->daoLog = $daoLog;
    }

    public function execute()
    {
        /* collect redirection parameters */
        $key = $this->_request->getParam(self::REQ_PARAM_KEY);
        /* get active redirection and extract customer attributes */
        $entity = $this->daoTrans->getOne($key);
        $custId = $entity->getCustomerRef();
        $userId = $entity->getUserRef();
        /* load customer and initiate session */
        $customer = $this->repoCust->getById($custId);
        $this->session->setCustomerDataAsLoggedIn($customer);
        /* remove used redirection from active registry */
        $this->daoTrans->deleteOne($key);
        /* log 'login as' event */
        $date = gmdate('Y-m-d H:i:s');
        $local = date('Y-m-d H:i:s');
        $log = new ELog();
        $log->setCustomerRef($custId);
        $log->setUserRef($userId);
        $log->setDate($date);
        $this->daoLog->create($log);
        /* redirect authenticated customer to the homepage */
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('customer/account');
        return $resultRedirect;
    }
}
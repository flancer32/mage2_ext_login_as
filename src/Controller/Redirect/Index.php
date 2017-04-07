<?php

namespace Flancer32\LoginAs\Controller\Redirect;

/**
 *
 */
class Index
    extends \Magento\Framework\App\Action\Action
{
    const REQ_PARAM_KEY = 'key';
    /** @var \Magento\Framework\Controller\Result\RedirectFactory */
    protected $factRedirect;
    /** @var \Flancer32\LoginAs\Repo\Entity\IActive */
    protected $repoActive;
    /** @var \Magento\Customer\Api\CustomerRepositoryInterface */
    protected $repoCust;
    /** @var \Flancer32\LoginAs\Repo\Entity\ILog */
    protected $repoLog;
    /** @var \Magento\Customer\Model\Session */
    protected $session;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\RedirectFactory $factRedirect,
        \Magento\Customer\Api\CustomerRepositoryInterface $repoCust,
        \Flancer32\LoginAs\Repo\Entity\IActive $repoActive,
        \Flancer32\LoginAs\Repo\Entity\ILog $repoLog,
        \Magento\Customer\Model\Session $session
    ) {
        parent::__construct($context);
        $this->factRedirect = $factRedirect;
        $this->repoCust = $repoCust;
        $this->repoActive = $repoActive;
        $this->repoLog = $repoLog;
        $this->session = $session;
    }

    public function execute()
    {
        /* collect redirection parameters */
        $key = $this->_request->getParam(self::REQ_PARAM_KEY);
        /* get active redirection and extract customer attributes */
        $entity = $this->repoActive->getById($key);
        $custId = $entity->getCustomerRef();
        $userId = $entity->getUserRef();
        /* load customer and initiate session */
        $customer = $this->repoCust->getById($custId);
        $this->session->setCustomerDataAsLoggedIn($customer);
        $this->session->regenerateId();
        /* remove used redirection from active registry */
        $this->repoActive->deleteById($key);
        /* log 'login as' event */
        $date = gmdate('Y-m-d H:i:s');
        $local = date('Y-m-d H:i:s');
        $log = new \Flancer32\LoginAs\Repo\Data\Entity\Log();
        $log->setCustomerRef($custId);
        $log->setUserRef($userId);
        $log->setDate($date);
        $this->repoLog->create($log);
        /* redirect authenticated customer to the homepage */
        $resultRedirect = $this->factRedirect->create();
        $resultRedirect->setPath('customer/account');
        return $resultRedirect;
    }
}
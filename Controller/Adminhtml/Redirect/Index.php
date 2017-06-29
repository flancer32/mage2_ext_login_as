<?php

namespace Flancer32\LoginAs\Controller\Adminhtml\Redirect;

use Flancer32\LoginAs\Config as Cfg;

/**
 * Register admin user's redirection request in 'active' registry.
 */
class Index
    extends \Magento\Backend\App\Action
{
    const REQ_PARAM_ID = 'id';
    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $manStore;
    /** @var \Flancer32\LoginAs\Repo\Entity\IActive */
    protected $repoActive;
    /** @var \Magento\Framework\Url */
    protected $hlpUrl;
    /** @var \Magento\Customer\Api\CustomerRepositoryInterface */
    protected $repoCust;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $manStore,
        \Magento\Framework\Url $factUrl,
        \Magento\Customer\Api\CustomerRepositoryInterface $repoCust,
        \Flancer32\LoginAs\Repo\Entity\IActive $repoActive
    ) {
        parent::__construct($context);
        $this->manStore = $manStore;
        $this->hlpUrl = $factUrl;
        $this->repoCust = $repoCust;
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
        $this->repoActive->create($entity);
        $saved = $this->repoActive->getById($key);
        /* redirect to frontend using search key for the activity */
        $keySaved = $saved->getKey();
        /* redirect admin user to the front redirector */
        $resultRedirect = $this->resultRedirectFactory->create();
        $route = Cfg::ROUTE_NAME_FRONT_LOGINAS . '/redirect/';
        /* get store ID for the customer or use default store */
        $customer = $this->repoCust->getById($custId);
        $storeId = $customer->getStoreId();
        $this->hlpUrl->setScope($storeId);
        $url = $this->hlpUrl->getUrl($route, [\Flancer32\LoginAs\Controller\Redirect\Index::REQ_PARAM_KEY => $keySaved]);
        $resultRedirect->setUrl($url);
        return $resultRedirect;
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
        $rand = rand();
        $source = "cust:$custId;user:$userId;at:$now;rand:$rand";
        $result = $now . md5($source);
        return $result;
    }
}
<?php

namespace Flancer32\LoginAs\Controller\Adminhtml\Redirect;

use Flancer32\LoginAs\Api\Repo\Data\Transition as ETrans;
use Flancer32\LoginAs\Config as Cfg;
use Flancer32\LoginAs\Controller\Redirect\Index as CtrlFront;

/**
 * Register admin user's redirection request in 'transition' registry.
 */
class Index
    extends \Magento\Backend\App\Action
{
    public const REQ_PARAM_ID = 'id';

    /** @var \Flancer32\LoginAs\Api\Repo\Dao\Transition */
    private $daoTrans;
    /** @var \Magento\Store\Model\StoreManagerInterface */
    private $manStore;
    /** @var \Magento\Customer\Api\CustomerRepositoryInterface */
    private $repoCust;
    /** @var \Magento\Framework\Url */
    private $url;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Url $url,
        \Magento\Store\Model\StoreManagerInterface $manStore,
        \Magento\Customer\Api\CustomerRepositoryInterface $repoCust,
        \Flancer32\LoginAs\Api\Repo\Dao\Transition $daoTrans
    ) {
        parent::__construct($context);
        $this->url = $url;
        $this->manStore = $manStore;
        $this->repoCust = $repoCust;
        $this->daoTrans = $daoTrans;
    }

    public function execute()
    {
        /* collect redirection parameters and registry activity */
        $custId = $this->_request->getParam(self::REQ_PARAM_ID);
        /** @var \Magento\Backend\Model\Auth\Credential\StorageInterface $user */
        $user = $this->_auth->getUser();
        $userId = $user->getUserId();
        $key = $this->generateKey($custId, $userId);
        $entity = new ETrans();
        $entity->setCustomerRef($custId);
        $entity->setUserRef($userId);
        $entity->setKey($key);
        $this->daoTrans->create($entity);
        $saved = $this->daoTrans->getOne($key);
        /* redirect to frontend using search key for the activity */
        $keySaved = $saved->getKey();
        /* redirect admin user to the front redirector */
        $resultRedirect = $this->resultRedirectFactory->create();
        $route = Cfg::ROUTE_NAME_FRONT_LOGINAS . '/redirect/';
        /* get store ID for the customer or use default store */
        $customer = $this->repoCust->getById($custId);
        $storeId = $customer->getStoreId();
        if ($storeId == Cfg::STORE_ID_ADMIN) $storeId = Cfg::STORE_ID_DEFAULT;
        $url = $this->url;
        $url->setScope($storeId);
        $goto = $url->getUrl($route, [CtrlFront::REQ_PARAM_KEY => $keySaved]);
        $resultRedirect->setUrl($goto);
        return $resultRedirect;
    }

    /**
     * Generate unique key to get redirection data from frontend as customer.
     *
     * @param int $custId
     * @param int $userId
     * @return string
     */
    private function generateKey($custId, $userId)
    {
        $now = date('YmdHis');
        $rand = rand();
        $source = "cust:$custId;user:$userId;at:$now;rand:$rand";
        $result = $now . md5($source);
        return $result;
    }
}
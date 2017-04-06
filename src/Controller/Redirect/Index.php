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
    /** @var \Magento\Customer\Model\Session */
    protected $session;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\RedirectFactory $factRedirect,
        \Magento\Customer\Api\CustomerRepositoryInterface $repoCust,
        \Flancer32\LoginAs\Repo\Entity\IActive $repoActive,
        \Magento\Customer\Model\Session $session
    ) {
        parent::__construct($context);
        $this->factRedirect = $factRedirect;
        $this->repoCust = $repoCust;
        $this->repoActive = $repoActive;
        $this->session = $session;
    }

    public function execute()
    {
        /* collect redirection parameters */
        $key = $this->_request->getParam(self::REQ_PARAM_KEY);
        /* get active redirection and extract customer attributes */
        $entity = $this->repoActive->getById($key);
        $custId = $entity->getCustomerRef();
        /* load customer and initiate session */
        $customer = $this->repoCust->getById($custId);
        $this->session->setCustomerDataAsLoggedIn($customer);
        $this->session->regenerateId();
        /* remove used redirection from active registry */
        $this->repoActive->deleteById($key);
        /* redirect authenticated customer to the homepage */
        $resultRedirect = $this->factRedirect->create();
        $resultRedirect->setPath('customer/account');
        return $resultRedirect;
    }
}
<?php

namespace Flancer32\LoginAs\Controller\Redirect;

/**
 *
 */
class Index
    extends \Magento\Framework\App\Action\Action
{
    const REQ_PARAM_KEY = 'key';
    /** @var \Flancer32\LoginAs\Repo\Entity\IActive */
    protected $repoActive;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Flancer32\LoginAs\Repo\Entity\IActive $repoActive
    )
    {
        parent::__construct($context);
        $this->repoActive = $repoActive;
    }

    public function execute()
    {
        /* collect redirection parameters and ... */
        $key = $this->_request->getParam(self::REQ_PARAM_KEY);
        $entity = $this->repoActive->getById($key);
    }
}
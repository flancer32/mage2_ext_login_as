<?php

/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Flancer32\LoginAs\Plugin\Sales\Ui\Component\Listing\Column;

use Flancer32\LoginAs\Config as Cfg;

/**
 * Add 'Login As' action to Sales grid.
 */
class ViewAction
{
    /** @var \Magento\Framework\AuthorizationInterface */
    protected $authorization;
    /** @var \Flancer32\LoginAs\Helper\Config */
    protected $hlpCfg;
    /** @var \Magento\Framework\UrlInterface */
    protected $url;

    public function __construct(
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\AuthorizationInterface $authorization,
        \Flancer32\LoginAs\Helper\Config $hlpCfg
    ) {
        $this->url = $url;
        $this->authorization = $authorization;
        $this->hlpCfg = $hlpCfg;
    }

    public function afterPrepareDataSource(
        \Magento\Sales\Ui\Component\Listing\Column\ViewAction $subject,
        $result
    ) {
        if (
            isset($result['data']) &&
            isset($result['data']['items']) &&
            is_array($result['data']['items'])
        ) {
            /* check ACL & store configuration */
            $isAllowed = $this->authorization->isAllowed(Cfg::ACL_RULE_LOGIN_AS);
            $isConfigured = $this->hlpCfg->getControlsSalesOrdersGridAction();
            if ($isAllowed && $isConfigured) {
                foreach ($result['data']['items'] as &$item) {
                    $entityId = $item['customer_id'];
                    $url = $this->getLoginAsUrl($entityId);
                    $actions = isset($item['actions']) ? $item['actions'] : [];
                    $actions['loginas'] = [
                        'href' => $this->url->getUrl($url),
                        'label' => __('Login As'),
                        'target' => '_blank'
                    ];
                    $item['actions'] = $actions;
                }
            }
        }
        return $result;
    }

    public function getLoginAsUrl($id)
    {
        $route = Cfg::ROUTE_NAME_ADMIN_LOGINAS . '/redirect/';
        return $this->url->getUrl(
            $route,
            [\Flancer32\LoginAs\Controller\Adminhtml\Redirect\Index::REQ_PARAM_ID => $id]
        );
    }
}
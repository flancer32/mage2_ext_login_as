<?php

/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Flancer32\LoginAs\Plugin\Customer\Ui\Component\Listing\Column;

use Flancer32\LoginAs\Config as Cfg;

/**
 * Add 'Login As' action to Customers grid.
 */
class Actions
{
    /** @var \Magento\Framework\UrlInterface */
    protected $url;

    public function __construct(
        \Magento\Framework\UrlInterface $url
    ) {
        $this->url = $url;

    }

    public function afterPrepareDataSource(
        \Magento\Customer\Ui\Component\Listing\Column\Actions $subject,
        $result
    ) {
        if (
            isset($result['data']) &&
            isset($result['data']['items']) &&
            is_array($result['data']['items'])
        ) {
            foreach ($result['data']['items'] as &$item) {
                $entityId = $item['entity_id'];
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
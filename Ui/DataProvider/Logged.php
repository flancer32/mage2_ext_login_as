<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Flancer32\LoginAs\Ui\DataProvider;


class Logged
    extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    /** @var  \Magento\Framework\DB\Adapter\AdapterInterface */
    private $conn;
    /** @var \Flancer32\Base\App\Ui\DataProvider\Adapter\ApiSearchCriteria */
    private $hlpAdpClauses;
    /** @var \Flancer32\Base\App\Repo\Query\ClauseSet\Processor */
    private  $hlpClauseProc;
    /** @var \Flancer32\LoginAs\Repo\Query\Grid\Logged\Builder */
    private  $qGrid;
    /** @var \Magento\Framework\App\ResourceConnection */
    private  $resource;

    public function __construct(
        $name,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Api\Search\ReportingInterface $reporting,
        \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Framework\UrlInterface $url,
        \Flancer32\Base\App\Ui\DataProvider\Adapter\ApiSearchCriteria $hlpAdpClauses,
        \Flancer32\Base\App\Repo\Query\ClauseSet\Processor $hlpClauseProc,
        \Flancer32\LoginAs\Repo\Query\Grid\Logged\Builder $qGrid
    ) {
        $primaryFieldName = 'id';
        $requestFieldName = 'id';
        $meta = [];
        $updateUrl = $url->getRouteUrl('mui/index/render');
        $data = [
            'config' => [
                'component' => 'Magento_Ui/js/grid/provider',
                'update_url' => $updateUrl
            ]
        ];
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data);
        $this->resource = $resource;
        $this->conn = $resource->getConnection();
        $this->hlpAdpClauses = $hlpAdpClauses;
        $this->hlpClauseProc = $hlpClauseProc;
        $this->qGrid = $qGrid;
    }

    public function getData()
    {
        /* Magento API criteria */
        $criteria = $this->getSearchCriteria();
        $clauses = $this->hlpAdpClauses->getClauseSet($criteria);

        $qTotal = $this->qGrid->getCountQuery();
        $this->hlpClauseProc->exec($qTotal, $clauses, true);
        $totals = $this->conn->fetchOne($qTotal);

        $qItems = $this->qGrid->getSelectQuery();
        $this->hlpClauseProc->exec($qItems, $clauses);
        $items = $this->conn->fetchAll($qItems);
        $result = [
            'items' => $items,
            'totalRecords' => $totals
        ];
        return $result;
    }

}
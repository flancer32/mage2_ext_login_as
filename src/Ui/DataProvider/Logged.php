<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Flancer32\LoginAs\Ui\DataProvider;


class Logged
    extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    /** @var  \Magento\Framework\DB\Adapter\AdapterInterface */
    protected $conn;
    /** @var \Flancer32\Lib\Repo\Helper\Data\Adapter\ClauseSet\ApiSearchCriteria */
    protected $hlpAdpClauses;
    /** @var \Flancer32\Lib\Repo\Helper\Repo\Query\ClauseSet\Processor */
    protected $hlpClauseProc;
    /** @var \Flancer32\LoginAs\Repo\Query\Grid\Logged\Builder */
    protected $qbldGrid;
    /** @var \Magento\Framework\App\ResourceConnection */
    protected $resource;

    public function __construct(
        $name,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Api\Search\ReportingInterface $reporting,
        \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Framework\UrlInterface $url,
        \Flancer32\Lib\Repo\Helper\Data\Adapter\ClauseSet\ApiSearchCriteria $hlpAdpClauses,
        \Flancer32\Lib\Repo\Helper\Repo\Query\ClauseSet\Processor $hlpClauseProc,
        \Flancer32\LoginAs\Repo\Query\Grid\Logged\Builder $qbldGrid
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
        $this->qbldGrid = $qbldGrid;
    }

    public function getData()
    {
        /* Magento API criteria */
        $criteria = $this->getSearchCriteria();
        $clauses = $this->hlpAdpClauses->getClauseSet($criteria);


        $qTotal = $this->qbldGrid->getCountQuery();
        $this->hlpClauseProc->exec($qTotal, $clauses);
        $totals = $this->conn->fetchOne($qTotal);

        $qItems = $this->qbldGrid->getSelectQuery();
        $this->hlpClauseProc->exec($qItems, $clauses);
        $items = $this->conn->fetchAll($qItems);
        $result = [
            'items' => $items,
            'totalRecords' => $totals
        ];
        return $result;
    }

}
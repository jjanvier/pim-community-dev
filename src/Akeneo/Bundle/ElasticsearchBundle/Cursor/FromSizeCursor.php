<?php

namespace Akeneo\Bundle\ElasticsearchBundle\Cursor;

use Akeneo\Bundle\ElasticsearchBundle\Client;
use Akeneo\Component\StorageUtils\Cursor\CursorInterface;
use Akeneo\Component\StorageUtils\Repository\CursorableRepositoryInterface;

/**
 * Bounded cursor to iterate over items where a start and a limit are defined.
 * Internally, this is implemented with the from/size pagination.
 * {@see https://www.elastic.co/guide/en/elasticsearch/reference/5.x/search-request-from-size.html}
 *
 * @author    Julien Janvier <jjanvier@akeneo.com>
 * @author    Marie Bochu <marie.bochu@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class FromSizeCursor extends AbstractCursor implements CursorInterface
{
    /** @var int */
    protected $initialFrom;

    /** @var int */
    protected $from;

    /** @var int */
    protected $limit;

    /** @var int */
    protected $to;

    /** @var int */
    protected $fetchedItemsCount;
    /**
     * @var CursorableRepositoryInterface
     */
    private $productModelRepository;

    /**
     * @param Client                        $esClient
     * @param CursorableRepositoryInterface $repository
     * @param array                         $esQuery
     * @param string                        $indexType
     * @param int                           $pageSize
     * @param int                           $limit
     * @param int                           $from
     */
    public function __construct(
        Client $esClient,
        CursorableRepositoryInterface $repository,
        CursorableRepositoryInterface $productModelRepository,
        array $esQuery,
        $indexType,
        $pageSize,
        $limit,
        $from = 0
    ) {
        $this->esClient = $esClient;
        $this->repository = $repository;
        $this->productModelRepository = $productModelRepository;
        $this->esQuery = $esQuery;
        $this->indexType = $indexType;
        $this->pageSize = $pageSize;
        $this->limit = $limit;
        $this->from = $from;
        $this->initialFrom = $from;
        $this->to = $this->from + $this->limit;
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        if (false === next($this->items)) {
            $this->from += count($this->items);
            $this->items = $this->getNextItems($this->esQuery);
            reset($this->items);
        }
    }

    /**
     * {@inheritdoc}
     *
     * {@see https://www.elastic.co/guide/en/elasticsearch/reference/5.x/search-request-from-size.html}
     */
    protected function getNextIdentifiers(array $esQuery)
    {
        $size = ($this->to - $this->from) > $this->pageSize ? $this->pageSize : ($this->to - $this->from);
        $esQuery['size'] = $size;

        if (0 === $esQuery['size']) {
            return [];
        }

        $sort = ['_uid' => 'asc'];

        if (isset($esQuery['sort'])) {
            $sort = array_merge($esQuery['sort'], $sort);
        }

        $esQuery['sort'] = $sort;
        $esQuery['from'] = $this->from;

        $indices = 'pim_catalog_product,pim_catalog_product_model';
        $response = $this->esClient->search($indices, $esQuery);
        $this->count = $response['hits']['total'];

        $identifiers = [];
        foreach ($response['hits']['hits'] as $hit) {
            $identifiers[] = [
                'identifier' => $hit['_source']['identifier'],
                'index' => $hit['_index'],
            ];
        }

        return $identifiers;
    }


    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->from = $this->initialFrom;
        $this->to = $this->from + $this->limit;
        $this->items = $this->getNextItems($this->esQuery);
        reset($this->items);
    }
}

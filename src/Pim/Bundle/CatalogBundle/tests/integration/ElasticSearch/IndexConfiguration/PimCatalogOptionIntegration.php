<?php

namespace Pim\Bundle\CatalogBundle\tests\integration\Elasticsearch\IndexConfiguration;

/**
 * This integration tests checks that given an index configuration with simple option (string) values
 * the option research is consistent.
 *
 * @author    Philippe Mossière <philippe.mossiere@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
class PimCatalogOptionIntegration extends AbstractPimCatalogIntegration
{
    public function testInListOperatorWithOptionValue()
    {
        $query = [
            'query' => [
                'bool' => [
                    'filter' => [
                        'terms' => [
                            'values.color-option.<all_channels>.<all_locales>' => ['black', 'yellow'],
                        ],
                    ],
                ],
            ],
        ];

        $productsFound = $this->getSearchQueryResults($query);

        $this->assertProducts($productsFound, ['product_1', 'product_2', 'product_3', 'product_6']);
    }

    public function testIsEmptyOperatorWithOptionValue()
    {
        $query = [
            'query' => [
                'bool' => [
                    'must_not' => [
                        'exists' => [
                            'field' => 'values.color-option.<all_channels>.<all_locales>',
                        ],
                    ],
                ],
            ],
        ];

        $productsFound = $this->getSearchQueryResults($query);

        $this->assertProducts($productsFound, ['product_7']);
    }

    public function testIsNotEmptyOperatorWithOptionValue()
    {
        $query = [
            'query' => [
                'bool' => [
                    'filter' => [
                        'exists' => [
                            'field' => 'values.color-option.<all_channels>.<all_locales>',
                        ],
                    ],
                ],
            ],
        ];

        $productsFound = $this->getSearchQueryResults($query);

        $this->assertProducts($productsFound, ['product_1', 'product_2', 'product_3', 'product_4', 'product_5', 'product_6']);
    }

    public function testNotInListOperatorWithOptionValue()
    {
        $query = [
            'query' => [
                'bool' => [
                    'must_not' => [
                        'terms' => [
                            'values.color-option.<all_channels>.<all_locales>' => ['black', 'blue'],
                        ],
                    ],
                    'filter' => [
                        'exists' => [
                            'field' => 'values.color-option.<all_channels>.<all_locales>',
                        ],
                    ]
                ],
            ],
        ];

        $productsFound = $this->getSearchQueryResults($query);

        $this->assertProducts($productsFound, ['product_2', 'product_6']);
    }

    public function testSortAscending()
    {
        $query = [
            'query' => [
                'match_all' => new \stdClass(),
            ],
            'sort'  => [
                [
                    'values.color-option.<all_channels>.<all_locales>' => [
                        'order'   => 'asc',
                        'missing' => '_first',
                    ],
                ],
            ],
        ];

        $productsFound = $this->getSearchQueryResults($query);

        $this->assertSame(
            $productsFound,
            ['product_7', 'product_3', 'product_1', 'product_4', 'product_5', 'product_2', 'product_6']
        );
    }

    public function testSortDescending()
    {
        $query = [
            'query' => [
                'match_all' => new \stdClass(),
            ],
            'sort'  => [
                [
                    'values.color-option.<all_channels>.<all_locales>' => [
                        'order'   => 'desc',
                        'missing' => '_last',
                    ],
                ],
            ],
        ];

        $productsFound = $this->getSearchQueryResults($query);

        $this->assertSame(
            $productsFound,
            ['product_2', 'product_6', 'product_4', 'product_5', 'product_3', 'product_1', 'product_7']
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function addProducts()
    {
        $products = [
            [
                'identifier' => 'product_1',
                'values'     => [
                    'color-option' => [
                        '<all_channels>' => [
                            '<all_locales>' => 'black',
                        ],
                    ],
                ],
            ],
            [
                'identifier' => 'product_2',
                'values'     => [
                    'color-option' => [
                        '<all_channels>' => [
                            '<all_locales>' => 'yellow',
                        ],
                    ],
                ],
            ],
            [
                'identifier' => 'product_3',
                'values'     => [
                    'color-option' => [
                        '<all_channels>' => [
                            '<all_locales>' => 'black',
                        ],
                    ],
                ],
            ],
            [
                'identifier' => 'product_4',
                'values'     => [
                    'color-option' => [
                        '<all_channels>' => [
                            '<all_locales>' => 'blue',
                        ],
                    ],
                ],
            ],
            [
                'identifier' => 'product_5',
                'values'     => [
                    'color-option' => [
                        '<all_channels>' => [
                            '<all_locales>' => 'blue',
                        ],
                    ],
                ],
            ],
            [
                'identifier' => 'product_6',
                'values'     => [
                    'color-option' => [
                        '<all_channels>' => [
                            '<all_locales>' => 'yellow',
                        ],
                    ],
                ],
            ],
            [
                'identifier' => 'product_7',
                'values'     => [],
            ],
        ];

        $this->indexProducts($products);
    }
}
<?php

declare(strict_types=1);

namespace Foo\Pdo\Statement\Preprocessor\ArrayParameter;

use Foo\Pdo\Statement\Preprocessor\ArrayParameter;

class AssociativeTest extends \PHPUnit\Framework\TestCase
{
    /** @var Associative */
    protected $sut;

    public function setUp()
    {
        $this->sut = new Associative();
    }

    /**
     * @return array
     */
    public function processDataProvider()
    {
        return [
            'missing-where-in-parameters' => [
                'SELECT * FROM foo',
                [],
                [],
                'SELECT * FROM foo',
                [],
                false,
            ],
            'numeric-parameters'          => [
                'SELECT * FROM foo',
                [],
                [
                    1 => [['hi', 'hola'], ArrayParameter::PARAM_STR_ARRAY],
                    2 => [['3', '5'], ArrayParameter::PARAM_INT_ARRAY],
                ],
                'SELECT * FROM foo',
                [],
                false,
            ],
            'simple-partials'             => [
                'SELECT * FROM foo WHERE enum_values IN :enum_values AND bar=:bar AND foo=:foo AND id IN(:ids) AND baz = :baz',
                [
                    'bar'         => 'baz',
                    'ids'         => [[1, 2, 3], ArrayParameter::PARAM_INT_ARRAY],
                    'baz'         => 'foo',
                    'enum_values' => [['a', 'b', 'c', 'd'], ArrayParameter::PARAM_STR_ARRAY],
                    'foo'         => ['baz', \PDO::PARAM_STR],
                ],
                [
                    'ids'         => [[1, 2, 3], ArrayParameter::PARAM_INT_ARRAY],
                    'enum_values' => [['a', 'b', 'c', 'd'], ArrayParameter::PARAM_STR_ARRAY],
                ],
                'SELECT * FROM foo WHERE enum_values IN :enum_values__expanded0, :enum_values__expanded1, :enum_values__expanded2, :enum_values__expanded3 AND bar=:bar AND foo=:foo AND id IN(:ids__expanded0, :ids__expanded1, :ids__expanded2) AND baz = :baz',
                [
                    'bar'                    => 'baz',
                    'ids__expanded0'         => [1, \PDO::PARAM_INT],
                    'ids__expanded1'         => [2, \PDO::PARAM_INT],
                    'ids__expanded2'         => [3, \PDO::PARAM_INT],
                    'baz'                    => 'foo',
                    'enum_values__expanded0' => ['a', \PDO::PARAM_STR],
                    'enum_values__expanded1' => ['b', \PDO::PARAM_STR],
                    'enum_values__expanded2' => ['c', \PDO::PARAM_STR],
                    'enum_values__expanded3' => ['d', \PDO::PARAM_STR],
                    'foo'                    => ['baz', \PDO::PARAM_STR],
                ],
                true,
            ],
        ];
    }

    /**
     * @dataProvider processDataProvider
     *
     * @param string $origQuery
     * @param array  $origParameters
     * @param array  $whereInParameters
     * @param string $resultQuery
     * @param array  $resultParameters
     * @param bool   $result
     */
    public function testProcess(
        string $origQuery,
        array $origParameters,
        array $whereInParameters,
        string $expectedQuery,
        array $expectedParameters,
        bool $expectedResult
    ) {
        $actualResult = $this->sut->process($origQuery, $origParameters, $whereInParameters);

        $this->assertSame($expectedQuery, $origQuery);
        $this->assertSame($expectedParameters, $origParameters);
        $this->assertSame($expectedResult, $actualResult);
    }
}

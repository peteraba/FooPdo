<?php

declare(strict_types=1);

namespace Foo\Pdo\Statement\Preprocessor\ArrayParameter;

use Foo\Pdo\Statement\Preprocessor\ArrayParameter;

class NumericTest extends \PHPUnit\Framework\TestCase
{
    /** @var Numeric */
    protected $sut;

    public function setUp()
    {
        $this->sut = new Numeric();
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
            'associative-parameters'      => [
                'SELECT * FROM foo',
                [],
                [
                    'greeting' => [['hi', 'hola'], ArrayParameter::PARAM_STR_ARRAY],
                    'count'    => [['3', '5'], ArrayParameter::PARAM_INT_ARRAY],
                ],
                'SELECT * FROM foo',
                [],
                false,
            ],
            'simple-partials'             => [
                'SELECT * FROM foo WHERE enum_values IN ? AND bar=? AND foo=? AND id IN(?) AND baz = ?',
                [
                    [['a', 'b', 'c', 'd'], ArrayParameter::PARAM_STR_ARRAY],
                    'baz',
                    ['baz', \PDO::PARAM_STR],
                    [[1, 2, 3], ArrayParameter::PARAM_INT_ARRAY],
                    'foo',
                ],
                [
                    0 => [['a', 'b', 'c', 'd'], ArrayParameter::PARAM_STR_ARRAY],
                    3 => [1, 2, 3],
                ],
                'SELECT * FROM foo WHERE enum_values IN ?, ?, ?, ? AND bar=? AND foo=? AND id IN(?, ?, ?) AND baz = ?',
                [
                    ['a', \PDO::PARAM_STR],
                    ['b', \PDO::PARAM_STR],
                    ['c', \PDO::PARAM_STR],
                    ['d', \PDO::PARAM_STR],
                    'baz',
                    ['baz', \PDO::PARAM_STR],
                    [1, \PDO::PARAM_INT],
                    [2, \PDO::PARAM_INT],
                    [3, \PDO::PARAM_INT],
                    'foo',
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

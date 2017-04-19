<?php

declare(strict_types=1);

namespace Foo\Pdo\Statement\Preprocessor;

use Foo\Pdo\Statement\Preprocessor\ArrayParameter\Associative;
use Foo\Pdo\Statement\Preprocessor\ArrayParameter\Numeric;

use PHPUnit_Framework_MockObject_MockObject;

class ArrayParameterTest extends \PHPUnit\Framework\TestCase
{
    /** @var ArrayParameter */
    protected $sut;

    /** @var Numeric|PHPUnit_Framework_MockObject_MockObject */
    protected $numericPreprocessor;

    /** @var Associative|PHPUnit_Framework_MockObject_MockObject */
    protected $associativePreprocessor;

    public function setUp()
    {
        $this->numericPreprocessor = $this->getMockBuilder(Numeric::class)
            ->setMethods(['process'])
            ->getMock();

        $this->associativePreprocessor = $this->getMockBuilder(Associative::class)
            ->setMethods(['process'])
            ->getMock();

        $this->sut = new ArrayParameter($this->numericPreprocessor, $this->associativePreprocessor);
    }

    /**
     * @return array
     */
    public function processSkipsProcessingDataProvider()
    {

        return [
            'integers' => [
                [
                    'categories' => 123,
                    'pages' => 321,
                ],
            ],
            'strings' => [
                [
                    'categories' => '123',
                    'pages' => '321',
                ],
            ],
            'object-likes' => [
                [
                    'categories' => new \StdClass(),
                    'pages' => null,
                ],
            ],
            'simple-pdo' => [
                [
                    'categories' => [12, \PDO::PARAM_INT],
                    'pages' => ['13', \PDO::PARAM_STR],
                ],
            ],
        ];
    }

    /**
     * @dataProvider processSkipsProcessingDataProvider
     *
     * @param array $parameters
     */
    public function testProcessSkipsProcessingIfNoArrayParameterIsProvided(array $parameters)
    {
        $query = '';

        $this->associativePreprocessor->expects($this->never())->method('process');
        $this->numericPreprocessor->expects($this->never())->method('process');

        $this->sut->process($query, $parameters);
    }

    public function testProcessCallsProcessingIfArrayParameterIsProvided()
    {
        $query = '';
        $parameters = [
            'hello' => [432, \PDO::PARAM_STR],
            'categories' => [[123, 124], ArrayParameter::PARAM_INT_ARRAY],
            'pages' => [['123', '1234'], ArrayParameter::PARAM_STR_ARRAY],
        ];

        $this->associativePreprocessor->expects($this->once())->method('process');
        $this->numericPreprocessor->expects($this->once())->method('process');

        $this->sut->process($query, $parameters);
    }
}

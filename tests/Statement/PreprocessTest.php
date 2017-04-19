<?php

declare(strict_types=1);

namespace Foo\Pdo\Statement;

class PreprocessorTest extends \PHPUnit\Framework\TestCase
{
    /** @var Preprocessor */
    protected $sut;

    /** @var Preprocessor|\PHPUnit_Framework_MockObject_MockObject */
    protected $childPreprocessor;

    public function setUp()
    {
        $this->childPreprocessor = $this->getMockBuilder(Preprocessor::class)
            ->setMethods(['process'])
            ->getMock();

        $this->sut = new Preprocessor($this->childPreprocessor, $this->childPreprocessor);
    }

    public function testProcessCallsSetupPreprocessorsToProcess()
    {
        $query      = 'foo';
        $parameters = ['bar', 'baz'];

        $this->childPreprocessor->expects($this->exactly(2))->method('process');

        $this->sut->process($query, $parameters);
    }
}

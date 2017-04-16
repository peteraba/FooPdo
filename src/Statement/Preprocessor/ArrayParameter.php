<?php

declare(strict_types=1);

namespace Foo\Pdo\Statement\Preprocessor;

use Foo\Pdo\Statement\IPreprocessor;
use Foo\Pdo\Statement\Preprocessor\ArrayParameter\Associative;
use Foo\Pdo\Statement\Preprocessor\ArrayParameter\Numeric;

class ArrayParameter implements IPreprocessor
{
    const PARAM_INT_ARRAY = 101;
    const PARAM_STR_ARRAY = 102;

    /** @var Numeric */
    protected $numeric;

    /** @var Associative */
    protected $assocative;

    /**
     * Preprocessor constructor.
     *
     * @param Numeric     $numeric
     * @param Associative $associative
     */
    public function __construct(Numeric $numeric, Associative $associative)
    {
        $this->numeric    = $numeric;
        $this->assocative = $associative;
    }

    /**
     * @param string $query
     * @param array  $parameters
     */
    public function process(string &$query, array &$parameters)
    {
        $whereInParameters = $this->getWhereInParameters($parameters);
        if (empty($whereInParameters)) {
            return;
        }

        $this->numeric->process($query, $parameters, $whereInParameters);
        $this->assocative->process($query, $parameters, $whereInParameters);
    }

    /**
     * @param array $parameters
     *
     * @return array
     */
    private function getWhereInParameters(array $parameters)
    {
        $whereInParameters = array_filter(
            $parameters,
            function ($parameter) {
                if (!is_array($parameter) || !array_key_exists(1, $parameter)) {
                    return false;
                }

                return in_array($parameter[1], [static::PARAM_INT_ARRAY, static::PARAM_STR_ARRAY], true);
            }
        );

        return $whereInParameters;
    }
}

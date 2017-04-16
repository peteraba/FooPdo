<?php

declare(strict_types=1);

namespace Foo\Pdo\Statement\Preprocessor\ArrayParameter;

use Foo\Pdo\Statement\Preprocessor\ArrayParameter;

class Associative
{
    /**
     * @param string $query
     * @param array  $parameters
     * @param array  $whereInParameters
     *
     * @return bool
     */
    public function process(string &$query, array &$parameters, array $whereInParameters)
    {
        $partials = [];
        foreach ($whereInParameters as $key => $values) {
            if (is_numeric($key)) {
                continue;
            }

            $partials[$key] = $this->getQueryPartial($parameters[$key], $key);
        }

        if (!$partials) {
            return false;
        }

        $query      = $this->replaceQueryPartials($query, $partials);
        $parameters = $this->injectParameterPartials($parameters, $partials);

        return true;
    }

    /**
     * @param array  $parameterValues
     * @param string $key
     *
     * @return array
     */
    private function getQueryPartial(array $parameterValues, string $key)
    {
        $paramType = $parameterValues[1] === ArrayParameter::PARAM_INT_ARRAY ? \PDO::PARAM_INT : \PDO::PARAM_STR;

        $queryPartial = [];
        foreach ($parameterValues[0] as $index => $value) {
            $queryPartial[$key . '__expanded' . $index] = [$value, $paramType];
        }

        return $queryPartial;
    }

    /**
     * @param string $query
     * @param array  $partials
     *
     * @return string
     */
    private function replaceQueryPartials(string $query, array $partials)
    {
        foreach ($partials as $key => $inQueryParts) {
            $inQueryParts = array_map(
                function ($value) {
                    return ":{$value}";
                },
                array_keys($inQueryParts)
            );
            $inQuery      = implode(', ', $inQueryParts);

            $query = str_replace(":$key", $inQuery, $query);
        }

        return $query;
    }

    /**
     * @param array $parameters
     * @param array $partials
     *
     * @return array
     */
    private function injectParameterPartials(array $parameters, array $partials)
    {
        foreach ($partials as $origKey => $replacement) {
            $parameters = $this->arraySpliceAssoc(
                $parameters,
                $origKey,
                1,
                $replacement
            );
        }

        return $parameters;
    }

    /**
     * @param array  $input
     * @param string $key
     * @param int    $length
     * @param array  $replacement
     *
     * @return array
     */
    private function arraySpliceAssoc(array &$input, string $key, int $length, array $replacement)
    {
        $keyIndices = array_flip(array_keys($input));
        $offset     = $keyIndices[$key];

        $beginning = array_slice($input, 0, $offset, true);
        $end       = array_slice($input, $offset + $length, null, true);

        $input = $beginning + $replacement + $end;

        return $input;
    }
}

<?php

declare(strict_types=1);

namespace Foo\Pdo\Statement\Preprocessor\ArrayParameter;

use Foo\Pdo\Statement\Preprocessor\ArrayParameter;

class Numeric
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
            if (!is_numeric($key)) {
                continue;
            }

            $partials[$key] = $this->getInQueryPartial($parameters, $key);
        }

        if (!$partials) {
            return false;
        }

        $query      = $this->replaceQueryPartials($query, $partials);
        $parameters = $this->injectParameterPartials($parameters, $partials);

        return true;
    }

    /**
     * @param array $parameters
     * @param int   $key
     *
     * @return array
     */
    private function getInQueryPartial(array $parameters, int $key)
    {
        $values = $parameters[$key][0];

        $paramType = $parameters[$key][1] === ArrayParameter::PARAM_INT_ARRAY ? \PDO::PARAM_INT : \PDO::PARAM_STR;

        $queryParts = array_fill(0, count($values), ['?', $paramType]);

        return $queryParts;
    }

    /**
     * @param string $query
     * @param array  $partials
     *
     * @return string
     */
    private function replaceQueryPartials(string $query, array $partials)
    {
        $queryParts = explode('?', $query);
        $lastIndex  = count($queryParts) - 1;
        $query      = '';
        foreach ($queryParts as $index => $queryPart) {
            if ($index == $lastIndex) {
                $query .= $queryPart;
            } elseif (array_key_exists($index, $partials)) {
                $partialIndex = array_map(
                    function (array $valueArray) {
                        return $valueArray[0];
                    },
                    $partials[$index]
                );

                $query .= $queryPart . implode(', ', $partialIndex);
            } else {
                $query .= $queryPart . '?';
            }
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
        $newParameters = [];
        foreach ($parameters as $parameterKey => $parameter) {
            if (!array_key_exists($parameterKey, $partials)) {
                if (is_numeric($parameterKey)) {
                    $newParameters[] = $parameter;
                } else {
                    $newParameters[$parameterKey] = $parameter;
                }

                continue;
            }

            foreach ($partials[$parameterKey] as $partialKey => $partialValues) {
                $newParameters[] = [$parameter[0][$partialKey], $partialValues[1]];
            }
        }

        return $newParameters;
    }
}

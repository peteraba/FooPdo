<?php

declare(strict_types=1);

namespace Foo\Pdo\Statement;

interface IPreprocessor
{
    /**
     * @param string $query
     * @param array  $parameters
     *
     * @return bool
     */
    public function process(string &$query, array &$parameters);
}

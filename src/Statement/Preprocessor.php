<?php

declare(strict_types = 1);

namespace Foo\Pdo\Statement;

class Preprocessor implements IPreprocessor
{
    protected $preprocessors = [];

    /**
     * Preprocessor constructor.
     *
     * @param IPreprocessor[] ...$preprocessors
     */
    public function __construct(IPreprocessor ...$preprocessors)
    {
        $this->preprocessors = $preprocessors;
    }

    /**
     * @param string $query
     * @param array  $parameters
     */
    public function process(string &$query, array &$parameters)
    {
        foreach ($this->preprocessors as $preprocessor) {
            $preprocessor->process($query, $parameters);
        }

        return true;
    }
}

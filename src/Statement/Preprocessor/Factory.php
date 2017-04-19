<?php

declare(strict_types = 1);

namespace Foo\Pdo\Statement\Preprocessor;

use Foo\Pdo\Statement\Preprocessor;
use Foo\Pdo\Statement\Preprocessor\ArrayParameter\Associative;
use Foo\Pdo\Statement\Preprocessor\ArrayParameter\Numeric;

class Factory
{
    /** @var Preprocessor */
    protected static $instance;

    /**
     * @return Preprocessor
     */
    public static function getPreprocessor()
    {
        if (self::$instance instanceof Preprocessor) {
            return self::$instance;
        }

        self::$instance = new Preprocessor(new ArrayParameter(new Numeric(), new Associative()));

        return self::$instance;
    }
}

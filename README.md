# FooPdo

Pdo statement preprocessors (e.g. easy IN () where clauses)

[![Build Status](https://travis-ci.org/peteraba/FooPdo.svg?branch=master)](https://travis-ci.org/peteraba/FooPdo)
[![License](https://poser.pugx.org/peteraba/foo-pdo/license)](https://packagist.org/packages/peteraba/foo-pdo)
[![composer.lock](https://poser.pugx.org/peteraba/foo-pdo/composerlock)](https://packagist.org/packages/peteraba/foo-pdo)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/peteraba/FooPdo/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/peteraba/FooPdo/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/peteraba/FooPdo/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/peteraba/FooPdo/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/peteraba/FooPdo/badges/build.png?b=master)](https://scrutinizer-ci.com/g/peteraba/FooPdo/build-status/master)


Setup
-----

Install the library via composer:

```
composer install peteraba/foo-pdo
```


Usage
-----

Usage by unnamed parameters:

```php
$sql           = 'SELECT name, age, salary FROM employee WHERE age > ? AND department_id IN (?)';
$departmentIds = [3, 4, 6];
$minAge        = 40;
$parameters    = [$minAge, $departmentIds];

$preprocessor = (new \Foo\Pdo\Statement\Preprocessor\Factory())->getPreprocessor();

$preprocessor->process($sql, $parameters);
// $sql = 'SELECT name, age, salary FROM employee WHERE age > ? department_id IN (?, ?, ?)'
// $departmentIds = [40, 3, 4, 6];
```

Usage with named parameters:
```php
$sql           = 'SELECT name, age, salary FROM employee WHERE age > :age AND department_id IN (:departmentIds)';
$departmentIds = [3, 4, 6];
$minAge        = 40;
$parameters    = [$minAge, $departmentIds];

$preprocessor = (new \Foo\Pdo\Statement\Preprocessor\Factory())->getPreprocessor();

$preprocessor->process($sql, $parameters);
// $sql = 'SELECT name, age, salary FROM employee WHERE age > :age department_id IN (:departmentIds__expanded0, :departmentIds__expanded1, :departmentIds__expanded2)'
// $departmentIds = [
    'age' => 40,
    'departmentIds__expanded0' => 3,
    'departmentIds__expanded1' => 4,
    'departmentIds__expanded2' => 6,
];
```

**Note:** The current implementation is able to handle a mixed set of named and unnamed parameters, but there is no guarantee for this to be the case in the future so you should avoid using this unsupported feature.

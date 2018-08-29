<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendBench\Stdlib;

use PhpBench\Benchmark\Metadata\Annotations\Iterations;
use PhpBench\Benchmark\Metadata\Annotations\Revs;
use PhpBench\Benchmark\Metadata\Annotations\Warmup;
use Zend\Stdlib\ArrayUtils;

/**
 * @Revs(1000)
 * @Iterations(10)
 * @Warmup(2)
 */
class ArrayUtilsBench
{
    public function benchHasStringKeys()
    {
        ArrayUtils::hasStringKeys([
            'key' => 'value',
        ]);
    }

    public function benchHasIntegerKeys()
    {
        ArrayUtils::hasIntegerKeys([
            1 => 'value',
        ]);
    }

    public function benchHasNumericKeys()
    {
        ArrayUtils::hasNumericKeys([
            '1' => 'value',
        ]);
    }
}

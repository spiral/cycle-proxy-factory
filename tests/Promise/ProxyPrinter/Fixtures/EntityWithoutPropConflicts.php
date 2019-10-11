<?php

/**
 * Spiral Framework. Cycle ProxyFactory
 *
 * @license MIT
 * @author  Valentin V (Vvval)
 */
declare(strict_types=1);

namespace Cycle\ORM\Promise\Tests\ProxyPrinter\Fixtures;

class EntityWithoutPropConflicts
{
    public $public;
    public static $publicStatic;
    protected $protected;
    private $private;
}

<?php

/**
 * Spiral Framework. Cycle ProxyFactory
 *
 * @license MIT
 * @author  Valentin V (Vvval)
 */

declare(strict_types=1);

namespace Cycle\ORM\Promise\Visitor;

use PhpParser\Builder;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

use function Cycle\ORM\Promise\ifInConstArray;
use function Cycle\ORM\Promise\resolveIntoVar;
use function Cycle\ORM\Promise\returnIssetFunc;
use function Cycle\ORM\Promise\throwExceptionOnNull;

final class AddMagicIssetMethod extends NodeVisitorAbstract
{
    /** @var string */
    private $class;

    /** @var string */
    private $resolverProperty;

    /** @var string */
    private $resolveMethod;

    /** @var string */
    private $unsetPropertiesProperty;

    /**
     * @param string $class
     * @param string $resolverProperty
     * @param string $resolveMethod
     * @param string $unsetPropertiesProperty
     */
    public function __construct(
        string $class,
        string $resolverProperty,
        string $resolveMethod,
        string $unsetPropertiesProperty
    ) {
        $this->class = $class;
        $this->unsetPropertiesProperty = $unsetPropertiesProperty;
        $this->resolveMethod = $resolveMethod;
        $this->resolverProperty = $resolverProperty;
    }

    /**
     * @param Node $node
     * @return int|Node|Node[]|null
     */
    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Class_) {
            $method = new Builder\Method('__isset');
            $method->makePublic();
            $method->addParam(new Builder\Param('name'));
            $method->addStmt($this->buildIssetExpression());

            $node->stmts[] = $method->getNode();
        }

        return null;
    }

    /**
     * @return Node\Stmt\If_
     */
    private function buildIssetExpression(): Node\Stmt\If_
    {
        $if = ifInConstArray('name', 'self', $this->unsetPropertiesProperty);
        $if->stmts[] = resolveIntoVar('entity', 'this', $this->resolverProperty, $this->resolveMethod);
        $if->stmts[] = throwExceptionOnNull(
            new Node\Expr\Variable('entity'),
            returnIssetFunc('entity', '{$name}'),
            'Property `%s` not loaded in `__isset()` method for `%s`',
            [
                new Node\Arg(new Node\Expr\Variable('name')),
                $this->class
            ]
        );
        $if->else = new Node\Stmt\Else_([
            returnIssetFunc('this', '{$name}')
        ]);

        return $if;
    }
}

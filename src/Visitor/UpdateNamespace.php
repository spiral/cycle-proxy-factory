<?php
declare(strict_types=1);

namespace Spiral\Cycle\Promise\Visitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class UpdateNamespace extends NodeVisitorAbstract
{
    /** @var string|null */
    private $namespace;

    public function __construct(?string $namespace)
    {
        $this->namespace = $namespace;
    }

    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Namespace_) {
            if (empty($this->namespace)) {
                return $node->stmts;
            } else {
                $node->name->parts = explode('\\', $this->namespace);
            }
        }

        return null;
    }
}
<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Analysis;

use Ghostwriter\Json\Json;
use JsonSerializable;
use Stringable;

/** @see TokenTest */
final class Node implements JsonSerializable, Stringable
{
    /**
     * @param array<Node|Token> $children
     */
    public function __construct(
        private readonly self $parent,
        private readonly array $children,
    ) {
    }

    public function __toString(): string
    {
        return Json::encode($this->jsonSerialize());
    }

    /**
     * @return array<Node|Token>
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    public function getParent(): self
    {
        return $this->parent;
    }

    /**
     * @return array{kind: string, parent: Node, children: array<Node|Token>}
     */
    public function jsonSerialize(): array
    {
        return [
            'kind' => self::class,
            'parent' => $this->parent,
            'children' => $this->children,
        ];
    }
}

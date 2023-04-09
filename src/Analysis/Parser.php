<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Analysis;

use Generator;

/** @see ParserTest */
final class Parser
{
    public function __construct(
        private readonly Lexer $lexer
    ) {
    }

    /**
     * @return Generator<Node|Token>
     */
    public function parse(string $document): Generator
    {
        yield from $this->lexer->lex($document);
//        $nodes = $this->lexer->lex($document);
//        foreach ($nodes as $offset => $node) {
//            yield $offset => $node;
//        }
    }
}

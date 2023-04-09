<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Analysis;

use Generator;

/** @see LexerTest */
final class Lexer
{
    public function __construct(
        private readonly Tokenizer $tokenizer
    ) {
    }

    /**
     * @return Generator<int,Token>
     */
    public function lex(string $document): Generator
    {
        $tokens = $this->tokenizer->tokenize($document);
        foreach ($tokens as $offset => $token) {
            yield $offset => new Token(TokenKind::UNKNOWN, $offset, $token, 0);
        }
    }
}

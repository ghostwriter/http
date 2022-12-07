<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Contract\Factory;

use Ghostwriter\Http\Contract\Message\ResponseInterface;
use Ghostwriter\Http\Contract\Message\StatusCodeInterface;

interface ResponseFactoryInterface extends StatusCodeInterface
{
    /**
     * Create a new response.
     *
     * @param int    $code         HTTP status code; defaults to 200
     * @param string $reasonPhrase reason phrase to associate with status code
     *                             in generated response; if none is provided implementations MAY use
     *                             the defaults as suggested in the HTTP specification
     *
     */
    public function createResponse(
        int $code = self::HTTP_200_OK,
        string $reasonPhrase = self::HTTP_REASON_PHRASE[self::HTTP_200_OK]
    ): ResponseInterface;
}

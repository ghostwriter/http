<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Client\Traits;

use CurlHandle;
use CurlMultiHandle;
use CurlShareHandle;

/**
 * Trait implementing functionality common to HTTP clients.
 */
trait ClientTrait
{
    /**
     * @var null|CurlHandle|CurlMultiHandle|CurlShareHandle
     */
    private mixed $handle;

    /**
     * @return null|CurlHandle|CurlMultiHandle|CurlShareHandle
     */
    public function getHandle(): mixed
    {
        return $this->handle;
    }
}

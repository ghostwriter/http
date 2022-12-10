<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Contract\Message;

/**
 * Defines constants for common HTTP request methods.
 */
interface RequestMethodInterface
{
    /**
     * @var string
     */
    public const METHOD_CONNECT = 'CONNECT';

    /**
     * @var string
     */
    public const METHOD_DELETE  = 'DELETE';

    /**
     * @var string
     */
    public const METHOD_GET     = 'GET';

    /**
     * @var string
     */
    public const METHOD_HEAD    = 'HEAD';

    /**
     * @var string
     */
    public const METHOD_OPTIONS = 'OPTIONS';

    /**
     * @var string
     */
    public const METHOD_PATCH   = 'PATCH';

    /**
     * @var string
     */
    public const METHOD_POST    = 'POST';

    /**
     * @var string
     */
    public const METHOD_PURGE   = 'PURGE';

    /**
     * @var string
     */
    public const METHOD_PUT     = 'PUT';

    /**
     * @var string
     */
    public const METHOD_TRACE   = 'TRACE';
}

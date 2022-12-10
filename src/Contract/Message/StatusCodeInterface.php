<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Contract\Message;

/**
 * Defines constants for common HTTP status code.
 */
interface StatusCodeInterface
{
    /**
     * @var int
     */
    public const HTTP_100_CONTINUE = 100;

    /**
     * @var int
     */
    public const HTTP_101_SWITCHING_PROTOCOLS = 101;

    /**
     * @var int
     */
    public const HTTP_102_PROCESSING = 102;

    /**
     * @var int
     */
    public const HTTP_103_EARLY_HINTS = 103;

    /**
     * @var int
     */
    public const HTTP_200_OK = 200;

    /**
     * @var int
     */
    public const HTTP_201_CREATED = 201;

    /**
     * @var int
     */
    public const HTTP_202_ACCEPTED = 202;

    /**
     * @var int
     */
    public const HTTP_203_NON_AUTHORITATIVE_INFORMATION = 203;

    /**
     * @var int
     */
    public const HTTP_204_NO_CONTENT = 204;

    /**
     * @var int
     */
    public const HTTP_205_RESET_CONTENT = 205;

    /**
     * @var int
     */
    public const HTTP_206_PARTIAL_CONTENT = 206;

    /**
     * @var int
     */
    public const HTTP_207_MULTI_STATUS = 207;

    /**
     * @var int
     */
    public const HTTP_208_ALREADY_REPORTED = 208;

    /**
     * @var int
     */
    public const HTTP_226_IM_USED = 226;

    /**
     * @var int
     */
    public const HTTP_300_MULTIPLE_CHOICES = 300;

    /**
     * @var int
     */
    public const HTTP_301_MOVED_PERMANENTLY = 301;

    /**
     * @var int
     */
    public const HTTP_302_FOUND = 302;

    /**
     * @var int
     */
    public const HTTP_303_SEE_OTHER = 303;

    /**
     * @var int
     */
    public const HTTP_304_NOT_MODIFIED = 304;

    /**
     * @var int
     */
    public const HTTP_305_USE_PROXY = 305;

    /**
     * @var int
     */
    public const HTTP_306_RESERVED = 306;

    /**
     * @var int
     */
    public const HTTP_307_TEMPORARY_REDIRECT = 307;

    /**
     * @var int
     */
    public const HTTP_308_PERMANENT_REDIRECT = 308;

    // Client Error 4xx
    /**
     * @var int
     */
    public const HTTP_400_BAD_REQUEST = 400;

    /**
     * @var int
     */
    public const HTTP_401_UNAUTHORIZED = 401;

    /**
     * @var int
     */
    public const HTTP_402_PAYMENT_REQUIRED = 402;

    /**
     * @var int
     */
    public const HTTP_403_FORBIDDEN = 403;

    /**
     * @var int
     */
    public const HTTP_404_NOT_FOUND = 404;

    /**
     * @var int
     */
    public const HTTP_405_METHOD_NOT_ALLOWED = 405;

    /**
     * @var int
     */
    public const HTTP_406_NOT_ACCEPTABLE = 406;

    /**
     * @var int
     */
    public const HTTP_407_PROXY_AUTHENTICATION_REQUIRED = 407;

    /**
     * @var int
     */
    public const HTTP_408_REQUEST_TIMEOUT = 408;

    /**
     * @var int
     */
    public const HTTP_409_CONFLICT = 409;

    /**
     * @var int
     */
    public const HTTP_410_GONE = 410;

    /**
     * @var int
     */
    public const HTTP_411_LENGTH_REQUIRED = 411;

    /**
     * @var int
     */
    public const HTTP_412_PRECONDITION_FAILED = 412;

    /**
     * @var int
     */
    public const HTTP_413_PAYLOAD_TOO_LARGE = 413;

    /**
     * @var int
     */
    public const HTTP_414_URI_TOO_LONG = 414;

    /**
     * @var int
     */
    public const HTTP_415_UNSUPPORTED_MEDIA_TYPE = 415;

    /**
     * @var int
     */
    public const HTTP_416_RANGE_NOT_SATISFIABLE = 416;

    /**
     * @var int
     */
    public const HTTP_417_EXPECTATION_FAILED = 417;

    /**
     * @var int
     */
    public const HTTP_418_IM_A_TEAPOT = 418;

    /**
     * @var int
     */
    public const HTTP_421_MISDIRECTED_REQUEST = 421;

    /**
     * @var int
     */
    public const HTTP_422_UNPROCESSABLE_ENTITY = 422;

    /**
     * @var int
     */
    public const HTTP_423_LOCKED = 423;

    /**
     * @var int
     */
    public const HTTP_424_FAILED_DEPENDENCY = 424;

    /**
     * @var int
     */
    public const HTTP_425_TOO_EARLY = 425;

    /**
     * @var int
     */
    public const HTTP_426_UPGRADE_REQUIRED = 426;

    /**
     * @var int
     */
    public const HTTP_428_PRECONDITION_REQUIRED = 428;

    /**
     * @var int
     */
    public const HTTP_429_TOO_MANY_REQUESTS = 429;

    /**
     * @var int
     */
    public const HTTP_431_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;

    /**
     * @var int
     */
    public const HTTP_451_UNAVAILABLE_FOR_LEGAL_REASONS = 451;

    /**
     * @var int
     */
    public const HTTP_500_INTERNAL_SERVER_ERROR = 500;

    /**
     * @var int
     */
    public const HTTP_501_NOT_IMPLEMENTED = 501;

    /**
     * @var int
     */
    public const HTTP_502_BAD_GATEWAY = 502;

    /**
     * @var int
     */
    public const HTTP_503_SERVICE_UNAVAILABLE = 503;

    /**
     * @var int
     */
    public const HTTP_504_GATEWAY_TIMEOUT = 504;

    /**
     * @var int
     */
    public const HTTP_505_VERSION_NOT_SUPPORTED = 505;

    /**
     * @var int
     */
    public const HTTP_506_VARIANT_ALSO_NEGOTIATES = 506;

    /**
     * @var int
     */
    public const HTTP_507_INSUFFICIENT_STORAGE = 507;

    /**
     * @var int
     */
    public const HTTP_508_LOOP_DETECTED = 508;

    /**
     * @var int
     */
    public const HTTP_510_NOT_EXTENDED = 510;

    /**
     * @var int
     */
    public const HTTP_511_NETWORK_AUTHENTICATION_REQUIRED = 511;

    /**
     * @var array<int, string>
     */
    public const HTTP_REASON_PHRASE = [
        // Informational 1xx
        self::HTTP_100_CONTINUE => 'Continue',
        self::HTTP_101_SWITCHING_PROTOCOLS=> 'Switching Protocols',
        self::HTTP_102_PROCESSING=> 'Processing',
        self::HTTP_103_EARLY_HINTS=> 'Early Hints',
        // Successful 2xx
        self::HTTP_200_OK=> 'OK',
        self::HTTP_201_CREATED=> 'Created',
        self::HTTP_202_ACCEPTED=> 'Accepted',
        self::HTTP_203_NON_AUTHORITATIVE_INFORMATION=> 'Non-Authoritative Information',
        self::HTTP_204_NO_CONTENT=> 'No Content',
        self::HTTP_205_RESET_CONTENT=> 'Reset Content',
        self::HTTP_206_PARTIAL_CONTENT=> 'Partial Content',
        self::HTTP_207_MULTI_STATUS=> 'Multi-Status',
        self::HTTP_208_ALREADY_REPORTED=> 'Already Reported',
        self::HTTP_226_IM_USED=> 'IM Used',
        // Redirection 3xx
        self::HTTP_300_MULTIPLE_CHOICES=> 'Multiple Choices',
        self::HTTP_301_MOVED_PERMANENTLY=> 'Moved Permanently',
        self::HTTP_302_FOUND=> 'Found',
        self::HTTP_303_SEE_OTHER=> 'See Other',
        self::HTTP_304_NOT_MODIFIED=> 'Not Modified',
        self::HTTP_305_USE_PROXY=> 'Use Proxy',
        self::HTTP_306_RESERVED=> 'Reserved',
        self::HTTP_307_TEMPORARY_REDIRECT=> 'Temporary Redirect',
        self::HTTP_308_PERMANENT_REDIRECT   => 'Permanent Redirect',
        // Client Error 4xx
        self::HTTP_400_BAD_REQUEST=> 'Bad Request',
        self::HTTP_401_UNAUTHORIZED=> 'Unauthorized',
        self::HTTP_402_PAYMENT_REQUIRED=> 'Payment Required',
        self::HTTP_403_FORBIDDEN=> 'Forbidden',
        self::HTTP_404_NOT_FOUND=> 'Not Found',
        self::HTTP_405_METHOD_NOT_ALLOWED=> 'Method Not Allowed',
        self::HTTP_406_NOT_ACCEPTABLE=> 'Not Acceptable',
        self::HTTP_407_PROXY_AUTHENTICATION_REQUIRED=> 'Proxy Authentication Required',
        self::HTTP_408_REQUEST_TIMEOUT=> 'Request Timeout',
        self::HTTP_409_CONFLICT=> 'Conflict',
        self::HTTP_410_GONE=> 'Gone',
        self::HTTP_411_LENGTH_REQUIRED=> 'Length Required',
        self::HTTP_412_PRECONDITION_FAILED=> 'Precondition Failed',
        self::HTTP_413_PAYLOAD_TOO_LARGE=> 'Payload Too Large',
        self::HTTP_414_URI_TOO_LONG=> 'URI Too Long',
        self::HTTP_415_UNSUPPORTED_MEDIA_TYPE=> 'Unsupported Media Type',
        self::HTTP_416_RANGE_NOT_SATISFIABLE=> 'Range Not Satisfiable',
        self::HTTP_417_EXPECTATION_FAILED=> 'Expectation Failed',
        self::HTTP_418_IM_A_TEAPOT=> 'Im a teapot',
        self::HTTP_421_MISDIRECTED_REQUEST=> 'Misdirected Request',
        self::HTTP_422_UNPROCESSABLE_ENTITY=> 'Unprocessable Entity',
        self::HTTP_423_LOCKED=> 'Locked',
        self::HTTP_424_FAILED_DEPENDENCY=> 'Failed Dependency',
        self::HTTP_425_TOO_EARLY=> 'Too Early',
        self::HTTP_426_UPGRADE_REQUIRED=> 'Upgrade Required',
        self::HTTP_428_PRECONDITION_REQUIRED=> 'Precondition Required',
        self::HTTP_429_TOO_MANY_REQUESTS=> 'Too Many Requests',
        self::HTTP_431_REQUEST_HEADER_FIELDS_TOO_LARGE=> 'Request Header Fields Too Large',
        self::HTTP_451_UNAVAILABLE_FOR_LEGAL_REASONS=> 'Unavailable For Legal Reasons',
        // Server Error 5xx
        self::HTTP_500_INTERNAL_SERVER_ERROR=> 'Internal Server Error',
        self::HTTP_501_NOT_IMPLEMENTED=> 'Not Implemented',
        self::HTTP_502_BAD_GATEWAY=> 'Bad Gateway',
        self::HTTP_503_SERVICE_UNAVAILABLE=> 'Service Unavailable',
        self::HTTP_504_GATEWAY_TIMEOUT=> 'Gateway Timeout',
        self::HTTP_505_VERSION_NOT_SUPPORTED=> 'HTTP Version Not Supported',
        self::HTTP_506_VARIANT_ALSO_NEGOTIATES=> 'Variant Also Negotiates',
        self::HTTP_507_INSUFFICIENT_STORAGE=> 'Insufficient Storage',
        self::HTTP_508_LOOP_DETECTED=> 'Loop Detected',
        self::HTTP_510_NOT_EXTENDED=> 'Not Extended',
        self::HTTP_511_NETWORK_AUTHENTICATION_REQUIRED=> 'Network Authentication Required',
    ];
}

<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Exceptions\Interfaces;

interface ExceptionInterface extends JsonSchemaErrorInterface
{
    // General errors *salute* 10000 - 10999
    const NOT_FOUND = [
        'code'  => '10000',
        'title' => 'Not Found',
    ];

    const INTERNAL_SERVER_ERROR = [
        'code'  => '10001',
        'title' => 'Internal Server Error',
    ];

    const RESOURCE_NOT_FOUND = [
        'code'  => '10002',
        'title' => 'Resource Not Found',
    ];

    const RESOURCE_HANDLED_BY_3RD_PARTY = [
        'code'  => '10014',
        'title' => 'Resource Handled By 3rd Party',
    ];

    const INVALID_JSON_SCHEMA = [
        'code'  => '10003',
        'title' => 'Invalid JSON Schema',
    ];

    const INVALID_REQUEST_HEADER = [
        'code'  => '10004',
        'title' => 'Invalid Request Header',
    ];

    const RESOURCE_CANNOT_BE_MODIFIED = [
        'code'  => '10005',
        'title' => 'Resource Cannot Be Modified',
    ];

    const INVALID_ERROR_SCHEMA = [
        'code'  => '10006',
        'title' => 'Invalid Error Schema',
    ];

    const RESOURCE_CONFLICT = [
        'code'  => '10007',
        'title' => 'Resource Conflict',
    ];

    const UNPROCESSABLE_ENTITY = [
        'code'  => '10008',
        'title' => 'Unprocessable entity',
    ];

    const METHOD_NOT_ALLOWED = [
        'code'  => '10009',
        'title' => 'Method not allowed',
    ];

    const MISSING_REQUEST_HEADER = [
        'code'  => '10010',
        'title' => 'Missing Request Header',
    ];

    const TOO_MANY_REQUESTS = [
        'code'  => '10011',
        'title' => 'Too many requests.',
    ];

    const RELATIONSHIP_CANNOT_BE_MODIFIED = [
        'code'  => '10012',
        'title' => 'Relationship cannot be modified.',
    ];

    const FORBIDDEN = [
        'code'  => '10013',
        'title' => 'Action not allowed.',
    ];

    // External API related errors 13000 - 13999
    const EXTERNAL_REQUEST_ERROR = [
        'code'  => '13001',
        'title' => 'External Request Error',
    ];

    const CARRIER_API_ERROR = [
        'code'  => '13002',
        'title' => 'Carrier API Error',
    ];

    const INVALID_SECRET = [
        'code'  => '13003',
        'title' => 'Invalid Secret',
    ];

    // Auth related errors 14000 - 14999
    const AUTH_INVALID_CLIENT = [
        'code'  => '14000',
        'title' => 'Invalid OAuth Client',
    ];

    const AUTH_INVALID_SCOPE = [
        'code'  => '14001',
        'title' => 'Scope Not Available To Client',
    ];

    const AUTH_INVALID_TOKEN = [
        'code'  => '14002',
        'title' => 'Access Token Is Invalid',
    ];

    const AUTH_MISSING_TOKEN = [
        'code'  => '14003',
        'title' => 'No Access Token Provided',
    ];

    const AUTH_MISSING_SCOPE = [
        'code'  => '14004',
        'title' => 'Access Token Is Invalid',
    ];

    const AUTH_SERVER_EXCEPTION = [
        'code'  => '14050',
        'title' => 'Unable To Process OAuth Request',
    ];
}

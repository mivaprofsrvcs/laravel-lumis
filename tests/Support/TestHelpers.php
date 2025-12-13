<?php

use MVPS\Lumis\Services\ApiClientService;

/**
 * Decode the current Miva API request body into an array.
 *
 * @return array<string, mixed>
 */
function decodeRequest(ApiClientService $service): array
{
    $body = $service->getRequestBody(JSON_THROW_ON_ERROR);

    return json_decode($body, true, 512, JSON_THROW_ON_ERROR);
}

/**
 * Find a filter entry by its "name" within a decoded request body.
 *
 * @param  array<string, mixed>  $request
 * @return array{name:string,value:mixed}|null
 */
function findFilter(array $request, string $name): ?array
{
    $filters = $request['Filter'] ?? [];

    foreach ($filters as $filter) {
        if (($filter['name'] ?? null) === $name) {
            return $filter;
        }
    }

    return null;
}

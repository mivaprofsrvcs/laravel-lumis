<?php

use MVPS\Lumis\Services\ApiClientService;

/**
 * Decode and extract the first function payload from a Miva API request body.
 */
function decodeFirstFunction(ApiClientService $service): array
{
	$body = $service->getRequestBody(JSON_THROW_ON_ERROR);
	$data = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

	expect($data)->toHaveKey('FunctionList');

	$functions = $data['FunctionList'];
	$firstName = array_key_first($functions);
	$firstPayload = $functions[$firstName];

	return [$firstName, $firstPayload];
}

/**
 * Find a specific filter entry by its name within the given filter list.
 */
function findFilter(array $filters, string $name): ?array
{
	foreach ($filters as $filter) {
		if (($filter['name'] ?? null) === $name) {
			return $filter;
		}
	}

	return null;
}

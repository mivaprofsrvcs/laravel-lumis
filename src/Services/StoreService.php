<?php

namespace MVPS\Lumis\Services;

final readonly class StoreService
{
	/**
	 * The Miva store authorization credentials.
	 *
	 * Typically used with password protected stores (Basic Auth).
	 *
	 * @var array{username:string, password:string}|array{}
	 */
	private array $auth;

	/**
	 * The Miva store code.
	 *
	 * @var string
	 */
	private string $code;

	/**
	 * The Miva store graphics path.
	 *
	 * @var string
	 */
	private string $graphicsPath;

	/**
	 * The Miva store root path (root directory for graphics, modules, etc).
	 *
	 * @var string
	 */
	private string $rootPath;

	/**
	 * The Miva store url.
	 *
	 * @var string
	 */
	private string $url;

	/**
	 * Create a new Miva store service instance.
	 */
	public function __construct(
		string $code,
		string $url,
		string $graphicsPath = 'graphics/',
		string $rootPath = '/mm5/',
		array $auth = []
	) {
		$this->code = trim($code);
		$this->url = rtrim(trim($url), '/');
		$this->graphicsPath = rtrim(trim($graphicsPath), '/') . '/';

		$trimmedRoot = trim($rootPath, '/');
		$this->rootPath = '/' . ($trimmedRoot === '' ? '' : $trimmedRoot . '/');

		$username = trim((string) ($auth['username'] ?? ''));
		$password = trim((string) ($auth['password'] ?? ''));
		$this->auth = $username !== '' && $password !== ''
			? ['username' => $username, 'password' => $password]
			: [];
	}

	/**
	 * Get the store auth array.
	 *
	 * @return array{username:string,password:string}|array{}
	 */
	public function auth(): array
	{
		return $this->auth;
	}

	/**
	 * Get the HTTP Basic Authorization header array,
	 * or an empty array if auth is not configured.
	 */
	public function authHeader(): array
	{
		$value = $this->authHeaderValue();

		return $value !== '' ? ['Authorization' => $value] : [];
	}

	/**
	 * Returns the HTTP Basic Authorization header value,
	 * or empty string if not set.
	 */
	public function authHeaderValue(): string
	{
		if (empty($this->auth['username']) || empty($this->auth['password'])) {
			return '';
		}

		return 'Basic ' . base64_encode($this->auth['username'] . ':' . $this->auth['password']);
	}

	/**
	 * Get the store code.
	 */
	public function code(): string
	{
		return $this->code;
	}

	/**
	 * Get the store graphics path.
	 */
	public function graphicsPath(): string
	{
		return $this->graphicsPath;
	}

	/**
	 * Get the URL for the Miva store's graphics path.
	 */
	public function graphicsUrl(): string
	{
		return $this->urlWithPath($this->rootPath . $this->graphicsPath);
	}

	/**
	 * Get the Miva store's JSON URL (json.mvc).
	 */
	public function jsonUrl(string $jsonFile = 'json.mvc'): string
	{
		return $this->urlWithPath($this->rootPath . $jsonFile);
	}

	/**
	 * Get the store root path.
	 */
	public function rootPath(): string
	{
		return $this->rootPath;
	}

	/**
	 * Get the Miva store's root path.
	 */
	public function rootUrl(): string
	{
		return $this->urlWithPath($this->rootPath);
	}

	/**
	 * Get the store URL.
	 */
	public function url(): string
	{
		return $this->url;
	}

	/**
	 * Generate a store URL with the provided path.
	 */
	public function urlWithPath(string $path): string
	{
		return $this->url . '/' . ltrim($path, '/');
	}
}

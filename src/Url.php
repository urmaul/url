<?php

namespace urmaul\url;

class Url
{
	/**
	 * Url string
	 * @var string
	 */
	protected $url;
	
	public function __construct($url)
	{
		$this->url = $url;
	}
	
	public static function from($url)
	{
		return new self($url);
	}
	
	/**
	 * Returns absolute url.
	 * @param string $baseUrl
	 * @return string
	 */
	public function absolute($baseUrl)
	{
		$url = $this->url;
		
		$pos = strpos($url, '://');
		if ($pos === false || $pos > 10) {
			$parsed = parse_url($baseUrl);
			if (!isset($parsed['scheme']))
				throw new \Exception('Invalid base url "' . $baseUrl . '": scheme not found.');
			
			if (strncmp($url, '//', 2) == 0) {
				return $parsed['scheme'] . ':' . $url;
			}
			
			$fullHost = $parsed['scheme'] . '://' . $parsed['host'];
			
			if (substr($url, 0, 1) == '?') {
				return $fullHost . $parsed['path'] . $url;
			}
			
			if (substr($url, 0, 1) == '/') {
				return $fullHost . $url;
			}
			
			$pathParts = explode('/', $parsed['path']);
			array_pop($pathParts);
			
			while (substr($url, 0, 3) == '../') {
				array_pop($pathParts);
				$url = substr($url, 3);
			}
			
			return $fullHost . implode('/', $pathParts) . '/' . $url;
		}
		
		return $url;
	}

	/**
	 * Adds parameters to an url.
	 * @param array $addParams [name => value] parameters to add
	 * @return string result url
	 */
	public function addParams(array $addParams)
	{
		$url = $this->url;
		
		$parts = parse_url($url) + array(
			'scheme' => 'http',
			'query' => '',
			'path' => '',
		);
		
		$prefix = '';
		if (isset($parts['host'])) {
			$prefix = sprintf('%s://%s', $parts['scheme'], $parts['host']);
		}
		
		parse_str($parts['query'], $params);
		$params = array_merge($params, $addParams);
		$query = http_build_query($params);
		
		return 
			$prefix .
			$parts['path'] .
			($query ? '?' . $query : '') .
			(isset($parts['fragment']) ? '#' . $parts['fragment'] : '');
	}

	/**
	 * Adds parameter to an url.
	 * @param string $name parameter name
	 * @param string $value parameter value
	 * @return string result url
	 */
	public function addParam($name, $value)
	{
		return $this->addParams(array($name => $value));
	}

	/**
	 * Removes query parameters from url.
	 * @param array $removeParams [name => value] parameters to add
	 * @return string result url
	 */
	public function removeParams(array $removeParams)
	{
		$url = $this->url;
		
		$parts = parse_url($url) + array(
			'scheme' => 'http',
			'query' => '',
			'path' => '',
		);
		
		$prefix = '';
		if (isset($parts['host'])) {
			$prefix = sprintf('%s://%s', $parts['scheme'], $parts['host']);
		}
		
		parse_str($parts['query'], $params);
		$params = array_diff_key($params, array_flip($removeParams));
		$query = http_build_query($params);
		
		return 
			$prefix .
			$parts['path'] .
			($query ? '?' . $query : '') .
			(isset($parts['fragment']) ? '#' . $parts['fragment'] : '');
	}

	/**
	 * Removes query parameter from url.
	 * @param string $name parameter name
	 * @return string result url
	 */
	public function removeParam($name)
	{
		return $this->removeParams(array($name));
	}
}

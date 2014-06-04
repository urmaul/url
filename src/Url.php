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
				throw new Exception('Invalid base url "' . $baseUrl . '": scheme not found.');
			
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
			//echo $url . '<br />' . $baseUrl . '<br />' . $fullHost . '<b>' . implode('/', $pathParts) . '</b>' . '/' . $url . '<br />' . '<br />';
			
			return $fullHost . implode('/', $pathParts) . '/' . $url;
		}
		
		return $url;
	}

	/**
	 * Adds parameter to an url.
	 * @param string $name parameter name
	 * @param string $value parameter value
	 * @return string result url
	 */
	public function addParam($name, $value)
	{
		$url = $this->url;
		
		$parts = parse_url($url) + array(
			'scheme' => 'http',
			'query' => '',
			'path' => '/',
		);
		
		parse_str($parts['query'], $params);
		
		$params[$name] = $value;
		
		return sprintf(
			'%s://%s%s?%s%s',
			$parts['scheme'],
			$parts['host'],
			$parts['path'],
			http_build_query($params),
			isset($parts['fragment']) ? '#' . $parts['fragment'] : ''
		);
	}
}

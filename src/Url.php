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
     * @param string $baseUrl base url to create absolute url.
     * @return string
     * @throws \Exception if baseUrl is invalid
     */
    public function absolute($baseUrl)
    {
        $url = $this->url;

        $pos = strpos($url, '://');
        if ($pos === false || $pos > 10) {
            $parsed = parse_url($baseUrl) + array(
                    'path' => '',
                );
            if (!isset($parsed['scheme'], $parsed['host']))
                throw new \Exception('Invalid base url "' . $baseUrl . '": scheme not found.');

            if (empty($url))
                return $baseUrl;

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
     * Returns root relative url.
     * @param string $baseUrl base url to create absolute url.
     * @return string
     * @throws \Exception if baseUrl is invalid
     */
    public function rootRelative($baseUrl)
    {
        $url = $this->url;

        $parsedBaseUrl = parse_url($baseUrl) + array(
                'path' => '',
            );
        if (!isset($parsedBaseUrl['scheme'], $parsedBaseUrl['host'])) {
            throw new \Exception('Invalid base url "' . $baseUrl . '": scheme not found.');
        }

        if (empty($url)) {
            return $parsedBaseUrl['path'] . (isset($parsedBaseUrl['query']) ? '?' . $parsedBaseUrl['query'] : '');
        }

        $parsedUrl = parse_url($url) + array(
                'path' => '/',
            );
        if (substr($url, 0, 1) == '?') {
            return $parsedBaseUrl['path'] . $url;
        }

        if (substr($url, 0, 3) == '../') {
            $pathParts = explode('/', $parsedBaseUrl['path']);
            array_pop($pathParts);

            while (substr($url, 0, 3) == '../') {
                array_pop($pathParts);
                $url = substr($url, 3);
            }

            return implode('/', $pathParts) . '/' . $url;
        }

        return $parsedUrl['path'] . (isset($parsedUrl['query']) ? '?' . $parsedUrl['query'] : '');
    }

    /**
     * Adds parameters to an url.
     * @param array $addParams [name => value] parameters to add
     * @return string result url
     */
    public function addParams(array $addParams)
    {
        $parts = $this->split($this->url);

        parse_str($parts['query'], $params);
        $params = array_merge($params, $addParams);
        $parts['query'] = http_build_query($params);

        return $this->join($parts);
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
        $parts = $this->split($this->url);

        parse_str($parts['query'], $params);
        $params = array_diff_key($params, array_flip($removeParams));
        $parts['query'] = http_build_query($params);

        return $this->join($parts);
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


    /**
     * Splits url to parts
     * @param string $url
     * @return array
     */
    private function split($url)
    {
        $parts = parse_url($url) + array(
                'scheme' => 'http',
                'query' => '',
                'path' => '',
            );

        $parts['prefix'] = '';
        if (isset($parts['host'])) {
            $parts['prefix'] = sprintf('%s://%s', $parts['scheme'], $parts['host']);
        }

        return $parts;
    }

    /**
     * Joins url parts to url string
     * @param array $parts
     * @return string
     */
    private function join($parts)
    {
        return
            $parts['prefix'] .
            $parts['path'] .
            ($parts['query'] ? '?' . $parts['query'] : '') .
            (isset($parts['fragment']) ? '#' . $parts['fragment'] : '');
    }
}

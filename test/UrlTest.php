<?php

use urmaul\url\Url;

class ScraperHelperTest extends PHPUnit_Framework_TestCase
{
	public function absoluteProvider()
	{
		$good = 'http://site.com/swf/game.swf';
		$base = 'http://site.com/cat/game.htm';
		
		return array(
			array($good, $base, $good, 'Url is absolute'),
			array('//site.com/swf/game.swf', $base, $good, 'Url starts with "//"'),
			array('/swf/game.swf', $base, $good, 'Url starts with "/"'),
			array('?a=b', $base, $base . '?a=b', 'Url starts with "?"'),
			array('?a=b', $base . '?c=d', $base . '?a=b', 'Url starts with "?"'),
			array('../swf/game.swf', $base, $good, 'Url doesn\'t start with "/"'),
		);
	}
	
	/**
	 * @dataProvider absoluteProvider
	 */
	public function testAbsolute($url, $base, $expected, $message)
	{
		$this->assertEquals($expected, Url::from($url)->absolute($base), $message);
	}

	public function addParamProvider()
	{
		return array(
			array('/?a=b', '/', 'a', 'b'),
			array('/', '/', 'a', null),
			array('?a=b', '', 'a', 'b'),
			array('/c?a=b', '/c', 'a', 'b'),
			array('/c', '/c', 'a', null),
			array('/c/?a=b', '/c/', 'a', 'b'),
			array('/c/d?a=b', '/c/d', 'a', 'b'),
			array('/c/d/?a=b', '/c/d/', 'a', 'b'),
			array('/c/d/?a=b', '/c/d/?', 'a', 'b'),
			array('/c/d/?e=f&a=b', '/c/d/?e=f', 'a', 'b'),
			array('/c/d/?a=b', '/c/d/?a=e', 'a', 'b'),
			array('/c/d/?e=f&a=b', '/c/d/?e=f&a=e', 'a', 'b'),
			array('/?a=b#hello', '/#hello', 'a', 'b'),
			array('/c/d/?a=b#hello', '/c/d/#hello', 'a', 'b'),
			array('/c/d/?e=f&a=b#hello', '/c/d/?e=f&a=e#hello', 'a', 'b'),
		);
	}
	
	/**
	 * @dataProvider addParamProvider
	 * @param type $expected
	 * @param type $uri
	 * @param type $name
	 * @param type $value
	 */
	public function testAddParam($expected, $uri, $name, $value)
	{
		$url = new Url($uri);
		$actual = $url->addParam($name, $value);
		
		$this->assertEquals($expected, $actual);
	}
	
	/**
	 * @dataProvider addParamProvider
	 * @param type $expected
	 * @param type $uri
	 * @param type $name
	 * @param type $value
	 */
	public function testAddParamAbsolute($expected, $uri, $name, $value)
	{
		$host = 'http://domain.com' . (strncmp($expected, '/', 1) === 0 ? '' : '/');
		$url = new Url($host . $uri);
		$actual = $url->addParam($name, $value);
		
		$this->assertEquals($host . $expected, $actual);
	}

	public function addParamsProvider()
	{
		return array(
			array('/?a=b', '/', array('a' => 'b')),
			array('/', '/', array('a' => null)),
			array('?a=b', '', array('a' => 'b')),
			array('/c?a=b', '/c', array('a' => 'b')),
			array('/c', '/c', array('a' => null)),
			array('/c/?a=b', '/c/', array('a' => 'b')),
			array('/c/d?a=b', '/c/d', array('a' => 'b')),
			array('/c/d/?a=b', '/c/d/', array('a' => 'b')),
			array('/c/d/?a=b', '/c/d/?', array('a' => 'b')),
			array('/c/d/?e=f&a=b', '/c/d/?e=f', array('a' => 'b')),
			array('/c/d/?a=b', '/c/d/?a=e', array('a' => 'b')),
			array('/c/d/?e=f&a=b', '/c/d/?e=f&a=e', array('a' => 'b')),
			array('/?a=b#hello', '/#hello', array('a' => 'b')),
			array('/c/d/?a=b#hello', '/c/d/#hello', array('a' => 'b')),
			array('/c/d/?e=f&a=b#hello', '/c/d/?e=f&a=e#hello', array('a' => 'b')),
			
			array('/?a=b&c=d', '/', array('a' => 'b', 'c' => 'd')),
			array('/?c=d', '/', array('a' => null, 'c' => 'd')),
			array('?a=b&c=d', '', array('a' => 'b', 'c' => 'd')),
			array('/c?a=b&c=d', '/c', array('a' => 'b', 'c' => 'd')),
			array('/c?c=d', '/c', array('a' => null, 'c' => 'd')),
			array('/c/?a=b&c=d', '/c/', array('a' => 'b', 'c' => 'd')),
			array('/c/d?a=b&c=d', '/c/d', array('a' => 'b', 'c' => 'd')),
			array('/c/d/?a=b&c=d', '/c/d/', array('a' => 'b', 'c' => 'd')),
			array('/c/d/?a=b&c=d', '/c/d/?', array('a' => 'b', 'c' => 'd')),
			array('/c/d/?e=f&a=b&c=d', '/c/d/?e=f', array('a' => 'b', 'c' => 'd')),
			array('/c/d/?a=b&c=d', '/c/d/?a=e', array('a' => 'b', 'c' => 'd')),
			array('/c/d/?e=f&a=b&c=d', '/c/d/?e=f&a=e', array('a' => 'b', 'c' => 'd')),
			array('/?a=b&c=d#hello', '/#hello', array('a' => 'b', 'c' => 'd')),
			array('/c/d/?a=b&c=d#hello', '/c/d/#hello', array('a' => 'b', 'c' => 'd')),
			array('/c/d/?e=f&a=b&c=d#hello', '/c/d/?e=f&a=e#hello', array('a' => 'b', 'c' => 'd')),
		);
	}
	
	/**
	 * @dataProvider addParamsProvider
	 * @param type $expected
	 * @param type $uri
	 * @param array $addParams
	 */
	public function testAddParams($expected, $uri, $addParams)
	{
		$url = new Url($uri);
		$actual = $url->addParams($addParams);
		
		$this->assertEquals($expected, $actual);
	}
	
	/**
	 * @dataProvider addParamsProvider
	 * @param type $expected
	 * @param type $uri
	 * @param array $addParams
	 */
	public function testAddParamsAbsolute($expected, $uri, $addParams)
	{
		$host = 'http://domain.com' . (strncmp($expected, '/', 1) === 0 ? '' : '/');
		$url = new Url($host . $uri);
		$actual = $url->addParams($addParams);
		
		$this->assertEquals($host . $expected, $actual);
	}
}

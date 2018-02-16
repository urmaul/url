<?php

namespace urmaul\url\test;

use PHPUnit\Framework\TestCase;
use urmaul\url\Url;

class UrlTest extends TestCase
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
			array('', $base, $base, 'Empty url'),
		);
	}
	
	/**
	 * @dataProvider absoluteProvider
	 */
	public function testAbsolute($url, $base, $expected, $message)
	{
		$this->assertEquals($expected, Url::from($url)->absolute($base), $message);
	}

	
	public function rootRelativeProvider()
	{
		$good = '/swf/game.swf';
		$base = 'http://site.com/cat/game.htm';

		return array(
			array($good, $base, $good, 'Url is absolute'),
			array('//site.com/swf/game.swf', $base, $good, 'Url starts with "//"'),
			array('/swf/game.swf', $base, $good, 'Url starts with "/"'),
			array('?a=b', $base, '/cat/game.htm' . '?a=b', 'Url starts with "?"'),
			array('?a=b', $base . '?c=d', '/cat/game.htm' . '?a=b', 'Url starts with "?"'),
			array('../swf/game.swf', $base, $good, 'Url doesn\'t start with "/"'),
			array('', $base, '/cat/game.htm', 'Empty url'),
		);
	}

	/**
	 * @dataProvider rootRelativeProvider
	 */
	public function testRootRelative($url, $base, $expected, $message)
	{
		$this->assertEquals($expected, Url::from($url)->rootRelative($base), $message);
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
	
	
	public function removeParamProvider()
	{
		return array(
			array('/?a=b', '/', 'a'),
			array('/', '/', 'a'),
			array('?a=b', '', 'a'),
			array('/c?a=b', '/c', 'a'),
			array('/c', '/c', 'a'),
			array('/c/?a=b', '/c/', 'a'),
			array('/c/d?a=b', '/c/d', 'a'),
			array('/c/d/?a=b', '/c/d/', 'a'),
			array('/c/d/?a=b&a=c', '/c/d/', 'a'),
			array('/c/d/?e=f&a=b', '/c/d/?e=f', 'a'),
			array('/?a=b#hello', '/#hello', 'a'),
			array('/c/d/?a=b#hello', '/c/d/#hello', 'a'),
			array('/c/d/?e=f&a=b#hello', '/c/d/?e=f#hello', 'a'),
		);
	}
	
	/**
	 * @dataProvider removeParamProvider
	 * @param type $expected
	 * @param type $uri
	 * @param type $name
	 * @param type $value
	 */
	public function testRemoveParam($uri, $expected, $name)
	{
		$url = new Url($uri);
		$actual = $url->removeParam($name);
		
		$this->assertEquals($expected, $actual);
	}
	
	/**
	 * @dataProvider removeParamProvider
	 * @param type $expected
	 * @param type $uri
	 * @param type $name
	 * @param type $value
	 */
	public function testRemoveParamAbsolute($uri, $expected, $name)
	{
		$host = 'http://domain.com' . (strncmp($expected, '/', 1) === 0 ? '' : '/');
		$url = new Url($host . $uri);
		$actual = $url->removeParam($name);
		
		$this->assertEquals($host . $expected, $actual);
	}
	
	
	public function removeParamsProvider()
	{
		return array(
			array('/?a=b', '/', array('a')),
			array('/', '/', array('a')),
			array('?a=b', '', array('a')),
			array('/c?a=b', '/c', array('a')),
			array('/c', '/c', array('a')),
			array('/c/?a=b', '/c/', array('a')),
			array('/c/d?a=b', '/c/d', array('a')),
			array('/c/d/?a=b', '/c/d/', array('a')),
			array('/c/d/?e=f&a=b', '/c/d/?e=f', array('a')),
			array('/?a=b#hello', '/#hello', array('a')),
			array('/c/d/?a=b#hello', '/c/d/#hello', array('a')),
			array('/c/d/?e=f&a=b#hello', '/c/d/?e=f#hello', array('a')),
			
			array('/?a=b&c=d', '/', array('a', 'c')),
			array('/?c=d', '/', array('a', 'c')),
			array('?a=b&c=d', '', array('a', 'c')),
			array('/c?a=b&c=d', '/c', array('a', 'c')),
			array('/c?c=d', '/c', array('a', 'c')),
			array('/c/?a=b&c=d', '/c/', array('a', 'c')),
			array('/c/d?a=b&c=d', '/c/d', array('a', 'c')),
			array('/c/d/?a=b&c=d', '/c/d/', array('a', 'c')),
			array('/c/d/?e=f&a=b&c=d', '/c/d/?e=f', array('a', 'c')),
			array('/?a=b&c=d#hello', '/#hello', array('a', 'c')),
			array('/c/d/?a=b&c=d#hello', '/c/d/#hello', array('a', 'c')),
			array('/c/d/?e=f&a=b&c=d#hello', '/c/d/?e=f#hello', array('a', 'c')),
			
			array('/catalog/sale/?admuid=48a8e994fc696d1a88b5c5197b1fb03d&PAGEN_1=1', '/catalog/sale/?PAGEN_1=1', array('ext_meta_id', 'source_id', 'admuid')),
		);
	}
	
	/**
	 * @dataProvider removeParamsProvider
	 * @param type $expected
	 * @param type $uri
	 * @param array $addParams
	 */
	public function testRemoveParams($uri, $expected, $params)
	{
		$url = new Url($uri);
		$actual = $url->removeParams($params);
		
		$this->assertEquals($expected, $actual);
	}
	
	/**
	 * @dataProvider removeParamsProvider
	 * @param type $expected
	 * @param type $uri
	 * @param array $addParams
	 */
	public function testRemoveParamsAbsolute($uri, $expected, $params)
	{
		$host = 'http://domain.com' . (strncmp($expected, '/', 1) === 0 ? '' : '/');
		$url = new Url($host . $uri);
		$actual = $url->removeParams($params);
		
		$this->assertEquals($host . $expected, $actual);
	}
}

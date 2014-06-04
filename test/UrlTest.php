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
	public function testAbsolute($urlString, $base, $expected, $message)
	{
		$url = new Url($urlString);
		$this->assertEquals($expected, $url->absolute($base), $message);
	}

	public function addParamProvider()
	{
		return array(
			array('/?a=b', '/', 'a', 'b'),
			array('/?a=b', '', 'a', 'b'),
			array('/c?a=b', '/c', 'a', 'b'),
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
		$host = 'http://domain.com';
		$url = new Url($host . $uri);
		$actual = $url->addParam($name, $value);
		
		$this->assertEquals($host . $expected, $actual);
	}
}

<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Http;

use MyParcelCom\JsonApi\Http\UrlBuilder;
use PHPUnit\Framework\TestCase;

class UrlBuilderTest extends TestCase
{
    /** @test */
    public function testUrl()
    {
        $urlBuilder = new UrlBuilder();

        $urlBuilder->setUrl('http://username:password@hostname:9090/path?arg=value#anchor');
        $this->assertEquals('http://username:password@hostname:9090/path?arg=value#anchor', $urlBuilder->getUrl());
        $this->assertEquals('http://username:password@hostname:9090/path?arg=value#anchor', (string) $urlBuilder);

        $urlBuilder->setUrl('https://username@hostname:9090/path?arg=value#anchor');
        $this->assertEquals('https://username@hostname:9090/path?arg=value#anchor', $urlBuilder->getUrl());
    }

    /** @test */
    public function testGetQuery()
    {
        $urlBuilder = new UrlBuilder();
        $this->assertEquals(
            [],
            $urlBuilder->getQuery(),
            'Empty url should return an empty query array'
        );

        $urlBuilder = new UrlBuilder('http://url?foo=bar&bar=baz&baz=foo');
        $this->assertEquals(
            [
                'foo' => 'bar',
                'bar' => 'baz',
                'baz' => 'foo',
            ],
            $urlBuilder->getQuery(),
            'Query did not contain all query params in url'
        );
    }

    /** @test */
    public function testSetQuery()
    {
        $urlBuilder = new UrlBuilder('http://url?foo=bar');

        $urlBuilder->setQuery(['mode' => 'god']);
        $this->assertEquals(
            'http://url?mode=god',
            $urlBuilder->getUrl(),
            'Set query was not added to build url or did not override earlier set query'
        );
    }

    /** @test */
    public function testAddQuery()
    {
        $urlBuilder = new UrlBuilder('http://url?foo=bar');

        $urlBuilder->addQuery(['mode' => 'god']);
        $this->assertEquals(
            'http://url?foo=bar&mode=god',
            $urlBuilder->getUrl(),
            'Added query was not added to build url'
        );

        $urlBuilder->addQuery(['foo' => 'baz']);
        $this->assertContains(
            $urlBuilder->getUrl(),
            ['http://url?foo=baz&mode=god', 'http://url?mode=god&foo=baz'],
            'Added query did not override earlier set query '
        );
    }

    /** @test */
    public function testGetScheme()
    {
        $urlBuilder = new UrlBuilder();
        $this->assertNull($urlBuilder->getScheme());
        $urlBuilder = new UrlBuilder('http://url?foo=bar');
        $this->assertEquals('http', $urlBuilder->getScheme());
        $urlBuilder = new UrlBuilder('ftp://url?foo=bar');
        $this->assertEquals('ftp', $urlBuilder->getScheme());
    }

    /** @test */
    public function testSetScheme()
    {
        $urlBuilder = new UrlBuilder('http://url?foo=bar');
        $urlBuilder->setScheme('https');
        $this->assertEquals(
            'https://url?foo=bar',
            (string) $urlBuilder,
            'Set scheme was not set in build url'
        );
        $this->assertEquals(
            'https',
            $urlBuilder->getScheme(),
            'Set scheme was not retrieved with `getScheme()` method'
        );
    }

    /** @test */
    public function testGetHost()
    {
        $urlBuilder = new UrlBuilder();
        $this->assertNull($urlBuilder->getHost());
        $urlBuilder = new UrlBuilder('http://url?foo=bar');
        $this->assertEquals('url', $urlBuilder->getHost());
        $urlBuilder = new UrlBuilder('https://some.random-url.com?foo=bar#yolo');
        $this->assertEquals('some.random-url.com', $urlBuilder->getHost());
    }

    /** @test */
    public function testSetHost()
    {
        $urlBuilder = new UrlBuilder('http://url?foo=bar');
        $urlBuilder->setHost('some.random-url.com');
        $this->assertEquals(
            'http://some.random-url.com?foo=bar',
            (string) $urlBuilder,
            'Set host was not set in build url'
        );
        $this->assertEquals(
            'some.random-url.com',
            $urlBuilder->getHost(),
            'Set host was not retrieved with `getHost()` method'
        );
    }

    /** @test */
    public function testGetPort()
    {
        $urlBuilder = new UrlBuilder('https://some.random-url.com?foo=bar#yolo');
        $this->assertNull($urlBuilder->getPort());
        $urlBuilder = new UrlBuilder('http://url:9001?foo=bar');
        $this->assertEquals(9001, $urlBuilder->getPort());
    }

    /** @test */
    public function testSetPort()
    {
        $urlBuilder = new UrlBuilder('http://url:9001?foo=bar');
        $urlBuilder->setPort(1337);
        $this->assertEquals(
            'http://url:1337?foo=bar',
            (string) $urlBuilder,
            'Set port was not set in build url'
        );
        $this->assertEquals(
            1337,
            $urlBuilder->getPort(),
            'Set port was not retrieved with `getPort()` method'
        );
    }

    /** @test */
    public function testGetUser()
    {
        $urlBuilder = new UrlBuilder();
        $this->assertNull($urlBuilder->getUser());
        $urlBuilder = new UrlBuilder('http://sam@url?foo=bar');
        $this->assertEquals('sam', $urlBuilder->getUser());
        $urlBuilder = new UrlBuilder('https://thomas:martin@some.random-url.com?foo=bar#yolo');
        $this->assertEquals('thomas', $urlBuilder->getUser());
    }

    /** @test */
    public function testSetUser()
    {
        $urlBuilder = new UrlBuilder('http://fidel@url?foo=bar');
        $urlBuilder->setUser('patrick');
        $this->assertEquals(
            'http://patrick@url?foo=bar',
            (string) $urlBuilder,
            'Set user was not set in build url'
        );
        $this->assertEquals(
            'patrick',
            $urlBuilder->getUser(),
            'Set user was not retrieved with `getUser()` method'
        );
    }

    /** @test */
    public function testGetPassword()
    {
        $urlBuilder = new UrlBuilder();
        $this->assertNull($urlBuilder->getPassword());
        $urlBuilder = new UrlBuilder('http://sam@url?foo=bar');
        $this->assertNull($urlBuilder->getPassword());
        $urlBuilder = new UrlBuilder('https://thomas:martin@some.random-url.com?foo=bar#yolo');
        $this->assertEquals('martin', $urlBuilder->getPassword());
    }

    /** @test */
    public function testSetPassword()
    {
        $urlBuilder = new UrlBuilder('http://fidel:secret@url?foo=bar');
        $urlBuilder->setPassword('welkom123');
        $this->assertEquals(
            'http://fidel:welkom123@url?foo=bar',
            (string) $urlBuilder,
            'Set password was not set in build url'
        );
        $this->assertEquals(
            'welkom123',
            $urlBuilder->getPassword(),
            'Set password was not retrieved with `getPassword()` method'
        );
    }

    /** @test */
    public function testGetPath()
    {
        $urlBuilder = new UrlBuilder();
        $this->assertNull($urlBuilder->getPath());
        $urlBuilder = new UrlBuilder('http://sam@url/look/i/am/an/amazing/path?foo=bar');
        $this->assertEquals('/look/i/am/an/amazing/path', $urlBuilder->getPath());
        $urlBuilder = new UrlBuilder('https://thomas:martin@some.random-url.com/some-other-path?foo=bar#yolo');
        $this->assertEquals('/some-other-path', $urlBuilder->getPath());
    }

    /** @test */
    public function testSetPath()
    {
        $urlBuilder = new UrlBuilder('http://fidel:secret@url/to/narnia?foo=bar');
        $urlBuilder->setPath('/to/somwhere/else');
        $this->assertEquals(
            'http://fidel:secret@url/to/somwhere/else?foo=bar',
            (string) $urlBuilder,
            'Set password was not set in build url'
        );
        $this->assertEquals(
            '/to/somwhere/else',
            $urlBuilder->getPath(),
            'Set path was not retrieved with `getPath()` method'
        );
    }

    /** @test */
    public function testGetFragment()
    {
        $urlBuilder = new UrlBuilder();
        $this->assertNull($urlBuilder->getFragment());
        $urlBuilder = new UrlBuilder('http://sam@url?foo=bar#i-frag-your-ment');
        $this->assertEquals('i-frag-your-ment', $urlBuilder->getFragment());
        $urlBuilder = new UrlBuilder('https://thomas:martin@some.random-url.com?foo=bar#yolo');
        $this->assertEquals('yolo', $urlBuilder->getFragment());
    }

    /** @test */
    public function testSetFragment()
    {
        $urlBuilder = new UrlBuilder('http://url?foo=bar#hashtag');
        $urlBuilder->setFragment('mom');
        $this->assertEquals(
            'http://url?foo=bar#mom',
            (string) $urlBuilder,
            'Set fragment was not set in build url'
        );
        $this->assertEquals(
            'mom',
            $urlBuilder->getFragment(),
            'Set path was not retrieved with `getFragment()` method'
        );
    }
}

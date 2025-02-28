<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Http;

use MyParcelCom\JsonApi\Http\UrlBuilder;
use PHPUnit\Framework\TestCase;

class UrlBuilderTest extends TestCase
{
    public function testUrl()
    {
        $urlBuilder = new UrlBuilder();

        $urlBuilder->setUrl('https://username:password@hostname:9090/path?arg=value#anchor');
        $this->assertEquals('https://username:password@hostname:9090/path?arg=value#anchor', $urlBuilder->getUrl());
        $this->assertEquals('https://username:password@hostname:9090/path?arg=value#anchor', (string) $urlBuilder);

        $urlBuilder->setUrl('https://username@hostname:9090/path?arg=value#anchor');
        $this->assertEquals('https://username@hostname:9090/path?arg=value#anchor', $urlBuilder->getUrl());
    }

    public function testGetQuery()
    {
        $urlBuilder = new UrlBuilder();
        $this->assertEquals(
            [],
            $urlBuilder->getQuery(),
            'Empty url should return an empty query array',
        );

        $urlBuilder = new UrlBuilder('https://url?foo=bar&bar=baz&baz=foo');
        $this->assertEquals(
            [
                'foo' => 'bar',
                'bar' => 'baz',
                'baz' => 'foo',
            ],
            $urlBuilder->getQuery(),
            'Query did not contain all query params in url',
        );
    }

    public function testSetQuery()
    {
        $urlBuilder = new UrlBuilder('https://url?foo=bar');

        $urlBuilder->setQuery(['mode' => 'god']);
        $this->assertEquals(
            'https://url?mode=god',
            $urlBuilder->getUrl(),
            'Set query was not added to build url or did not override earlier set query',
        );
    }

    public function testAddQuery()
    {
        $urlBuilder = new UrlBuilder('https://url?foo=bar');

        $urlBuilder->addQuery(['mode' => 'god']);
        $this->assertEquals(
            'https://url?foo=bar&mode=god',
            $urlBuilder->getUrl(),
            'Added query was not added to build url',
        );

        $urlBuilder->addQuery(['foo' => 'baz']);
        $this->assertContains(
            $urlBuilder->getUrl(),
            ['https://url?foo=baz&mode=god', 'https://url?mode=god&foo=baz'],
            'Added query did not override earlier set query ',
        );
    }

    public function testGetScheme()
    {
        $urlBuilder = new UrlBuilder();
        $this->assertNull($urlBuilder->getScheme());
        $urlBuilder = new UrlBuilder('https://url?foo=bar');
        $this->assertEquals('https', $urlBuilder->getScheme());
        $urlBuilder = new UrlBuilder('ftp://url?foo=bar');
        $this->assertEquals('ftp', $urlBuilder->getScheme());
    }

    public function testSetScheme()
    {
        $urlBuilder = new UrlBuilder('https://url?foo=bar');
        $urlBuilder->setScheme('https');
        $this->assertEquals(
            'https://url?foo=bar',
            (string) $urlBuilder,
            'Set scheme was not set in build url',
        );
        $this->assertEquals(
            'https',
            $urlBuilder->getScheme(),
            'Set scheme was not retrieved with `getScheme()` method',
        );
    }

    public function testGetHost()
    {
        $urlBuilder = new UrlBuilder();
        $this->assertNull($urlBuilder->getHost());
        $urlBuilder = new UrlBuilder('https://url?foo=bar');
        $this->assertEquals('url', $urlBuilder->getHost());
        $urlBuilder = new UrlBuilder('https://some.random-url.com?foo=bar#yolo');
        $this->assertEquals('some.random-url.com', $urlBuilder->getHost());
    }

    public function testSetHost()
    {
        $urlBuilder = new UrlBuilder('https://url?foo=bar');
        $urlBuilder->setHost('some.random-url.com');
        $this->assertEquals(
            'https://some.random-url.com?foo=bar',
            (string) $urlBuilder,
            'Set host was not set in build url',
        );
        $this->assertEquals(
            'some.random-url.com',
            $urlBuilder->getHost(),
            'Set host was not retrieved with `getHost()` method',
        );
    }

    public function testGetPort()
    {
        $urlBuilder = new UrlBuilder('https://some.random-url.com?foo=bar#yolo');
        $this->assertNull($urlBuilder->getPort());
        $urlBuilder = new UrlBuilder('https://url:9001?foo=bar');
        $this->assertEquals(9001, $urlBuilder->getPort());
    }

    public function testSetPort()
    {
        $urlBuilder = new UrlBuilder('https://url:9001?foo=bar');
        $urlBuilder->setPort(1337);
        $this->assertEquals(
            'https://url:1337?foo=bar',
            (string) $urlBuilder,
            'Set port was not set in build url',
        );
        $this->assertEquals(
            1337,
            $urlBuilder->getPort(),
            'Set port was not retrieved with `getPort()` method',
        );
    }

    public function testGetUser()
    {
        $urlBuilder = new UrlBuilder();
        $this->assertNull($urlBuilder->getUser());
        $urlBuilder = new UrlBuilder('https://sam@url?foo=bar');
        $this->assertEquals('sam', $urlBuilder->getUser());
        $urlBuilder = new UrlBuilder('https://thomas:martin@some.random-url.com?foo=bar#yolo');
        $this->assertEquals('thomas', $urlBuilder->getUser());
    }

    public function testSetUser()
    {
        $urlBuilder = new UrlBuilder('https://fidel@url?foo=bar');
        $urlBuilder->setUser('patrick');
        $this->assertEquals(
            'https://patrick@url?foo=bar',
            (string) $urlBuilder,
            'Set user was not set in build url',
        );
        $this->assertEquals(
            'patrick',
            $urlBuilder->getUser(),
            'Set user was not retrieved with `getUser()` method',
        );
    }

    public function testGetPassword()
    {
        $urlBuilder = new UrlBuilder();
        $this->assertNull($urlBuilder->getPassword());
        $urlBuilder = new UrlBuilder('https://sam@url?foo=bar');
        $this->assertNull($urlBuilder->getPassword());
        $urlBuilder = new UrlBuilder('https://thomas:martin@some.random-url.com?foo=bar#yolo');
        $this->assertEquals('martin', $urlBuilder->getPassword());
    }

    public function testSetPassword()
    {
        $urlBuilder = new UrlBuilder('https://fidel:secret@url?foo=bar');
        $urlBuilder->setPassword('welkom123');
        $this->assertEquals(
            'https://fidel:welkom123@url?foo=bar',
            (string) $urlBuilder,
            'Set password was not set in build url',
        );
        $this->assertEquals(
            'welkom123',
            $urlBuilder->getPassword(),
            'Set password was not retrieved with `getPassword()` method',
        );
    }

    public function testGetPath()
    {
        $urlBuilder = new UrlBuilder();
        $this->assertNull($urlBuilder->getPath());
        $urlBuilder = new UrlBuilder('https://sam@url/look/i/am/an/amazing/path?foo=bar');
        $this->assertEquals('/look/i/am/an/amazing/path', $urlBuilder->getPath());
        $urlBuilder = new UrlBuilder('https://thomas:martin@some.random-url.com/some-other-path?foo=bar#yolo');
        $this->assertEquals('/some-other-path', $urlBuilder->getPath());
    }

    public function testSetPath()
    {
        $urlBuilder = new UrlBuilder('https://fidel:secret@url/to/narnia?foo=bar');
        $urlBuilder->setPath('/to/somwhere/else');
        $this->assertEquals(
            'https://fidel:secret@url/to/somwhere/else?foo=bar',
            (string) $urlBuilder,
            'Set password was not set in build url',
        );
        $this->assertEquals(
            '/to/somwhere/else',
            $urlBuilder->getPath(),
            'Set path was not retrieved with `getPath()` method',
        );
    }

    public function testGetFragment()
    {
        $urlBuilder = new UrlBuilder();
        $this->assertNull($urlBuilder->getFragment());
        $urlBuilder = new UrlBuilder('https://sam@url?foo=bar#i-frag-your-ment');
        $this->assertEquals('i-frag-your-ment', $urlBuilder->getFragment());
        $urlBuilder = new UrlBuilder('https://thomas:martin@some.random-url.com?foo=bar#yolo');
        $this->assertEquals('yolo', $urlBuilder->getFragment());
    }

    public function testSetFragment()
    {
        $urlBuilder = new UrlBuilder('https://url?foo=bar#hashtag');
        $urlBuilder->setFragment('mom');
        $this->assertEquals(
            'https://url?foo=bar#mom',
            (string) $urlBuilder,
            'Set fragment was not set in build url',
        );
        $this->assertEquals(
            'mom',
            $urlBuilder->getFragment(),
            'Set path was not retrieved with `getFragment()` method',
        );
    }
}

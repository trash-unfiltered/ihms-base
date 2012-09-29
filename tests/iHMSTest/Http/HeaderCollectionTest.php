<?php
namespace iHMSTest\Http;

use iHMS\Http\HeaderCollection;
use iHMS\Http\Header\Header;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2012-09-12 at 18:35:49.
 *
 * @group skipped
 */
class HeaderCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Iterator and \Countable interfaces
     */
    public function testHeaderCollectionImplementsProperInterfaces()
    {
        $headers = new HeaderCollection();
        $this->assertInstanceOf('Iterator', $headers);
        $this->assertInstanceOf('Countable', $headers);
    }

    /**
     * @covers iHMS\Http\HeaderCollection::__construct
     */
    public function test__construct()
    {
        $headerCollection = new HeaderCollection();
        $this->assertEquals(0, count($headerCollection));

        $headerCollection = new HeaderCollection(
            array(
                'foo' => 'bar',
                'bar' => array('foo', 'bar')
            )
        );
        $this->assertEquals(3, count($headerCollection));
        $this->assertTrue($headerCollection->hasHeader('foo'));
        $this->assertTrue($headerCollection->hasHeader('bar'));
    }

    /**
     * @covers iHMS\Http\HeaderCollection::addHeader
     */
    public function testAddHeader()
    {
        $headerCollection = new HeaderCollection();
        $headerCollection->addHeader($foo = new Header('foo', 'bar'));
        $this->assertEquals(1, count($headerCollection));
        $this->assertSame($foo, $headerCollection->getHeader('foo'));
        $this->assertEquals('bar', $headerCollection->getHeader('foo')->getFieldValue());
        $headerCollection = new HeaderCollection();
        $headerCollection->addHeader($foo = new Header('foo', 'bar'));
        $this->assertEquals(1, count($headerCollection));
        $headerCollection->addHeader($bar = new Header('foo', 'baz'));
        $this->assertEquals(2, count($headerCollection));
    }

    /**
     * @covers iHMS\Http\HeaderCollection::addRawHeader
     */
    public function testAddRawHeader()
    {
        $headerCollection = new HeaderCollection();
        $headerCollection->addRawHeader('foo', 'bar');
        $this->assertEquals(1, count($headerCollection));
        $this->assertTrue($headerCollection->hasHeader('foo'));
        $this->setExpectedException('\InvalidArgumentException', 'must be a non-empty string');
        $headerCollection->addRawHeader('', 'bar');
        $headerCollection->addRawHeader(array(), 'bar');
    }

    /**
     * @covers iHMS\Http\HeaderCollection::addHeaders
     */
    public function testAddHeaders()
    {
        $headerCollection = new HeaderCollection();
        $headerCollection->addHeaders(
            array(
                'foo' => 'bar',
                'bar' => array('foo', 'bar')
            )
        );
        $this->assertEquals(3, count($headerCollection));
        $this->assertTrue($headerCollection->hasHeader('foo'));
        $this->assertTrue($headerCollection->hasHeader('bar'));
        $anotherHeaderCollection2 = new HeaderCollection();
        $anotherHeaderCollection2->addHeaders($headerCollection);
        $this->assertEquals(3, count($anotherHeaderCollection2));
        $this->assertTrue($anotherHeaderCollection2->hasHeader('foo'));
        $this->assertTrue($anotherHeaderCollection2->hasHeader('bar'));
        $this->setExpectedException('\InvalidArgumentException', 'expects an array or HeaderCollection object');
        $headerCollection = new HeaderCollection();
        $headerCollection->addHeaders('');
    }

    /**
     * @covers iHMS\Http\HeaderCollection::getHeader
     */
    public function testGetHeader()
    {
        $headerCollection = new HeaderCollection(
            array(
                $foo = new Header('foo', 'bar'),
                new Header('bar', 'foo'),
                new Header('bar', 'baz')
            )
        );
        $this->assertEquals(3, count($headerCollection));
        $header = $headerCollection->getHeader('foo');
        $this->assertInstanceOf('iHMS\Http\Header\Header', $header);
        $this->assertEquals('Foo', $header->getFieldName());
        $this->assertSame($foo, $header);
        $this->assertNotSame($foo, new Header('foo', 'bar'));
        $headerCollection = $headerCollection->getHeader('bar');
        $this->assertInstanceOf('iHMS\Http\HeaderCollection', $headerCollection);

        foreach ($headerCollection as $header) {
            $this->assertInstanceOf('iHMS\Http\Header\Header', $header);
        }
    }

    /**
     * @covers iHMS\Http\HeaderCollection::getFirstMatchHeader
     */
    public function testGetFirstMatchHeader()
    {
        $headerCollection = new HeaderCollection(
            array(
                'foo' => 'bar',
                'bar' => array('foo', 'bar')
            )
        );
        $header = $headerCollection->getFirstMatchHeader(array('qux', 'foo', 'bar', 'baz'));
        $this->assertInstanceOf('iHMS\Http\Header\Header', $header);
        $this->assertEquals('Foo', $header->getFieldName());
        $this->assertInstanceOf(
            'iHMS\Http\HeaderCollection', $headerCollection->getFirstMatchHeader(array('qux', 'bar', 'foo', 'baz'))
        );
    }

    /**
     * @covers iHMS\Http\HeaderCollection::getFirstPartialMatchHeader
     */
    /*
    public function testGetFirstPartialMatchHeader()
    {
        $headerCollection = new HeaderCollection(
            array(
                'foo' => 'bar',
                'bar' => array('foo', 'bar')
            )
        );
        $header = $headerCollection->getFirstPartialMatchHeader(array('qux', 'foo', 'bar', 'baz'));
        $this->assertInstanceOf('iHMS\Http\Header\Header', $header);
        $this->assertEquals('Foo', $header->getFieldName());
        $this->assertInstanceOf(
            'iHMS\Http\HeaderCollection', $headerCollection->getFirstPartialMatchHeader(array('qux', 'bar', 'foo', 'baz'))
        );
    }
    */

    /**
     * @covers iHMS\Http\HeaderCollection::removeHeader
     */
    public function testRemoveHeader()
    {
        $headerCollection = new HeaderCollection();
        $headerCollection->addHeader($foo = new Header('foo', 'bar'));
        $headerCollection->addHeader($baz = new Header('Baz', 'foo'));
        $this->assertEquals(2, $headerCollection->count());
        $this->assertTrue($headerCollection->removeHeader($foo));
        $this->assertEquals(1, $headerCollection->count());
        $this->assertTrue($headerCollection->removeHeader($baz));
        $this->assertEquals(0, $headerCollection->count());
        $this->assertFalse($headerCollection->removeHeader(new Header('foo', 'bar')));
    }

    /**
     * @covers iHMS\Http\HeaderCollection::removeHeaderByName
     */
    public function testRemoveHeaderByName()
    {
        $headerCollection = new HeaderCollection(
            array(
                'foo' => 'bar',
                'Baz' => 'foo'
            )
        );
        $this->assertEquals(2, $headerCollection->count());
        $this->assertTrue($headerCollection->removeHeaderByName('foo'));
        $this->assertEquals(1, $headerCollection->count());
        $this->assertTrue($headerCollection->removeHeaderByName('baz'));
        $this->assertEquals(0, $headerCollection->count());
        $this->assertFalse($headerCollection->removeHeaderByName('bar'));
    }

    /**
     * @covers iHMS\Http\HeaderCollection::removeHeaders
     */
    public function testRemoveHeaders()
    {
        $headerCollection = new HeaderCollection(
            array(
                'foo' => 'bar',
                'bar' => 'foo'
            )
        );
        $this->assertEquals(2, $headerCollection->count());
        $headerCollection->removeHeaders();
        $this->assertEquals(0, $headerCollection->count());
    }

    /**
     * @covers iHMS\Http\HeaderCollection::hasHeader
     */
    public function testHasHeader()
    {
        $headerCollection = new HeaderCollection(
            array(
                'foo' => 'bar',
                'bar' => 'foo'
            )
        );
        $this->assertFalse($headerCollection->hasHeader('baz'));
        $this->assertTrue($headerCollection->hasHeader('foo'));
        $this->assertTrue($headerCollection->hasHeader('Foo'));
    }

    /**
     * @covers iHMS\Http\HeaderCollection::__tostring
     */
    public function test__tostring()
    {
        $headerCollection = new HeaderCollection();
        $headerCollection->addHeaders(
            array(
                'Foo' => 'bar',
                'Baz' => 'baz'
            )
        );
        $this->assertEquals("Foo: bar\r\nBaz: baz\r\n", $headerCollection);
        $headerCollection = new HeaderCollection();
        $header1 = new Header('foo', 'bar');
        $header2 = new Header('foo', 'baz');
        $headerCollection->addHeader($header1);
        $headerCollection->addHeader($header2);
        $expected = array(
            'Foo: ' . $header1->getFieldValue(),
            'Foo: ' . $header2->getFieldValue(),
        );
        $expected = implode("\r\n", $expected) . "\r\n";
        $this->assertEquals($expected, $headerCollection);
    }

    /**
     * @covers iHMS\Http\HeaderCollection::toArray
     */
    public function testToArray()
    {
        $headerCollection = new HeaderCollection();
        $headerCollection->addHeaders(
            array(
                'Foo' => 'bar',
                'Baz' => 'baz'
            )
        );
        $this->assertEquals(array('Foo' => 'bar', 'Baz' => 'baz'), $headerCollection->toArray());
        $headerCollection = new HeaderCollection();
        $header1 = new Header('foo', 'bar');
        $header2 = new Header('foo', 'baz');
        $headerCollection->addHeader($header1);
        $headerCollection->addHeader($header2);
        $expected = array(
            'Foo' => array(
                $header1->getFieldValue(),
                $header2->getFieldValue(),
            ),
        );
        $this->assertEquals($expected, $headerCollection->toArray());
    }

    /**
     * @cover iHMS\Http\HeaderCollection iteration
     */
    public function testHeaderCollectionCanBeIterated()
    {
        $headers = new HeaderCollection();
        $headers->addHeaders(
            array(
                'Foo' => 'bar',
                'Baz' => 'baz')
        );
        $iterations = 0;
        /** @var $header Header */
        foreach ($headers as $index => $header) {
            $iterations++;
            $this->assertInstanceOf('iHMS\Http\Header\Header', $header);
            switch ($index) {
                case 0:
                    $this->assertEquals('bar', $header->getFieldValue());
                    break;
                case 1:
                    $this->assertEquals('baz', $header->getFieldValue());
                    break;
                default:
                    $this->fail('Invalid index returned from iterator');
            }
        }
        $this->assertEquals(2, $iterations);
    }
}

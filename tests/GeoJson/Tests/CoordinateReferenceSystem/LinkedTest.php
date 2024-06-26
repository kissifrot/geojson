<?php

namespace GeoJson\Tests\CoordinateReferenceSystem;

use GeoJson\CoordinateReferenceSystem\CoordinateReferenceSystem;
use GeoJson\CoordinateReferenceSystem\Linked;
use GeoJson\Exception\UnserializationException;
use PHPUnit\Framework\TestCase;

class LinkedTest extends TestCase
{
    public function testIsSubclassOfCoordinateReferenceSystem(): void
    {
        $this->assertTrue(is_subclass_of(
            Linked::class,
            CoordinateReferenceSystem::class
        ));
    }

    public function testSerialization(): void
    {
        $crs = new Linked('http://example.com/crs/42', 'proj4');

        $expected = array(
            'type' => 'link',
            'properties' => array(
                'href' => 'http://example.com/crs/42',
                'type' => 'proj4',
            ),
        );

        $this->assertSame('link', $crs->getType());
        $this->assertSame($expected['properties'], $crs->getProperties());
        $this->assertSame($expected, $crs->jsonSerialize());
    }

    public function testSerializationWithoutHrefType(): void
    {
        $crs = new Linked('http://example.com/crs/42');

        $expected = array(
            'type' => 'link',
            'properties' => array(
                'href' => 'http://example.com/crs/42',
            ),
        );

        $this->assertSame($expected, $crs->jsonSerialize());
    }

    /**
     * @dataProvider provideJsonDecodeAssocOptions
     * @group functional
     */
    public function testUnserialization($assoc): void
    {
        $json = <<<'JSON'
{
    "type": "link",
    "properties": {
        "href": "http://example.com/crs/42",
        "type": "proj4"
    }
}
JSON;

        $json = json_decode($json, $assoc);
        $crs = CoordinateReferenceSystem::jsonUnserialize($json);

        $expectedProperties = array(
            'href' => 'http://example.com/crs/42',
            'type' => 'proj4',
        );

        $this->assertInstanceOf(Linked::class, $crs);
        $this->assertSame('link', $crs->getType());
        $this->assertSame($expectedProperties, $crs->getProperties());
    }

    /**
     * @dataProvider provideJsonDecodeAssocOptions
     * @group functional
     */
    public function testUnserializationWithoutHrefType($assoc): void
    {
        $json = <<<'JSON'
{
    "type": "link",
    "properties": {
        "href": "http://example.com/crs/42"
    }
}
JSON;

        $json = json_decode($json, $assoc);
        $crs = CoordinateReferenceSystem::jsonUnserialize($json);

        $expectedProperties = array('href' => 'http://example.com/crs/42');

        $this->assertInstanceOf(Linked::class, $crs);
        $this->assertSame('link', $crs->getType());
        $this->assertSame($expectedProperties, $crs->getProperties());
    }

    public function provideJsonDecodeAssocOptions(): array
    {
        return array(
            'assoc=true' => array(true),
            'assoc=false' => array(false),
        );
    }

    public function testUnserializationShouldRequirePropertiesArrayOrObject(): void
    {
        $this->expectException(UnserializationException::class);
        $this->expectExceptionMessage('Linked CRS expected "properties" property of type array or object');
        CoordinateReferenceSystem::jsonUnserialize(array('type' => 'link', 'properties' => null));
    }

    public function testUnserializationShouldRequireHrefProperty(): void
    {
        $this->expectException(UnserializationException::class);
        $this->expectExceptionMessage('Linked CRS expected "properties.href" property of type string');
        CoordinateReferenceSystem::jsonUnserialize(array('type' => 'link', 'properties' => array()));
    }
}

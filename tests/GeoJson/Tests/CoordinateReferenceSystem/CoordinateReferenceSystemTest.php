<?php

namespace GeoJson\Tests\CoordinateReferenceSystem;

use GeoJson\CoordinateReferenceSystem\CoordinateReferenceSystem;
use GeoJson\Exception\UnserializationException;
use PHPUnit\Framework\TestCase;

class CoordinateReferenceSystemTest extends TestCase
{
    public function testIsJsonSerializable(): void
    {
        $this->assertInstanceOf(
            'JsonSerializable',
            $this->getMockBuilder(CoordinateReferenceSystem::class)->getMock()
        );
    }

    public function testIsJsonUnserializable(): void
    {
        $this->assertInstanceOf(
            'GeoJson\JsonUnserializable',
            $this->getMockBuilder(CoordinateReferenceSystem::class)->getMock()
        );
    }

    public function testUnserializationShouldRequireArrayOrObject(): void
    {
        $this->expectException(UnserializationException::class);
        $this->expectExceptionMessage('CRS expected value of type array or object');
        CoordinateReferenceSystem::jsonUnserialize(null);
    }

    public function testUnserializationShouldRequireTypeField(): void
    {
        $this->expectException(UnserializationException::class);
        $this->expectExceptionMessage('CRS expected "type" property of type string, none given');
        CoordinateReferenceSystem::jsonUnserialize(array('properties' => array()));
    }

    public function testUnserializationShouldRequirePropertiesField(): void
    {
        $this->expectException(UnserializationException::class);
        $this->expectExceptionMessage('CRS expected "properties" property of type array or object, none given');
        CoordinateReferenceSystem::jsonUnserialize(array('type' => 'foo'));
    }

    public function testUnserializationShouldRequireValidType(): void
    {
        $this->expectException(UnserializationException::class);
        $this->expectExceptionMessage('Invalid CRS type "foo"');
        CoordinateReferenceSystem::jsonUnserialize(array('type' => 'foo', 'properties' => array()));
    }
}

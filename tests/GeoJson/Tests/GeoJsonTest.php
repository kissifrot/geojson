<?php

namespace GeoJson\Tests;

use GeoJson\GeoJson;
use PHPUnit\Framework\TestCase;
use GeoJson\JsonUnserializable;
use GeoJson\Geometry\Point;
use GeoJson\BoundingBox;
use GeoJson\CoordinateReferenceSystem\Named;

class GeoJsonTest extends TestCase
{
    public function testIsJsonSerializable(): void
    {
        $this->assertInstanceOf('JsonSerializable', $this->getMockBuilder(GeoJson::class)->getMock());
    }

    public function testIsJsonUnserializable(): void
    {
        $this->assertInstanceOf(JsonUnserializable::class, $this->getMockBuilder(GeoJson::class)->getMock());
    }

    /**
     * @dataProvider provideJsonDecodeAssocOptions
     * @group functional
     */
    public function testUnserializationWithBoundingBox($assoc): void
    {
        $json = <<<'JSON'
{
    "type": "Point",
    "coordinates": [1, 1],
    "bbox": [-180.0, -90.0, 180.0, 90.0]
}
JSON;

        $json = json_decode($json, $assoc);
        $point = GeoJson::jsonUnserialize($json);

        $this->assertInstanceOf(Point::class, $point);
        $this->assertSame('Point', $point->getType());
        $this->assertSame(array(1, 1), $point->getCoordinates());

        $boundingBox = $point->getBoundingBox();

        $this->assertInstanceOf(BoundingBox::class, $boundingBox);
        $this->assertSame(array(-180.0, -90.0, 180.0, 90.0), $boundingBox->getBounds());
    }

    /**
     * @dataProvider provideJsonDecodeAssocOptions
     * @group functional
     */
    public function testUnserializationWithCrs($assoc): void
    {
        $json = <<<'JSON'
{
    "type": "Point",
    "coordinates": [1, 1],
    "crs": {
        "type": "name",
        "properties": {
            "name": "urn:ogc:def:crs:OGC:1.3:CRS84"
        }
    }
}
JSON;

        $json = json_decode($json, $assoc);
        $point = GeoJson::jsonUnserialize($json);

        $this->assertInstanceOf(Point::class, $point);
        $this->assertSame('Point', $point->getType());
        $this->assertSame(array(1, 1), $point->getCoordinates());

        $crs = $point->getCrs();

        $expectedProperties = array('name' => 'urn:ogc:def:crs:OGC:1.3:CRS84');

        $this->assertInstanceOf(Named::class, $crs);
        $this->assertSame('name', $crs->getType());
        $this->assertSame($expectedProperties, $crs->getProperties());
    }

    public function provideJsonDecodeAssocOptions(): array
    {
        return array(
            'assoc=true' => array(true),
            'assoc=false' => array(false),
        );
    }
}

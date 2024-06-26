<?php

namespace GeoJson\Tests\Geometry;

use GeoJson\GeoJson;
use GeoJson\Geometry\MultiPoint;
use GeoJson\Geometry\Point;
use GeoJson\Tests\BaseGeoJsonTest;
use GeoJson\Geometry\Geometry;

class MultiPointTest extends BaseGeoJsonTest
{
    public function createSubjectWithExtraArguments(array $extraArgs)
    {
        $class = new \ReflectionClass(MultiPoint::class);

        return $class->newInstanceArgs(array_merge(
            array(array(
                array(1, 1),
                array(2, 2),
            )),
            $extraArgs
        ));
    }

    public function testIsSubclassOfGeometry(): void
    {
        $this->assertTrue(is_subclass_of(MultiPoint::class, Geometry::class));
    }

    public function testConstructionFromPointObjects(): void
    {
        $multiPoint1 = new MultiPoint(array(
            new Point(array(1, 1)),
            new Point(array(2, 2)),
        ));

        $multiPoint2 = new MultiPoint(array(
            array(1, 1),
            array(2, 2),
        ));

        $this->assertSame($multiPoint1->getCoordinates(), $multiPoint2->getCoordinates());
    }

    public function testSerialization(): void
    {
        $coordinates = array(array(1, 1), array(2, 2));
        $multiPoint = new MultiPoint($coordinates);

        $expected = array(
            'type' => 'MultiPoint',
            'coordinates' => $coordinates,
        );

        $this->assertSame('MultiPoint', $multiPoint->getType());
        $this->assertSame($coordinates, $multiPoint->getCoordinates());
        $this->assertSame($expected, $multiPoint->jsonSerialize());
    }

    /**
     * @dataProvider provideJsonDecodeAssocOptions
     * @group functional
     */
    public function testUnserialization($assoc): void
    {
        $json = <<<'JSON'
{
    "type": "MultiPoint",
    "coordinates": [
        [1, 1],
        [2, 2]
    ]
}
JSON;

        $json = json_decode($json, $assoc);
        $multiPoint = GeoJson::jsonUnserialize($json);

        $expectedCoordinates = array(array(1, 1), array(2, 2));

        $this->assertInstanceOf(MultiPoint::class, $multiPoint);
        $this->assertSame('MultiPoint', $multiPoint->getType());
        $this->assertSame($expectedCoordinates, $multiPoint->getCoordinates());
    }

    public function provideJsonDecodeAssocOptions(): array
    {
        return array(
            'assoc=true' => array(true),
            'assoc=false' => array(false),
        );
    }
}

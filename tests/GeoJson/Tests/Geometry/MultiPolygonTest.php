<?php

namespace GeoJson\Tests\Geometry;

use GeoJson\GeoJson;
use GeoJson\Geometry\MultiPolygon;
use GeoJson\Geometry\Polygon;
use GeoJson\Tests\BaseGeoJsonTest;
use GeoJson\Geometry\Geometry;

class MultiPolygonTest extends BaseGeoJsonTest
{
    public function createSubjectWithExtraArguments(array $extraArgs)
    {
        $class = new \ReflectionClass(MultiPolygon::class);

        return $class->newInstanceArgs(array_merge(
            array(array(
                array(array(array(0, 0), array(0, 4), array(4, 4), array(4, 0), array(0, 0))),
                array(array(array(1, 1), array(1, 3), array(3, 3), array(3, 1), array(1, 1))),
            )),
            $extraArgs
        ));
    }

    public function testIsSubclassOfGeometry(): void
    {
        $this->assertTrue(is_subclass_of(MultiPolygon::class, Geometry::class));
    }

    public function testConstructionFromPolygonObjects(): void
    {
        $multiPolygon1 = new MultiPolygon(array(
            new Polygon(array(array(array(0, 0), array(0, 4), array(4, 4), array(4, 0), array(0, 0)))),
            new Polygon(array(array(array(1, 1), array(1, 3), array(3, 3), array(3, 1), array(1, 1)))),
        ));

        $multiPolygon2 = new MultiPolygon(array(
            array(array(array(0, 0), array(0, 4), array(4, 4), array(4, 0), array(0, 0))),
            array(array(array(1, 1), array(1, 3), array(3, 3), array(3, 1), array(1, 1))),
        ));

        $this->assertSame($multiPolygon1->getCoordinates(), $multiPolygon2->getCoordinates());
    }

    public function testSerialization(): void
    {
        $coordinates = array(
            array(array(array(0, 0), array(0, 4), array(4, 4), array(4, 0), array(0, 0))),
            array(array(array(1, 1), array(1, 3), array(3, 3), array(3, 1), array(1, 1))),
        );

        $multiPolygon = new MultiPolygon($coordinates);

        $expected = array(
            'type' => 'MultiPolygon',
            'coordinates' => $coordinates,
        );

        $this->assertSame('MultiPolygon', $multiPolygon->getType());
        $this->assertSame($coordinates, $multiPolygon->getCoordinates());
        $this->assertSame($expected, $multiPolygon->jsonSerialize());
    }

    /**
     * @dataProvider provideJsonDecodeAssocOptions
     * @group functional
     */
    public function testUnserialization($assoc): void
    {
        $json = <<<'JSON'
{
    "type": "MultiPolygon",
    "coordinates": [
        [ [ [0, 0], [0, 4], [4, 4], [4, 0], [0, 0] ] ],
        [ [ [1, 1], [1, 3], [3, 3], [3, 1], [1, 1] ] ]
    ]
}
JSON;

        $json = json_decode($json, $assoc);
        $multiPolygon = GeoJson::jsonUnserialize($json);

        $expectedCoordinates = array(
            array(array(array(0, 0), array(0, 4), array(4, 4), array(4, 0), array(0, 0))),
            array(array(array(1, 1), array(1, 3), array(3, 3), array(3, 1), array(1, 1))),
        );

        $this->assertInstanceOf(MultiPolygon::class, $multiPolygon);
        $this->assertSame('MultiPolygon', $multiPolygon->getType());
        $this->assertSame($expectedCoordinates, $multiPolygon->getCoordinates());
    }

    public function provideJsonDecodeAssocOptions(): array
    {
        return array(
            'assoc=true' => array(true),
            'assoc=false' => array(false),
        );
    }
}

<?php

namespace GeoJson\Tests\Feature;

use GeoJson\Feature\Feature;
use GeoJson\GeoJson;
use GeoJson\Tests\BaseGeoJsonTest;
use GeoJson\Geometry\Point;

class FeatureTest extends BaseGeoJsonTest
{
    public function createSubjectWithExtraArguments(array $extraArgs)
    {
        $class = new \ReflectionClass(Feature::class);

        return $class->newInstanceArgs(array_merge(array(null, [], null), $extraArgs));
    }

    public function testIsSubclassOfGeoJson(): void
    {
        $this->assertTrue(is_subclass_of(Feature::class, GeoJson::class));
    }

    public function testSerialization(): void
    {
        $geometry = $this->getMockGeometry();

        $geometry->expects($this->any())
            ->method('jsonSerialize')
            ->will($this->returnValue(array('geometry')));

        $properties = array('key' => 'value');
        $id = 'identifier';

        $feature = new Feature($geometry, $properties, $id);

        $expected = array(
            'type' => 'Feature',
            'geometry' => array('geometry'),
            'properties' => $properties,
            'id' => 'identifier',
        );

        $this->assertSame('Feature', $feature->getType());
        $this->assertSame($geometry, $feature->getGeometry());
        $this->assertSame($id, $feature->getId());
        $this->assertSame($properties, $feature->getProperties());
        $this->assertSame($expected, $feature->jsonSerialize());
    }

    public function testSerializationWithNullConstructorArguments(): void
    {
        $feature = new Feature();

        $expected = array(
            'type' => 'Feature',
            'geometry' => null,
            'properties' => new \stdClass(),
        );

        $this->assertEquals($expected, $feature->jsonSerialize());
    }

    public function testSerializationShouldConvertEmptyPropertiesArrayToObject(): void
    {
        $feature = new Feature(null, array());

        $expected = array(
            'type' => 'Feature',
            'geometry' => null,
            'properties' => new \stdClass(),
        );

        $this->assertEquals($expected, $feature->jsonSerialize());
    }

    /**
     * @dataProvider provideJsonDecodeAssocOptions
     * @group functional
     */
    public function testUnserialization($assoc): void
    {
        $json = <<<'JSON'
{
    "type": "Feature",
    "id": "test.feature.1",
    "properties": {
        "key": "value"
    },
    "geometry": {
        "type": "Point",
        "coordinates": [1, 1]
    }
}
JSON;

        $json = json_decode($json, $assoc);
        $feature = GeoJson::jsonUnserialize($json);

        $this->assertInstanceOf(Feature::class, $feature);
        $this->assertSame('Feature', $feature->getType());
        $this->assertSame('test.feature.1', $feature->getId());
        $this->assertSame(array('key' => 'value'), $feature->getProperties());

        $geometry = $feature->getGeometry();

        $this->assertInstanceOf(Point::class, $geometry);
        $this->assertSame('Point', $geometry->getType());
        $this->assertSame(array(1, 1), $geometry->getCoordinates());
    }

    public function provideJsonDecodeAssocOptions(): array
    {
        return array(
            'assoc=true' => array(true),
            'assoc=false' => array(false),
        );
    }
}

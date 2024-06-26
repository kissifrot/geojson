<?php

namespace GeoJson\Tests\Feature;

use GeoJson\Exception\UnserializationException;
use GeoJson\Feature\FeatureCollection;
use GeoJson\GeoJson;
use GeoJson\Tests\BaseGeoJsonTest;
use GeoJson\Feature\Feature;
use GeoJson\Geometry\Point;

class FeatureCollectionTest extends BaseGeoJsonTest
{
    public function createSubjectWithExtraArguments(array $extraArgs)
    {
        $class = new \ReflectionClass(FeatureCollection::class);

        return $class->newInstanceArgs(array_merge(array(array()), $extraArgs));
    }

    public function testIsSubclassOfGeoJson(): void
    {
        $this->assertTrue(is_subclass_of(FeatureCollection::class, GeoJson::class));
    }

    public function testConstructorShouldRequireArrayOfFeatureObjects(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('FeatureCollection may only contain Feature objects');
        new FeatureCollection(array(new \stdClass()));
    }

    public function testConstructorShouldReindexFeaturesArrayNumerically(): void
    {
        $feature1 = $this->getMockFeature();
        $feature2 = $this->getMockFeature();

        $features = array(
            'one' => $feature1,
            'two' => $feature2,
        );

        $collection = new FeatureCollection($features);

        $this->assertSame(array($feature1, $feature2), iterator_to_array($collection));
    }

    public function testIsTraversable(): void
    {
        $features = array(
            $this->getMockFeature(),
            $this->getMockFeature(),
        );

        $collection = new FeatureCollection($features);

        $this->assertInstanceOf('Traversable', $collection);
        $this->assertSame($features, iterator_to_array($collection));
    }

    public function testIsCountable(): void
    {
        $features = array(
            $this->getMockFeature(),
            $this->getMockFeature(),
        );

        $collection = new FeatureCollection($features);

        $this->assertInstanceOf('Countable', $collection);
        $this->assertCount(2, $collection);
    }

    public function testSerialization(): void
    {
        $features = array(
            $this->getMockFeature(),
            $this->getMockFeature(),
        );

        $features[0]->expects($this->any())
            ->method('jsonSerialize')
            ->will($this->returnValue(array('feature1')));

        $features[1]->expects($this->any())
            ->method('jsonSerialize')
            ->will($this->returnValue(array('feature2')));

        $collection = new FeatureCollection($features);

        $expected = array(
            'type' => 'FeatureCollection',
            'features' => array(array('feature1'), array('feature2')),
        );

        $this->assertSame('FeatureCollection', $collection->getType());
        $this->assertSame($features, $collection->getFeatures());
        $this->assertSame($expected, $collection->jsonSerialize());
    }

    /**
     * @dataProvider provideJsonDecodeAssocOptions
     * @group functional
     */
    public function testUnserialization($assoc): void
    {
        $json = <<<'JSON'
{
    "type": "FeatureCollection",
    "features": [
        {
            "type": "Feature",
            "id": "test.feature.1",
            "properties": [],
            "geometry": {
                "type": "Point",
                "coordinates": [1, 1]
            }
        }
    ]
}
JSON;

        $json = json_decode($json, $assoc);
        $collection = GeoJson::jsonUnserialize($json);

        $this->assertInstanceOf(FeatureCollection::class, $collection);
        $this->assertSame('FeatureCollection', $collection->getType());
        $this->assertCount(1, $collection);

        $features = iterator_to_array($collection);
        $feature = $features[0];

        $this->assertInstanceOf(Feature::class, $feature);
        $this->assertSame('Feature', $feature->getType());
        $this->assertSame('test.feature.1', $feature->getId());
        $this->assertEmpty($feature->getProperties());

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

    public function testUnserializationShouldRequireFeaturesProperty(): void
    {
        $this->expectException(UnserializationException::class);
        $this->expectExceptionMessage('FeatureCollection expected "features" property of type array, none given');
        GeoJson::jsonUnserialize(array('type' => 'FeatureCollection'));
    }

    public function testUnserializationShouldRequireFeaturesArray(): void
    {
        $this->expectException(UnserializationException::class);
        $this->expectExceptionMessage('FeatureCollection expected "features" property of type array');
        GeoJson::jsonUnserialize(array('type' => 'FeatureCollection', 'features' => null));
    }
}

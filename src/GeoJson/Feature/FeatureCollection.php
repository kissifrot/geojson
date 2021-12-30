<?php

namespace GeoJson\Feature;

use GeoJson\BoundingBox;
use GeoJson\CoordinateReferenceSystem\CoordinateReferenceSystem;
use GeoJson\GeoJson;

/**
 * Collection of Feature objects.
 *
 * @see http://www.geojson.org/geojson-spec.html#feature-collection-objects
 * @since 1.0
 */
class FeatureCollection extends GeoJson implements \Countable, \IteratorAggregate
{
    protected $type = 'FeatureCollection';

    /**
     * @var array
     */
    protected $features;

    /**
     * Constructor.
     *
     * @param Feature[] $features
     * @param CoordinateReferenceSystem|BoundingBox $arg,...
     */
    public function __construct(array $features, ...$arg)
    {
        foreach ($features as $feature) {
            if ( ! $feature instanceof Feature) {
                throw new \InvalidArgumentException('FeatureCollection may only contain Feature objects');
            }
        }

        $this->features = array_values($features);

        $this->setOptionalConstructorArgs($arg);
    }

    /**
     * @see http://php.net/manual/en/countable.count.php
     */
    public function count(): int
    {
        return \count($this->features);
    }

    /**
     * Return the Feature objects in this collection.
     *
     * @return Feature[]
     */
    public function getFeatures(): array
    {
        return $this->features;
    }

    /**
     * @see http://php.net/manual/en/iteratoraggregate.getiterator.php
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->features);
    }

    /**
     * @see http://php.net/manual/en/jsonserializable.jsonserialize.php
     */
    public function jsonSerialize(): array
    {
        return array_merge(
            parent::jsonSerialize(),
            array('features' => array_map(
                function(Feature $feature) { return $feature->jsonSerialize(); },
                $this->features
            ))
        );
    }
}

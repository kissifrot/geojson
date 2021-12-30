<?php

namespace GeoJson\Geometry;

use GeoJson\BoundingBox;
use GeoJson\CoordinateReferenceSystem\CoordinateReferenceSystem;

/**
 * Point geometry object.
 *
 * Coordinates consist of a single position.
 *
 * @see http://www.geojson.org/geojson-spec.html#point
 * @since 1.0
 */
class Point extends Geometry
{
    protected $type = 'Point';

    /**
     * Constructor.
     *
     * @param float[] $position
     * @param CoordinateReferenceSystem|BoundingBox $arg,...
     */
    public function __construct(array $position, ...$arg)
    {
        if (count($position) < 2) {
            throw new \InvalidArgumentException('Position requires at least two elements');
        }

        foreach ($position as $value) {
            if ( ! is_int($value) && ! is_float($value)) {
                throw new \InvalidArgumentException('Position elements must be integers or floats');
            }
        }

        $this->coordinates = $position;

        $this->setOptionalConstructorArgs($arg);
    }
}

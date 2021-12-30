<?php

namespace GeoJson\Geometry;

use GeoJson\BoundingBox;
use GeoJson\CoordinateReferenceSystem\CoordinateReferenceSystem;

/**
 * MultiPoint geometry object.
 *
 * Coordinates consist of an array of positions.
 *
 * @see http://www.geojson.org/geojson-spec.html#multipoint
 * @since 1.0
 */
class MultiPoint extends Geometry
{
    protected $type = 'MultiPoint';

    /**
     * Constructor.
     *
     * @param float[][]|Point[] $positions
     * @param CoordinateReferenceSystem|BoundingBox $arg,...
     */
    public function __construct(array $positions, ...$arg)
    {
        $this->coordinates = array_map(
            static function($point) {
                if ( ! $point instanceof Point) {
                    $point = new Point($point);
                }

                return $point->getCoordinates();
            },
            $positions
        );

        $this->setOptionalConstructorArgs($arg);
    }
}

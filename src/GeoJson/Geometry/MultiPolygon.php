<?php

namespace GeoJson\Geometry;

use GeoJson\BoundingBox;
use GeoJson\CoordinateReferenceSystem\CoordinateReferenceSystem;

/**
 * MultiPolygon geometry object.
 *
 * Coordinates consist of an array of Polygon coordinates.
 *
 * @see http://www.geojson.org/geojson-spec.html#multipolygon
 * @since 1.0
 */
class MultiPolygon extends Geometry
{
    protected $type = 'MultiPolygon';

    /**
     * Constructor.
     *
     * @param float[][][][]|Polygon[] $polygons
     * @param CoordinateReferenceSystem|BoundingBox $arg,...
     */
    public function __construct(array $polygons, ...$arg)
    {
        $this->coordinates = array_map(
            static function($polygon) {
                if ( ! $polygon instanceof Polygon) {
                    $polygon = new Polygon($polygon);
                }

                return $polygon->getCoordinates();
            },
            $polygons
        );

        $this->setOptionalConstructorArgs($arg);
    }
}

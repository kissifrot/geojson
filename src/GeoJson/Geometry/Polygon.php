<?php

namespace GeoJson\Geometry;

use GeoJson\BoundingBox;
use GeoJson\CoordinateReferenceSystem\CoordinateReferenceSystem;

/**
 * Polygon geometry object.
 *
 * Coordinates consist of an array of LinearRing coordinates.
 *
 * @see http://www.geojson.org/geojson-spec.html#polygon
 * @since 1.0
 */
class Polygon extends Geometry
{
    protected $type = 'Polygon';

    /**
     * Constructor.
     *
     * @param float[][][]|LinearRing[] $linearRings
     * @param CoordinateReferenceSystem|BoundingBox $arg,...
     */
    public function __construct(array $linearRings, ...$arg)
    {
        foreach ($linearRings as $linearRing) {
            if ( ! $linearRing instanceof LinearRing) {
                $linearRing = new LinearRing($linearRing);
            }
            $this->coordinates[] = $linearRing->getCoordinates();
        }

        $this->setOptionalConstructorArgs($arg);
    }
}

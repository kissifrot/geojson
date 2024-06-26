<?php

namespace GeoJson\Geometry;

use GeoJson\BoundingBox;
use GeoJson\CoordinateReferenceSystem\CoordinateReferenceSystem;

/**
 * MultiLineString geometry object.
 *
 * Coordinates consist of an array of LineString coordinates.
 *
 * @see http://www.geojson.org/geojson-spec.html#multilinestring
 * @since 1.0
 */
class MultiLineString extends Geometry
{
    protected $type = 'MultiLineString';

    /**
     * Constructor.
     *
     * @param float[][][]|LineString[] $lineStrings
     * @param CoordinateReferenceSystem|BoundingBox $arg,...
     */
    public function __construct(array $lineStrings, ...$arg)
    {
        $this->coordinates = array_map(
            static function($lineString) {
                if ( ! $lineString instanceof LineString) {
                    $lineString = new LineString($lineString);
                }

                return $lineString->getCoordinates();
            },
            $lineStrings
        );

        $this->setOptionalConstructorArgs($arg);
    }
}

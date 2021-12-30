<?php

namespace GeoJson\Tests\Geometry;

use PHPUnit\Framework\TestCase;
use GeoJson\Geometry\Geometry;
use GeoJson\GeoJson;

class GeometryTest extends TestCase
{
    public function testIsSubclassOfGeoJson(): void
    {
        $this->assertTrue(is_subclass_of(Geometry::class, GeoJson::class));
    }
}

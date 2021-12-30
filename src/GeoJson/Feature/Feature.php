<?php

namespace GeoJson\Feature;

use GeoJson\BoundingBox;
use GeoJson\CoordinateReferenceSystem\CoordinateReferenceSystem;
use GeoJson\GeoJson;
use GeoJson\Geometry\Geometry;

/**
 * Feature object.
 *
 * @see http://www.geojson.org/geojson-spec.html#feature-objects
 * @since 1.0
 */
class Feature extends GeoJson
{
    protected $type = 'Feature';

    /**
     * @var Geometry
     */
    protected $geometry;

    /**
     * Properties are a JSON object, which corresponds to an associative array.
     *
     * @var array
     */
    protected $properties;

    /**
     * @var mixed
     */
    protected $id;

    /**
     * Constructor.
     *
     * @param mixed $id
     * @param CoordinateReferenceSystem|BoundingBox $arg,...
     */
    public function __construct(Geometry $geometry = null, array $properties = array(), $id = null, ...$arg)
    {
        $this->geometry = $geometry;
        $this->properties = $properties;
        $this->id = $id;

        $this->setOptionalConstructorArgs($arg);
    }

    /**
     * Return the Geometry object for this Feature object.
     *
     * @return Geometry
     */
    public function getGeometry(): ?Geometry
    {
        return $this->geometry;
    }

    /**
     * Return the identifier for this Feature object.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Return the properties for this Feature object.
     *
     * @return array|null
     */
    public function getProperties(): ?array
    {
        return $this->properties;
    }

    /**
     * @see http://php.net/manual/en/jsonserializable.jsonserialize.php
     */
    public function jsonSerialize(): array
    {
        $json = parent::jsonSerialize();

        $json['geometry'] = isset($this->geometry) ? $this->geometry->jsonSerialize() : null;
        $json['properties'] = isset($this->properties) ? $this->properties : null;

        // Ensure empty associative arrays are encoded as JSON objects
        if ($json['properties'] === array()) {
            $json['properties'] = new \stdClass();
        }

        if (isset($this->id)) {
            $json['id'] = $this->id;
        }

        return $json;
    }
}

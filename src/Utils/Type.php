<?php

declare(strict_types=1);

namespace Atk4\GoogleAddress\Utils;

/**
 * Google address_component type.
 */
class Type
{
    /** @var string non address_component type supported by AddressLookup */
    public const LAT = 'lat';
    public const LNG = 'lng';

    /** @var string Address Type as per https://developers.google.com/maps/documentation/javascript/geocoding#GeocodingAddressTypes */
    public const STREET_NUMBER = 'street_number';
    public const ROUTE = 'route';
    public const COUNTRY = 'country';
    public const ADMIN_LEVEL_1 = 'administrative_area_level_1';
    public const ADMIN_LEVEL_2 = 'administrative_area_level_2';
    public const ADMIN_LEVEL_3 = 'administrative_area_level_3';
    public const ADMIN_LEVEL_4 = 'administrative_area_level_4';
    public const ADMIN_LEVEL_5 = 'administrative_area_level_5';
    public const COLLOQUIAL_AREA = 'colloquial_area';
    public const LOCALITY = 'locality';
    public const SUB_LOCALITY = 'sublocality';
    public const SUB_LOCALITY_1 = 'sublocality_level_1';
    public const SUB_LOCALITY_2 = 'sublocality_level_2';
    public const SUB_LOCALITY_3 = 'sublocality_level_3';
    public const SUB_LOCALITY_4 = 'sublocality_level_4';
    public const SUB_LOCALITY_5 = 'sublocality_level_5';
    public const NEIGHBORHOOD = 'neighborhood';
    public const PREMISE = 'premise';
    public const SUB_PREMISE = 'subpremise';
    public const PLUS_CODE = 'plus_code';
    public const POSTAL_CODE = 'postal_code';
    public const NATURAL_FEATURE = 'natural_feature';
    public const AIRPORT = 'airport';
    public const PARK = 'park';
    public const POINT_OF_INTEREST = 'point_of_interest';
    public const FLOOR = 'floor';
    public const ESTABLISHMENT = 'establishment';
    public const LANDMARK = 'landmark';
    public const PARKING = 'parking';
    public const POST_BOX = 'post_box';
    public const POSTAL_TOWN = 'postal_town';
    public const ROOM = 'room';
    public const STREET_ADDRESS = 'street_address';
    public const BUS_STATION = 'bus_station';
    public const TRAIN_STATION = 'train_station';
    public const TRANSIT_STATION = 'transit_station';
}

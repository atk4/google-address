<?php

declare(strict_types=1);
namespace Atk4\GoogleAddress\Model;

use Atk4\Data\Model;
use Atk4\GoogleAddress\AddressLookup;

class Address extends Model
{
    public $table = 'your_address_table_name_here';
    public $apiKey = 'YOUR_GOOGLE_API_KEY_HERE';

    protected function init(): void
    {
        parent::init();

        $this->addField('map_search', ['never_save' => true, 'ui' => ['form' => [new AddressLookup(['apiKey' => $this->apiKey])]]]);

        $this->addField('street_number');
        $this->addField('route');
        $this->addField('locality');
        $this->addField('sub_locality_level_1');
        $this->addField('postal_town');
        $this->addField('administrative_area_level_2');
        $this->addField('administrative_area_level_1');
        $this->addField('country');
        $this->addField('postal_code');
        $this->addField('lat');
        $this->addField('lng');

        $this->addHook('beforeSave', function($m) {
            $m->getElement('map_search')->never_persist = true;
        });
    }
}
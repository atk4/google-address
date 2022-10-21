<?php

declare(strict_types=1);

namespace Atk4\GoogleAddress\Model;

use Atk4\Data\Model;
use Atk4\GoogleAddress\Form\Control\AddressLookup;
use Atk4\GoogleAddress\Utils\Type;

/**
 * Model sample.
 */
class Address extends Model
{
    public $table = 'your_address_table_name_here';

    protected function init(): void
    {
        parent::init();

        $this->addField('map_search', [
            'neverSave' => true,
            'neverPersist' => true,
            'ui' => ['editable' => true, 'visible' => false, 'form' => [AddressLookup::class]],
        ]);

        $this->addField(Type::STREET_NUMBER);
        $this->addField(Type::ROUTE);
        $this->addField(Type::LOCALITY);
        $this->addField(Type::SUB_LOCALITY_1);
        $this->addField(Type::POSTAL_TOWN);
        $this->addField(Type::ADMIN_LEVEL_1);
        $this->addField(Type::ADMIN_LEVEL_2);
        $this->addField(Type::COUNTRY);
        $this->addField(Type::POSTAL_CODE);
        $this->addField(Type::LAT);
        $this->addField(Type::LNG);
    }
}

<?php
declare(strict_types=1);

namespace PrestaShop\Module\APIResources\ApiPlatform\Resources\Contact;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use PrestaShopBundle\ApiPlatform\Metadata\PaginatedList;

#[ApiResource(
    operations: [
        new PaginatedList(
            uriTemplate: '/contacts',
            scopes: [
                'contact_read',
            ],
            gridDataFactory: 'prestashop.core.grid.data_provider.contacts',
            ApiResourceMapping: self::RESOURCE_MAPPING,
            filtersMapping: self::FILTERS_MAPPING,
        ),
    ],
)]
class ContactList
{
    #[ApiProperty(identifier: true)]
    public int $contactId;

    public $name;

    public $email;

    public $description;

    public $customerService;

     public const RESOURCE_MAPPING = [
        '[id_contact]' => '[contactId]',
        '[customer_service]' => '[customerService]',
    ];

     public const FILTERS_MAPPING = [
        '[contactId]' => '[id_contact]',
    ];
}

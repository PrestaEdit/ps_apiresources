<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

declare(strict_types=1);

namespace PrestaShop\Module\APIResources\ApiPlatform\Resources\BusinessEntity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Command\AddBusinessEntityCommand;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception\BusinessEntityBillingAddressConstraintException;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception\BusinessEntityConstraintException;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception\UnableToCreateBusinessEntityAddress;
use PrestaShopBundle\ApiPlatform\Metadata\CQRSCreate;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new CQRSCreate(
            uriTemplate: '/business-entities',
            validationContext: ['groups' => ['Default', 'Create']],
            CQRSCommand: AddBusinessEntityCommand::class,
            scopes: [
                'business_entity_write',
            ],
            experimentalOperation: true,
        ),
    ],
    exceptionToStatus: [
        BusinessEntityConstraintException::class => Response::HTTP_UNPROCESSABLE_ENTITY,
        BusinessEntityBillingAddressConstraintException::class => Response::HTTP_UNPROCESSABLE_ENTITY,
        UnableToCreateBusinessEntityAddress::class => Response::HTTP_UNPROCESSABLE_ENTITY,
    ],
)]
class BusinessEntity
{
    #[ApiProperty(identifier: true)]
    public int $businessEntityId;

    #[Assert\NotBlank(groups: ['Create'])]
    #[Assert\Length(min: 1, max: 255)]
    public string $name;

    #[Assert\NotBlank(groups: ['Create'])]
    #[Assert\Length(min: 1, max: 255)]
    public string $legalName;

    #[Assert\Length(max: 255)]
    public ?string $externalRef = null;

    #[Assert\NotNull(groups: ['Create'])]
    public bool $deliveryAuthorized;

    /**
     * BusinessEntityStatus enum name — one of PENDING, ENABLED, DISABLED.
     */
    #[Assert\NotBlank(groups: ['Create'])]
    #[Assert\Choice(choices: ['PENDING', 'ENABLED', 'DISABLED'])]
    public string $status;

    #[Assert\NotNull(groups: ['Create'])]
    #[Assert\Positive]
    public int $shopId;

    #[Assert\NotNull(groups: ['Create'])]
    #[Assert\Positive]
    public int $customerGroupId;

    #[Assert\NotNull(groups: ['Create'])]
    public bool $billingAddressAsShippingAddress;

    #[ApiProperty(openapiContext: [
        'type' => 'array',
        'description' => 'Billing addresses. At least one is required, exactly one must be default.',
        'items' => [
            'type' => 'object',
            'properties' => [
                'alias' => ['type' => 'string'],
                'address1' => ['type' => 'string'],
                'address2' => ['type' => 'string', 'nullable' => true],
                'city' => ['type' => 'string'],
                'postcode' => ['type' => 'string'],
                'countryId' => ['type' => 'integer'],
                'stateId' => ['type' => 'integer', 'nullable' => true],
                'phone' => ['type' => 'string', 'nullable' => true],
                'phoneMobile' => ['type' => 'string', 'nullable' => true],
                'default' => ['type' => 'boolean'],
            ],
        ],
    ])]
    #[Assert\NotBlank(groups: ['Create'])]
    public array $billingAddresses;

    #[ApiProperty(openapiContext: [
        'type' => 'array',
        'description' => 'Shipping addresses. Required unless billingAddressAsShippingAddress=true. Exactly one must be default when provided.',
        'items' => [
            'type' => 'object',
            'properties' => [
                'alias' => ['type' => 'string'],
                'address1' => ['type' => 'string'],
                'address2' => ['type' => 'string', 'nullable' => true],
                'city' => ['type' => 'string'],
                'postcode' => ['type' => 'string'],
                'countryId' => ['type' => 'integer'],
                'stateId' => ['type' => 'integer', 'nullable' => true],
                'phone' => ['type' => 'string', 'nullable' => true],
                'phoneMobile' => ['type' => 'string', 'nullable' => true],
                'default' => ['type' => 'boolean'],
            ],
        ],
    ])]
    public array $shippingAddresses = [];
}

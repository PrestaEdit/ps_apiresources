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

namespace PsApiResourcesTest\Integration\ApiPlatform;

use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Command\AddBusinessEntityCommand;
use Symfony\Component\HttpFoundation\Response;
use Tests\Resources\DatabaseDump;

class BusinessEntityEndpointTest extends ApiTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        if (!class_exists(AddBusinessEntityCommand::class)) {
            return;
        }
        self::createApiClient(['business_entity_write']);
    }

    protected function setUp(): void
    {
        parent::setUp();
        if (!class_exists(AddBusinessEntityCommand::class)) {
            $this->markTestSkipped('AddBusinessEntityCommand only exists on PrestaShop develop.');
        }
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        if (!class_exists(AddBusinessEntityCommand::class)) {
            return;
        }
        DatabaseDump::restoreTables(['business_entity', 'business_entity_address', 'address']);
    }

    public static function getProtectedEndpoints(): iterable
    {
        if (class_exists(AddBusinessEntityCommand::class)) {
            yield 'create endpoint' => [
                'POST',
                '/business-entities',
            ];
        }
    }

    public function testAddBusinessEntityWithBillingOnly(): int
    {
        $created = $this->createItem('/business-entities', [
            'name' => 'Acme Corp',
            'legalName' => 'Acme S.A.',
            'externalRef' => 'ACME-001',
            'deliveryAuthorized' => true,
            'status' => 'active',
            'shopId' => 1,
            'customerGroupId' => 3,
            'billingAddressAsShippingAddress' => true,
            'billingAddresses' => [
                [
                    'alias' => 'HQ',
                    'address1' => '1 rue de la Paix',
                    'address2' => null,
                    'city' => 'Paris',
                    'postcode' => '75002',
                    'countryId' => 8,
                    'stateId' => null,
                    'phone' => null,
                    'phoneMobile' => null,
                    'default' => true,
                ],
            ],
        ], ['business_entity_write']);

        $this->assertArrayHasKey('businessEntityId', $created);
        $this->assertIsInt($created['businessEntityId']);
        $this->assertGreaterThan(0, $created['businessEntityId']);

        return $created['businessEntityId'];
    }

    public function testCreateInvalidBusinessEntity(): void
    {
        $response = $this->createItem(
            '/business-entities',
            [
                // missing name, legalName, addresses...
                'deliveryAuthorized' => true,
                'status' => 'active',
                'shopId' => 1,
                'customerGroupId' => 3,
                'billingAddressAsShippingAddress' => true,
            ],
            ['business_entity_write'],
            Response::HTTP_UNPROCESSABLE_ENTITY
        );

        $this->assertIsArray($response);
    }
}

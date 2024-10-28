<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Tests\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\AdminBundle\DependencyInjection\Configuration;

final class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /** @test */
    public function it_turns_on_hub_notifications_by_default(): void
    {
        $this->assertProcessedConfigurationEquals(
            [[]],
            ['notifications' => ['hub_enabled' => true]],
            'notifications.hub_enabled',
        );
    }

    /** @test */
    public function its_hub_notifications_can_be_turned_off(): void
    {
        $this->assertProcessedConfigurationEquals(
            [['notifications' => ['hub_enabled' => false]]],
            ['notifications' => ['hub_enabled' => false]],
            'notifications.hub_enabled',
        );
    }

    protected function getConfiguration(): Configuration
    {
        return new Configuration();
    }
}

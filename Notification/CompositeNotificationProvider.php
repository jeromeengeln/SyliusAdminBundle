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

namespace Sylius\Bundle\AdminBundle\Notification;

use Traversable;
use Webmozart\Assert\Assert;

final class CompositeNotificationProvider implements NotificationProviderInterface
{
    /** @var array<NotificationProviderInterface> */
    private array $notificationProviders;

    /** @param iterable<NotificationProviderInterface> $notificationProviders */
    public function __construct(iterable $notificationProviders)
    {
        Assert::allIsInstanceOf(
            $notificationProviders,
            NotificationProviderInterface::class,
            sprintf('All notification providers should implement the "%s" interface.', NotificationProviderInterface::class),
        );
        $this->notificationProviders = $notificationProviders instanceof Traversable ? iterator_to_array($notificationProviders) : $notificationProviders;
    }

    public function getNotifications(): array
    {
        $notifications = [];
        foreach ($this->notificationProviders as $notificationProvider) {
            $notifications = array_merge($notifications, $notificationProvider->getNotifications());
        }

        return $notifications;
    }
}

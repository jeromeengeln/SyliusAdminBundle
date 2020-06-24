<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Controller;

use Sylius\Bundle\AdminBundle\Provider\StatisticsDataProviderInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Dashboard\SalesDataProviderInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

final class DashboardController
{
    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    /** @var EngineInterface */
    private $templatingEngine;

    /** @var RouterInterface */
    private $router;

    /** @var SalesDataProviderInterface|null */
    private $salesDataProvider;

    /** @var StatisticsDataProviderInterface */
    private $statisticsDataProvider;

    public function __construct(
        ChannelRepositoryInterface $channelRepository,
        EngineInterface $templatingEngine,
        RouterInterface $router,
        ?SalesDataProviderInterface $salesDataProvider = null,
        ?StatisticsDataProviderInterface $statisticsDataProvider = null
    ) {
        $this->channelRepository = $channelRepository;
        $this->templatingEngine = $templatingEngine;
        $this->router = $router;
        $this->salesDataProvider = $salesDataProvider;
        $this->statisticsDataProvider = $statisticsDataProvider;
    }

    public function indexAction(Request $request): Response
    {
        /** @var ChannelInterface|null $channel */
        $channel = $this->findChannelByCodeOrFindFirst($request->query->get('channel'));

        if (null === $channel) {
            return new RedirectResponse($this->router->generate('sylius_admin_channel_create'));
        }

        return $this->templatingEngine->renderResponse('@SyliusAdmin/Dashboard/index.html.twig', [
            'channel' => $channel,
        ]);
    }

    public function getRawData(Request $request): Response
    {
        /** @var ChannelInterface|null $channel */
        $channel = $this->findChannelByCodeOrFindFirst($request->query->get('channelCode'));

        if (null === $channel) {
            return new RedirectResponse($this->router->generate('sylius_admin_channel_create'));
        }

        return new JsonResponse(
            $this->statisticsDataProvider->getRawData(
                $channel,
                (new \DateTime($request->query->get('startDate'))),
                (new \DateTime($request->query->get('endDate'))),
                $request->query->get('interval')
            )
        );
    }

    private function findChannelByCodeOrFindFirst(?string $channelCode): ?ChannelInterface
    {
        if (null !== $channelCode) {
            return $this->channelRepository->findOneByCode($channelCode);
        }

        return $this->channelRepository->findOneBy([]);
    }
}

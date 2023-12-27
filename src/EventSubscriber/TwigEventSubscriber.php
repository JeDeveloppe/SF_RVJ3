<?php

namespace App\EventSubscriber;

use App\Repository\SiteSettingRepository;
use Twig\Environment;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TwigEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Environment $twig,
        private SiteSettingRepository $siteSettingRepository
    )
    {
    }

    public function onControllerEvent(ControllerEvent $event): void
    {
        $siteSetting = $this->siteSettingRepository->findOneBy([]);
        $this->twig->addGlobal('marquee', $siteSetting->getMarquee() );
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ControllerEvent::class => 'onControllerEvent',
        ];
    }
}

<?php

namespace App\EventSubscriber;

use Twig\Environment;
use App\Repository\SiteSettingRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class TwigEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Environment $twig,
        private SiteSettingRepository $siteSettingRepository,
    )
    {
    }

    public function onControllerEvent(ControllerEvent $event): void
    {
        $siteSetting = $this->siteSettingRepository->findOneBy([]);

        $this->twig->addGlobal('marquee', $siteSetting->getMarquee());
        $this->twig->addGlobal('fairDay', $siteSetting->getFairday());
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ControllerEvent::class => 'onControllerEvent',
        ];
    }

    public function maintenanceRedirection(RequestEvent $event) {

        $event->setResponse(
            new Response($this->twig->render('site/maintenance/index.html.twig'), Response::HTTP_SERVICE_UNAVAILABLE)
        );
        $event->stopPropagation();
        
    }
}

<?php

namespace App\EventSubscriber;

use Twig\Environment;
use App\Repository\SiteSettingRepository;
use App\Service\UtilitiesService;
use DateTimeImmutable;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class TwigEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Environment $twig,
        private SiteSettingRepository $siteSettingRepository,
        private RequestStack $requestStack,
        private Security $security,
        private UtilitiesService $utilitiesService
    )
    {
    }

    public function onControllerEvent(ControllerEvent $event): void
    {

        $siteSetting = $this->siteSettingRepository->findOneBy([]);
        $session = $this->requestStack->getSession();
        $paniers = $session->get('paniers');
        $tokenSession = $session->get('tokenSession');

        if(!$tokenSession){

            $pre_token = $this->utilitiesService->generateRandomString(200);
            $now = new DateTimeImmutable('now');
            $milli = (int) $now->format('Uv');
            $token = $pre_token.'_'.$milli;
            $session->set('tokenSession', $token);
        }

        if(!$paniers){
            $paniers['occasions'] = [];
            $paniers['items'] = [];
            $paniers['boites'] = [];
            $session->set('paniers', $paniers);
        }

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

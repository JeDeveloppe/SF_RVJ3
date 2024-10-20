<?php

namespace App\Service;

use Symfony\Component\Routing\RouterInterface;

class MemberService
{
    public function __construct(
        private RouterInterface $routerInterface
    )
    {
        
    }
    public function memberThemes(){

        $themes[] = [
            'title' => 'Mes commandes',
            'imgName' => 'commandes',
            'link' => $this->routerInterface->generate('member_historique')
        ];
        $themes[] = [
            'title' => 'Mes adresses',
            'imgName' => 'adresses',
            'link' => $this->routerInterface->generate('member_adresses')           
        ];
        $themes[] = [
            'title' => 'Mes paramètres',
            'imgName' => 'parametres',
            'link' => $this->routerInterface->generate('member_compte')           
        ];

        return $themes;
    }
}
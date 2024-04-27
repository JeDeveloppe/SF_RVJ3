<?php

namespace App\Service;

use App\Entity\Benefit;
use App\Repository\BenefitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class BenefitService
{
    public function __construct(
        private EntityManagerInterface $em,
        private BenefitRepository $benefitRepository
        ){
    }

    public function addBenefits(SymfonyStyle $io){

        $benefits = [];

        $benefits[] = [
            'title' => "L’animation de jeux vintage",
            'priceHt' => "60€ / heure ",
            'priceInfo' => "(hors frais de déplacement : 0,40€/km)",
            'description' => "Replongez dans vos souvenirs d’enfance avec nos malles de jeux vintage !
                                Retrouvez les grands classiques complétés par nos soins : Qui est-ce ?, Piège !, Dix de chute, le cochon qui rit….
                                Vous ne vous rappelez plus très bien des règles ? Un membre de l’association sera là pour vous les réexpliquer !
                                Cette animation est une belle occasion de partage et de transmission. Parce que ces jeux ont une histoire, parents et grands-parents auront plaisir à les faire découvrir à leurs enfants et petits-enfants."
        ];

        $benefits[] = [
            'title' => "L’animation d’un atelier de sensibilisation",
            'priceHt' => "60€ / heure ",
            'priceInfo' => "(hors frais de déplacement : 0,40€/km)",
            'description' => "Vous organisez un événement autour du développement durable ?
                            L’association propose de tenir un stand pour sensibiliser le public aux questions liées à la sobriété et à la réduction des déchets.
                            Ces thèmes seront abordés à travers le jeu, de sa fabrication à son traitement en tant que déchet.
                            Un atelier manuel sera également proposé.
                            Enfants comme adultes, pourront repartir avec un porte-cartes qu’ils auront fabriqué à partir d’éléments de jeux de récupération.
                            Activité accessible dès 6 ans, le tout dans une ambiance ludique !"
        ];

        $benefits[] = [
            'title' => "Inventaire des stocks de jeux (simple)",
            'priceHt' => "15€ pour 15 jeux",
            'priceInfo' => "(hors frais de déplacement : 0,40€/km)",
            'description' => "Vous faites partie d’une structure et vous avez une armoire remplie de jeux en souffrance ?
                                Pas de panique ! Nous sommes là ! Nous venons sur place et nous faisons l’inventaire de vos jeux. Les jeux sont triés en 3 catégories : 
                                - complets.
                                - incomplets mais jouables (pièces ou règles du jeu manquantes).
                                - incomplets et pas jouables.
                                Si besoin, les boîtes de jeux sont réparées et les petites pièces sont mises en sachet pour limiter les risques de perte."
        ];

        $benefits[] = [
            'title' => "Inventaire et complétion",
            'priceHt' => "20€ pour 15 jeux",
            'priceInfo' => "(hors frais de déplacement : 0,40€/km)",
            'description' => "Inventaire simple + les jeux sont complétés par l’association lorsque cela est possible.
                                Cela concerne les jeux pour lesquels il manque moins de 30 % des pièces et pour lesquels l’association possède des pièces détachées. L’association se charge également de rechercher les règles du jeu manquantes et de vous les envoyer par mail."
        ];

        foreach($benefits as $prestation){

            $benefit = $this->benefitRepository->findOneBy(['title' => $prestation['title']]);

            if(!$benefit){
                $benefit = new Benefit();
            }

            $benefit
                ->setTitle($prestation['title'])
                ->setPriceHt($prestation['priceHt'])
                ->setPriceInfo($prestation['priceInfo'])
                ->setDescription($prestation['description'])
            ;

            $this->em->persist($benefit);

        }
        $this->em->flush();
        $io->success('Créations des prestations terminée');
    }
}
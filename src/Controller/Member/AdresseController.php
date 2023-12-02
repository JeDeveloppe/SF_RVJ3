<?php

namespace App\Controller\Member;

use App\Entity\Address;
use App\Form\AddressType;
use App\Repository\AddressRepository;
use App\Repository\PanierRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class AdresseController extends AbstractController
{
    public function __construct(
        private Security $security,
        private PanierRepository $panierRepository,
        private AddressRepository $addressRepository
    )
    { 
    }


    #[Route('/membre/adresses/new/', name: 'adresse_new')]
    public function new(
        Request $request,
        Security $security,
        ): Response
    {

        $form = $this->createForm(AddressType::class);
        $form->handleRequest($request);

        // if($form->isSubmitted() && $form->isValid()) {
        //     dd($form);
        //     $this->addressRepository->add($adresse);
        //     $this->addFlash('success', 'Adresse créée !');
        //     return $this->redirectToRoute('member_adresses', [], Response::HTTP_SEE_OTHER);
        // }

        return $this->render('member/adresse/new.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/membre/adresses/{id}', name: 'adresse_show')]
    public function show(Address $adresse): Response
    {
        return $this->render('adresse/show.html.twig', [
            'adresse' => $adresse,
        ]);
    }

    #[Route('/membre/adresses/{id}/edit', name: 'adresse_edit')]
    public function edit(
        Request $request,
        Address $adresse,
        Security $security,
        ): Response
    {
        $form = $this->createForm(AddressType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addressRepository->add($adresse);
            return $this->redirectToRoute('member_adresses', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('member/adresse/edit.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/membre/adresses/{id}/delete', name: 'adresse_delete')]
    public function delete(Request $request, Address $adresse): Response
    {
        if ($this->isCsrfTokenValid('delete'.$adresse->getId(), $request->request->get('_token'))) {
            $this->addressRepository->remove($adresse);
        }

        return $this->redirectToRoute('member_adresses', [], Response::HTTP_SEE_OTHER);
    }
}

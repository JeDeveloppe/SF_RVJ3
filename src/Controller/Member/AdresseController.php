<?php

namespace App\Controller\Member;

use App\Entity\Address;
use App\Form\Member\AdresseType;
use App\Repository\AddressRepository;
use App\Repository\PanierRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/membre/adresses")
 */
class AdresseController extends AbstractController
{
    public function __construct(
        private Security $security,
        private PanierRepository $panierRepository,
        private AddressRepository $addressRepository
    )
    { 
    }


    #[Route('/new/{slug}', name: 'adresse_new')]
    public function new(
        $slug,
        Request $request,
        Security $security,
        ): Response
    {
        $user = $security->getUser();
 
        $department = $user->getDepartment();

        $adresse = new Address();

        $adresse->setUser($user);

        $array = [];

        if($slug == "facturation"){
            $adresse->setIsFacturation(true);
            $array['titre'] = "facturation";
        }else if($slug == "livraison"){
            $adresse->setIsFacturation(false);
            $array['titre'] = "livraison";

        }else{
            $this->addFlash('danger', 'Url inconnue !');
            return $this->redirectToRoute('member_adresses', [], Response::HTTP_SEE_OTHER);
        }

        $form = $this->createForm(AdresseType::class, $adresse, ['department' => $department]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addressRepository->add($adresse);
            $this->addFlash('success', 'Adresse créée !');
            return $this->redirectToRoute('member_adresses', [], Response::HTTP_SEE_OTHER);
        }


        return $this->renderForm('member/adresse/new.html.twig', [
            'adresse' => $adresse,
            'form' => $form,
            'array' => $array,
        ]);
    }

    #[Route('/{id}', name: 'adresse_show')]
    public function show(Address $adresse): Response
    {
        return $this->render('adresse/show.html.twig', [
            'adresse' => $adresse,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_adresse_edit", methods={"GET", "POST"})
     */
    #[Route('/{id}/edit', name: 'adresse_edit')]
    public function edit(
        Request $request,
        Address $adresse,
        Security $security,
        ): Response
    {
        $user = $security->getUser();
 
        $department = $user->getDepartment();

        $form = $this->createForm(AdresseType::class, $adresse, ['department' => $department]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addressRepository->add($adresse);
            return $this->redirectToRoute('member_adresses', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('member/adresse/edit.html.twig', [
            'adresse' => $adresse,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'adresse_delete')]
    public function delete(Request $request, Address $adresse): Response
    {
        if ($this->isCsrfTokenValid('delete'.$adresse->getId(), $request->request->get('_token'))) {
            $this->addressRepository->remove($adresse);
        }

        return $this->redirectToRoute('member_adresses', [], Response::HTTP_SEE_OTHER);
    }
}

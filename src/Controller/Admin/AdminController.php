<?php

namespace App\Controller\Admin;

use App\Repository\DocumentRepository;
use App\Repository\DocumentStatusRepository;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    public function __construct(
        private DocumentRepository $documentRepository,
        private DocumentStatusRepository $documentStatusRepository,
        private EntityManagerInterface $em,
        private EmailService $emailService
    )
    {
    }

    #[Route('/admin/change-status-of-document/{document}/{status}', name: 'app_admin_change_status_document')]
    public function changeStatusDocument($document,$status): Response
    {
        $document = $this->documentRepository->findOneBy(['token' => $document]);
        $status = $this->documentStatusRepository->findOneby(['action' => $status]);

        if(!$document || !$status){

            $this->addFlash('warning', 'Document inconnu !');
            return $this->redirectToRoute('admin_traited_daily');

        }else{

            //TODO envoi email
            //$this->emailService->sendEmailWhenDocumentIsChangingStatus($document,$status);

            $document->setDocumentStatus($status);
            $this->em->persist($document);
            $this->em->flush();

            $this->addFlash('success', 'Status mis à jour !');
            return $this->redirectToRoute('admin_traited_daily');

        }
    }
}

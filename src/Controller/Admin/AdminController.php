<?php

namespace App\Controller\Admin;

use App\Repository\DocumentRepository;
use App\Repository\DocumentStatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    public function __construct(
        private DocumentRepository $documentRepository,
        private DocumentStatusRepository $documentStatusRepository,
        private EntityManagerInterface $em
    )
    {
    }

    #[Route('/admin/change-status-of-document/{document}/{status}', name: 'app_admin_change_status_document')]
    public function changeStatusDocument($document,$status): Response
    {
        $document = $this->documentRepository->find($document);
        $status = $this->documentStatusRepository->find($status);

        if(!$document || !$status){

            return $this->redirectToRoute('admin_traited_daily');

        }else{

            $document->setDocumentStatus($status);
            $this->em->persist($document);
            $this->em->flush();
            return $this->redirectToRoute('admin_traited_daily');

        }
    }
}

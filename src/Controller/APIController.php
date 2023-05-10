<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Entity\Abonnement;
use App\Entity\Contrat;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

use Doctrine\ORM\EntityManagerInterface;

#[Route('/api')]
class APIController extends AbstractController
{

    #[Route('/abonnement', name: 'abonnement', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {        
        $entityManager = $this->getDoctrine()->getManager();
        $abonnements = $entityManager->getRepository(Abonnement::class)->findAll();

        $responseArray = array();
        foreach ($abonnements as $abonnement) {
            $responseArray[] = array(
                'id' => $abonnement->getId(),
                'typeAb' => $abonnement->getTypeAb(),
                'prixAb' => $abonnement->getPrixAb(),
                'modePaiementAb' => $abonnement->getModePaiementAb()
            );
        }

        $responseData = json_encode($responseArray);
        $response = new Response($responseData);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    #[Route('/abonnement/{id}', name: 'abonnement_delete', methods: ['DELETE'])]
    public function deleteAbonnement(int $id): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $abonnement = $entityManager->getRepository(Abonnement::class)->find($id);

        if (!$abonnement) {
            throw $this->createNotFoundException('The abonnement does not exist');
        }

        $entityManager->remove($abonnement);
        $entityManager->flush();

        $response = new JsonResponse(['status' => 'deleted'], Response::HTTP_OK);
        return $response;
    }

    #[Route('/abonnement/{id}', name: 'abonnement_edit', methods: ['PUT'])]
    public function editEvenement(Request $request, $id): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $abonnement = $entityManager->getRepository(Abonnement::class)->find($id);

        if (!$abonnement) {
            return new JsonResponse(['status' => 'Faild']);;
        }

        $abonnement->setTypeAb($request->request->get('typeAb'));
        $abonnement->setPrixAb($request->request->get('prixAb'));
        $abonnement->setModePaiementAb($request->request->get('modePaiementAb'));
        
        $entityManager->persist($abonnement);
        $entityManager->flush();

        $response = new JsonResponse(['status' => 'edited'], Response::HTTP_OK);
        return $response;
    }
    
    #[Route('/abonnement/add', name: 'abonnement_add', methods: ['GET', 'POST'])]
    public function addAbonnement(Request $request): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $abonnement = new Abonnement();
        
        $abonnement->setTypeAb($request->request->get('typeAb'));
        $abonnement->setPrixAb($request->request->get('prixAb'));
        $abonnement->setModePaiementAb($request->request->get('modePaiementAb'));
        
        $entityManager->persist($abonnement);
        $entityManager->flush();
            
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'magicbook.pi@gmail.com';
        $mail->Password = 'wrqfzvitjcovvfqd';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('magicbook.pi@gmail.com', 'Autoxpress');
        $mail->addAddress('nahdii13@gmail.com', 'You');
        $mail->Subject = 'Abonnement ajouter ';
        $mail->Body = 'Votre Abonnement a été ajouter avec un succès et nous vous en remercions. ';
        if ($mail->send()) {
        echo 'cbon ';
        } else {
        echo "echec";
        }

        $response = new JsonResponse(['status' => 'added'], Response::HTTP_CREATED);
        return $response;
    }

    /*-------------------------------------------------------------------------*/

    #[Route('/contrat', name: 'contrats', methods: ['GET'])]
    public function indexContrat(EntityManagerInterface $entityManager): Response
    {        
        $entityManager = $this->getDoctrine()->getManager();
        $contrats = $entityManager->getRepository(Contrat::class)->findAll();

        $responseArray = array();
        foreach ($contrats as $contrat) {
            $responseArray[] = array(
                'id_contrat' => $contrat->getId(),
                'id_conducteur' => $contrat->getIdConducteur(),
                'id_admin' => $contrat->getIdAdmin(),
                'date_debut' => $contrat->getDateDebut(),
                'date_fin' => $contrat->getDateFin(),
                'prix' => $contrat->getPrix(),
                'statut' => $contrat->getStatut(),
                'qr_code' => $contrat->getQrCode()
            );
        }

        $responseData = json_encode($responseArray);
        $response = new Response($responseData);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    #[Route('/contrat/{id}', name: 'contrat_delete', methods: ['DELETE'])]
    public function deleteContrat(int $id): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $contrats = $entityManager->getRepository(Contrat::class)->find($id);

        if (!$contrats) {
            throw $this->createNotFoundException('The contrats does not exist');
        }

        $entityManager->remove($contrats);
        $entityManager->flush();

        $response = new JsonResponse(['status' => 'deleted'], Response::HTTP_OK);
        return $response;
    }

    #[Route('/contrat/{id}', name: 'contrat_edit', methods: ['PUT'])]
    public function editContrat(Request $request, $id): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $contrat = $entityManager->getRepository(Contrat::class)->find($id);

        if (!$contrat) {
            return new JsonResponse(['status' => 'Faild']);;
        }

        $contrat->setIdConducteur($request->request->get('id_contrat'));
        $contrat->setIdAdmin($request->request->get('id_admin'));
        $contrat->setDateDebut(new \DateTime($request->request->get('date_debut')));
        $contrat->setDateFin(new \DateTime($request->request->get('date_fin')));
        $contrat->setPrix($request->request->get('prix'));
        $contrat->setStatut($request->request->get('statut'));
        $contrat->setQrCode($request->request->get('qr_code'));
        
        $entityManager->persist($contrat);
        $entityManager->flush();

        $response = new JsonResponse(['status' => 'edited'], Response::HTTP_OK);
        return $response;
    }
    
    #[Route('/contrat/add', name: 'contrat_add', methods: ['GET', 'POST'])]
    public function addContrat(Request $request): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $contrat = new Contrat();
        
        $contrat->setIdConducteur($request->request->get('id_contrat'));
        $contrat->setIdAdmin($request->request->get('id_admin'));
        $contrat->setDateDebut(new \DateTime($request->request->get('date_debut')));
        $contrat->setDateFin(new \DateTime($request->request->get('date_fin')));
        $contrat->setPrix($request->request->get('prix'));
        $contrat->setStatut($request->request->get('statut'));
        $contrat->setQrCode($request->request->get('qr_code'));
        
        $entityManager->persist($contrat);
        $entityManager->flush();

        $response = new JsonResponse(['status' => 'added'], Response::HTTP_CREATED);
        return $response;
    }
}

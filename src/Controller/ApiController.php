<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Repository\PersonneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    #[Route('/api/personne', name: 'api_liste',methods:["GET"])]
    public function index(PersonneRepository $repo):Response
    {
        $personnes = $repo->findAll();
        $data = [];
        
        foreach ($personnes as $personne) {
           
           $data [] = $personne->toArray();
        }
        return $this->json($data);
    }    

    #[Route('/api/personne', name: 'api_ajouter',methods:["POST"])]
    public function ajouter(Request $request, EntityManagerInterface $em):Response
    {
        // objet PHP = un objet JS (body) 
        $objet = json_decode($request ->getContent());

        if ( empty($objet->nom) || empty($objet->prenom))
        {
            throw new NotFoundHttpException('le nom ou le prénom est vide.');
        }

        $p = new Personne();
        $p->setNom($objet->nom);
        $p->setPrenom($objet->prenom);

        $em->persist($p);
        $em->flush();

        return $this->json($p->toArray());
    }   


    #[Route('/api/personne/{id}', name: 'api_modifier',methods:["PUT"])]
    public function modifier(Personne $personne,Request $request, EntityManagerInterface $em ):Response
    {
        $objet = json_decode($request ->getContent());
        if ( empty($objet->nom) || empty($objet->prenom))
        {
            throw new NotFoundHttpException('le nom ou le prénom est vide.');
        }

        $personne->setNom($objet->nom);
        $personne->setPrenom($objet->prenom);
        
        if(!empty($objet->age))
        {
            $personne->setAge($objet->age);
        }
        $em->flush();

        return $this->json($personne->toArray());
    }

    #[Route('/api/personne/{id}', name: 'api_supprimer',methods:["DELETE"])]
    public function supprimer(Personne $personne, EntityManagerInterface $em ):Response
    {
        $em->remove($personne);
        $em->flush();
        
        return $this->json($personne->toArray());
    }
    
}

<?php

namespace App\Controller;

use App\Entity\Person;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Employment;
use OA\Items;
use OA\Schema;
use OA\JsonContent;
use OpenApi\Attributes as OA;

#[Route('/api', name: 'api_')]
class EmploymentController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $manager)
    {

        $this->em = $manager;
    }

    #[Route('/employments', name: 'employment_create', methods:['post'] )]
    #[OA\RequestBody(
        description: "Ajout d'un emploi pour une personne",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'companyName', type:'string'),
                new OA\Property(property: 'position', type:'string'),
                new OA\Property(property: 'start', type:'date'),
                new OA\Property(property: 'end', type:'date'),
                new OA\Property(property: 'personId', type:'int')
            ]
        )
    )]
    public function create(ManagerRegistry $doctrine, Request $request): JsonResponse
    {

        $data = json_decode($request->getContent(), true);

        if( empty($data['companyName']) || empty($data['position']) || empty($data['start']) || empty($data['personId'])){
            return $this->json('Veuillez vérifier les champs manquants.', 400);
        }

        $employment = new Employment();

        $employment->setCompanyName($data['companyName']);
        $employment->setPosition($data['position']);
        $start = \DateTime::createFromFormat("Y-m-d", $data['start']);
        $employment->setStart($start);

        $personId = $data['personId'];
        $person = $this->em->getRepository(Person::class)->find($personId);
        if($person){
            $employment->setPerson($person);
        }


        if (!empty($data['end'])) {
            $end = \DateTime::createFromFormat("Y-m-d", $data['end']);
            $employment->setEnd($end);
        }

        $this->em->persist($employment);
        $this->em->flush();

        $data = [
            'id' => $employment->getId(),
            'companyName' => $employment->getCompanyName(),
            'position' => $employment->getPosition()
        ];

        return $this->json($data);
    }

}
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

    #[Route('/persons-by-company', name: 'find_person_by_company', methods:['post'] )]
    #[OA\RequestBody(
        description: "Chercher les personnes selon la société",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'company', type:'string')
            ]
        )
    )]
    public function findPersonByCompany(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $comapnies = $this->em
            ->getRepository(Employment::class)
            ->findByCompany($data['company']);

        $data = [];

        foreach ($comapnies as $company) {
            $data[] = [
                'company' => $company->getCompanyName(),
                'emploi' => $company->getPosition(),
                'firstname' => $company->getPerson()->getFirstname(),
                'lastname' => $company->getPerson()->getLastname(),
                'birthday' => $company->getPerson()->getBirthday(),
                'age' => $company->getPerson()->getAge()
            ];
        }

        return $this->json($data);
    }

    #[Route('/employments-by-date-employments/{idPerson}', name: 'find_employments_by_date_employments', methods:['post'] )]
    #[OA\RequestBody(
        description: "Chercher les emplois d'une personne entre deux plages de dates",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'date-start', type:'date'),
                new OA\Property(property: 'date-end', type:'date')
            ]
        )
    )]
    public function findEmploymentsByDates(Request $request, ManagerRegistry $doctrine, int $idPerson): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $dateStart =  \DateTime::createFromFormat("Y-m-d",$data['date-start']);
        $dateEnd = \DateTime::createFromFormat("Y-m-d",$data['date-end']);

        $comapnies = $this->em
            ->getRepository(Employment::class)
            ->findEmploymentsByDates($idPerson, $dateStart, $dateEnd);

        $data = [];

        foreach ($comapnies as $company) {
            $data[] = [
                'id' => $company->getId(),
                'company' => $company->getCompanyName(),
                'emploi' => $company->getPosition(),
                'start' => $company->getStart(),
                'end' => $company->getEnd(),
            ];
        }

        return $this->json($data);
    }

}
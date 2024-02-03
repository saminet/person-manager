<?php

namespace App\Controller;

use App\Entity\Employment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Person;
use App\Service\PersonService;
use App\Repository\EmploymentRepository;
use OA\Items;
use OA\Schema;
use OA\JsonContent;
use OpenApi\Attributes as OA;

#[Route('/api', name: 'api_')]
class PersonController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $manager, PersonService $personService)
    {
        $this->em=$manager;
        $this->personService=$personService;
    }

    #[Route('/persons', name: 'person_index', methods:['get'] )]
    public function index(ManagerRegistry $doctrine): JsonResponse
    {
        $persons = $this->em
            ->getRepository(Person::class)
            ->findAllPersons();

        $data = [];

        foreach ($persons as $person) {

            $listEmployment=[];
            $employments = $person->getEmployments();
            foreach ($employments as $employment) {

                array_push($listEmployment,
                    ['companyName' => $employment->getCompanyName(),
                     'position' => $employment->getPosition()
                    ]
                    );
            }

            $data[] = [
                'id' => $person->getId(),
                'lastname' => $person->getLastname(),
                'firstname' => $person->getFirstname(),
                'birthday' => $person->getBirthday(),
                'age' => $person->getAge(),
                'employments' => $listEmployment
            ];
        }

        return $this->json($data);
    }

    #[Route('/persons', name: 'person_create', methods:['post'] )]
    #[OA\RequestBody(
        description: "Ajout d'une nouvelle personne",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'firstname', type:'string'),
                new OA\Property(property: 'lastname', type:'string'),
                new OA\Property(property: 'birthday', type:'date')

            ]
        )
    )]
    public function create(ManagerRegistry $doctrine, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $person = new Person();

        if( empty($data['firstname']) || empty($data['lastname']) || empty($data['birthday'])){
            return $this->json('Veuillez vérifier les champs manquants.', 400);
        }

        $birthday = \DateTime::createFromFormat("Y-m-d",$data['birthday']);
        if(!$this->personService->checkAge($birthday)){
            return $this->json('Seules les personnes de moins de 150 ans peuvent être enregistrées.', 400);
        }
        $person->setBirthday($birthday);
        $person->setFirstname($data['firstname']);
        $person->setLastname($data['lastname']);

        $this->em->persist($person);
        $this->em->flush();

        $data =  [
            'id' => $person->getId(),
            'firstname' => $person->getFirstname(),
            'lastname' => $person->getLastname(),
            'birthday' => $person->getBirthday()
        ];

        return $this->json($data);
    }


}
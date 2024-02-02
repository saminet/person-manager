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


}
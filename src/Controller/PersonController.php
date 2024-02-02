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



}
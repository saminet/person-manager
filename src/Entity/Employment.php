<?php

namespace App\Entity;

use App\Repository\EmploymentRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Person;
use Doctrine\DBAL\Types\Types;
#use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: EmploymentRepository::class)]
class Employment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["company"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["company"])]
    private ?string $companyName = null;

    #[ORM\Column(length: 255)]
    #[Groups(["company"])]
    private ?string $position = null;

    #[ORM\ManyToOne(targetEntity: Person::class, inversedBy: 'employments')]
    private Person $person;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $start = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable:true)]
    private ?\DateTimeInterface $end = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(string $companyName): static
    {
        $this->companyName = $companyName;

        return $this;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(string $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getPerson(): ?Person
    {
        return $this->person;
    }

    public function setPerson(?Person $person): static
    {
        $this->person = $person;

        return $this;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start): static
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?\DateTimeInterface
    {
        return $this->end;
    }

    public function setEnd(\DateTimeInterface $end): static
    {
        $this->end = $end;

        return $this;
    }

    public function __toString()
    {
        return $this->getCompanyName();
    }

}

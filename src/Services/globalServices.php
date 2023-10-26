<?php

namespace App\Services;

use App\Entity\Blog;
use App\Entity\ExperienceOverView;
use App\Entity\OfficeExperiences;
use App\Entity\Phrase;
use App\Entity\Portfolio;
use App\Entity\Services;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class globalServices
{

    private $entityManagerInterface;
    private $getUser;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManagerInterface = $entityManager;
        $this->getUser = $entityManager->getRepository(User::class)->findOneBY(['id' => 1]);
    }

    public function getUserDetails()
    {
        $data = [
            'email' => $this->getUser->getEmail(),
            "resume" => $this->getUser->getResume(),
            "image" => $this->getUser->getImage(),
            "name" => $this->getUser->getName(),
        ];
        return $data;
    }

    /*  public function downloadResume()
    {
        $resumeFileId = $this->getUser->getResume();
        $fileName = "Soumya.pdf";
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        readfile("https://drive.google.com/uc?id=$resumeFileId");
    } */

    /* get phrase */
    public function getPhrase()
    {
        $getPhrase = $this->entityManagerInterface->getRepository(Phrase::class)->findOneBy(['user_id' => 1]);
        $data = [
            'phrase' => $getPhrase->getPhraseName(),
            'description' => $getPhrase->getPhraseDescription(),
        ];
        return $data;
    }

    /* get services */
    public function getServices()
    {
        $getServices = $this->entityManagerInterface->getRepository(Services::class)->findBy(['user_id' => 1]);
        return $getServices;
    }

    /* experience section */
    public function experience()
    {
        $getExperienceOverview = $this->entityManagerInterface->getRepository(ExperienceOverView::class)->findOneBy(['user_id' => 1]);
        $getExperiences = $this->entityManagerInterface->getRepository(OfficeExperiences::class)->findBy(['user_id' => 1]);
        $experience['experienceOverView'] = $getExperienceOverview;
        $experience['experience'] = $getExperiences;
        return $experience;
    }

    /* blog section */
    public function getBlog()
    {
        $connection = $this->entityManagerInterface->getRepository(Blog::class);
        $experience = $connection->createQueryBuilder('p')
            ->where('p.user_id = :userId')
            ->setParameter('userId', 1)
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();
        return $experience;
    }

    /* projects */
    public function getProjects()
    {
        $connection = $this->entityManagerInterface->getRepository(Portfolio::class);
        $projects = $connection->createQueryBuilder('project')
            ->where('project.user_id = :userId')
            ->setParameter('userId', 1)
            ->setMaxResults(6)
            ->getQuery()
            ->getResult();
        return $projects;
    }
}

<?php

namespace App\Controller;
use App\Entity\ActivityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ActivityTypeController extends AbstractController
{
    

    #[Route('/activityTypes', name: 'get_activity_types', methods: ['GET'])]
    public function getAll(EntityManagerInterface $entityManager): JsonResponse
    {
        // Retrieve and return the list of activity types
        $activityTypeRepository = $entityManager->getRepository(ActivityType::class)->findAll();

        $activityTypesArray = [];
        foreach ($activityTypeRepository as $activityType) {
            $activityTypesArray[] = [
                'id' => $activityType->getId(),
                'name' => $activityType->getName(),
                'minotorsRequired' => $activityType->getNumberMonitors()
            ];
        }

        return $this->json($activityTypesArray);
    }
}

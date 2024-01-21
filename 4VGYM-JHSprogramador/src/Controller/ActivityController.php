<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\ActivityType;
use App\Entity\Monitor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ActivityController extends AbstractController
{
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }
    //conseguir TODAS las actividades
    #[Route('/activity', name: 'get_activity', methods: ['GET'])]
    public function getAll(EntityManagerInterface $entityManager): JsonResponse
    {
        $activities = $entityManager->getRepository(Activity::class)->findAll();

        $activitiesArray = [];
        foreach ($activities as $activity) {
            $activitiesArray[] = [
                'id' => $activity->getId(),
                'activity_type' => $activity->getActivityType()->getName(),
                'monitors' => $activity->getMonitors()->map(function (Monitor $monitor) {
                    return $monitor->getName();
                })->toArray(),
                'date_start' => $activity->getDateStart()->format('Y-m-d H:i:s'),
                'date_end' => $activity->getDateEnd()->format('Y-m-d H:i:s'),
            ];
        }

        return $this->json($activitiesArray);
    }
    //introducir una actividad
    #[Route('/activity', name: 'post_activity', methods: ['POST'])]
    public function postActivity(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $activity = new Activity();
        $activity->setActivityType($entityManager->getRepository(ActivityType::class)->find($data['activity_type']));
        $activity->setDateStart(new \DateTime($data['date_start']));
        $activity->setDateEnd(new \DateTime($data['date_end']));

        foreach ($data['monitors'] as $monitorId) {
            $activity->addMonitor($entityManager->getRepository(Monitor::class)->find($monitorId));
        }

        $errors = $this->validator->validate($activity);
        if (count($errors) > 0) {
            return $this->json($errors, 400);
        }

        $entityManager->persist($activity);
        $entityManager->flush();

        return $this->json([
            'id' => $activity->getId(),
            'activity_type' => $activity->getActivityType()->getName(),
            'monitors' => $activity->getMonitors()->map(function (Monitor $monitor) {
                return $monitor->getName();
            })->toArray(),
            'date_start' => $activity->getDateStart()->format('Y-m-d H:i:s'),
            'date_end' => $activity->getDateEnd()->format('Y-m-d H:i:s'),
        ]);
    }
    //actualizar una actividad
    #[Route('/activity/{id}', name: 'put_activity', methods: ['PUT'])]
    public function putActivity(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $activity = $entityManager->getRepository(Activity::class)->find($request->get('id'));
        if (!$activity) {
            throw $this->createNotFoundException(
                'No activity found for id '.$request->get('id')
            );
        }

        $activity->setActivityType($entityManager->getRepository(ActivityType::class)->find($data['activity_type']));
        $activity->setDateStart(new \DateTime($data['date_start']));
        $activity->setDateEnd(new \DateTime($data['date_end']));

        foreach ($data['monitors'] as $monitorId) {
            $activity->addMonitor($entityManager->getRepository(Monitor::class)->find($monitorId));
        }

        $errors = $this->validator->validate($activity);
        if (count($errors) > 0) {
            return $this->json($errors, 400);
        }

        $entityManager->flush();

        return $this->json([
            'id' => $activity->getId(),
            'activity_type' => $activity->getActivityType()->getName(),
            'monitors' => $activity->getMonitors()->map(function (Monitor $monitor) {
                return $monitor->getName();
            })->toArray(),
            'date_start' => $activity->getDateStart()->format('Y-m-d H:i:s'),
            'date_end' => $activity->getDateEnd()->format('Y-m-d H:i:s'),
        ]);
    }
    //borrar una actividad
    #[Route('/activity/{id}', name: 'delete_activity', methods: ['DELETE'])]
    public function deleteActivity(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $activity = $entityManager->getRepository(Activity::class)->find($request->get('id'));
        if (!$activity) {
            throw $this->createNotFoundException(
                'No activity found for id '.$request->get('id')
            );
        }

        $entityManager->remove($activity);
        $entityManager->flush();

        return $this->json([
            'id' => $activity->getId(),
            'activity_type' => $activity->getActivityType()->getName(),
            'monitors' => $activity->getMonitors()->map(function (Monitor $monitor) {
                return $monitor->getName();
            })->toArray(),
            'date_start' => $activity->getDateStart()->format('Y-m-d H:i:s'),
            'date_end' => $activity->getDateEnd()->format('Y-m-d H:i:s'),
        ]);
    }
} 

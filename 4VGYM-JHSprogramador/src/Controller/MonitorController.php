<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Monitor;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MonitorController extends AbstractController
{
    //conseguir TODOS los monitores
    #[Route('/monitor', name: 'get_monitor', methods: ['GET'])]
    public function getAll(EntityManagerInterface $entityManager): JsonResponse
    {
        $monitors = $entityManager->getRepository(Monitor::class)->findAll();

        $monitorsArray = [];
        foreach ($monitors as $monitor) {
            $monitorsArray[] = [
                'id' => $monitor->getId(),
                'name' => $monitor->getName(),
                'email' => $monitor->getEmail(), 
                'phone' => $monitor->getPhone(),
                'photo' => $monitor->getPhoto(),
            ];
        }

        return $this->json($monitorsArray);
    }
    //introducir un monitor
    #[Route('/monitor', name: 'post_monitor', methods: ['POST'])]
    public function postMonitor(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $monitor = new Monitor();
        $monitor->setName($data['name']);
        $monitor->setEmail($data['email']);
        $monitor->setPhone($data['phone']);
        $monitor->setPhoto($data['photo']);

        $errors = $validator->validate($monitor);
        if (count($errors) > 0) {
            return $this->json($errors, 400);
        }

        $entityManager->persist($monitor);
        $entityManager->flush();

        return $this->json([
            'id' => $monitor->getId(),
            'name' => $monitor->getName(),
            'email' => $monitor->getEmail(),
            'phone' => $monitor->getPhone(),
            'photo' => $monitor->getPhoto(),
        ]);
        }
    //actualizar un monitor
    #[Route('/monitor/{id}', name: 'put_monitor', methods: ['PUT'])]
    public function putMonitor(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $monitor = $entityManager->getRepository(Monitor::class)->find($request->get('id'));
        if (!$monitor) {
            throw $this->createNotFoundException(
                'No monitor found for id '.$request->get('id')
            );
        }

        $monitor->setName($data['name']);
        $monitor->setEmail($data['email']);
        $monitor->setPhone($data['phone']);
        $monitor->setPhoto($data['photo']);

        $errors = $validator->validate($monitor);
        if (count($errors) > 0) {
            return $this->json($errors, 400);
        }

        $entityManager->flush();

        return $this->json([
            'id' => $monitor->getId(),
            'name' => $monitor->getName(),
            'email' => $monitor->getEmail(),
            'phone' => $monitor->getPhone(),
            'photo' => $monitor->getPhoto(),
        ]);


    }
    //borrar un monitor
    #[Route('/monitor/{id}', name: 'delete_monitor', methods: ['DELETE'])]
    public function deleteMonitor(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $monitor = $entityManager->getRepository(Monitor::class)->find($request->get('id'));
        if (!$monitor) {
            throw $this->createNotFoundException(
                'No monitor found for id '.$request->get('id')
            );
        }

        $entityManager->remove($monitor);
        $entityManager->flush();

        return $this->json(['message' => 'Monitor eliminado con éxito.']);
    }
}
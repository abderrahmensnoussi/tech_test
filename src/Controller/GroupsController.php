<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;
use App\Entity\Group;

#[Route('/api/groups', name: 'api_')]
class GroupsController extends AbstractController
{
    #[Route('/', name: 'groups', methods: ['GET'])]
    public function users(ManagerRegistry $doctrine): JsonResponse
    {
        $groups = $doctrine
            ->getRepository(Group::class)
            ->findAll();
   
        $data = [];
   
        foreach ($groups as $group) {
           $data[] = [
               'id' => $group->getId(),
               'name' => $group->getName()
           ];
        }
        
        return new JsonResponse($data);
    }

    #[Route('/{id}', name: 'group', methods: ['GET'])]
    public function user(int $id, ManagerRegistry $doctrine): JsonResponse
    {
        $group = $doctrine
            ->getRepository(Group::class)
            ->find($id);

        if ($group !== null) {
            $data = [
                'id' => $group->getId(),
                'name' => $group->getName()
            ];

            return new JsonResponse($data);
        }

        return new JsonResponse('group not found', 404);
    }

    #[Route('/', name: 'group_create',methods: ['POST'])]
    public function createUser(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        $entityManager = $doctrine->getManager();
   
        $group = new Group();
        $group->setName($request->request->get('name'));

        $entityManager->persist($group);
        $entityManager->flush();
   
        $data = [
            'id' => $group->getId(),
            'name' => $group->getName()
        ];

        return $this->json($data);
    }

    #[Route('/{id}', name: 'group_update',methods: ['PUT'])]
    public function updateUser(int $id, Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $group = $doctrine
            ->getRepository(Group::class)
            ->find($id);

        if ($group === null) {
            return new JsonResponse('group not found', 404);
        }
        $group->setName($request->request->get('name'));

        $entityManager->flush();

        $data = [
            'id' => $group->getId(),
            'name' => $group->getName()
        ];

        return $this->json($data);
    }
}

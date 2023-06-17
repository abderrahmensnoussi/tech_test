<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;
use App\Entity\Group;

#[Route('/api/users', name: 'api_')]
class UsersController extends AbstractController
{
    #[Route('/', name: 'users', methods: ['GET'])]
    public function users(ManagerRegistry $doctrine): JsonResponse
    {
        $users = $doctrine
            ->getRepository(User::class)
            ->findAll();
   
        $data = [];
   
        foreach ($users as $user) {
           $data[] = [
               'id' => $user->getId(),
               'email' => $user->getEmail(),
               'firstName' => $user->getFirstName(),
               'lastName' => $user->getLastname(),
               'actif' => $user->isActif(),
               'creationDate' => $user->getCreationDate(),
               'group' => $user->getUserGroup()->getName()
           ];
        }
        
        return new JsonResponse($data);
    }

    #[Route('/{id}', name: 'user', methods: ['GET'])]
    public function user(int $id, ManagerRegistry $doctrine): JsonResponse
    {
        $user = $doctrine
            ->getRepository(User::class)
            ->find($id);

        if ($user !== null) {
            $data = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'firstname' => $user->getFirstName(),
                'lastname' => $user->getLastname(),
                'actif' => $user->isActif(),
                'creationDate' => $user->getCreationDate(),
                'group' => $user->getUserGroup()->getName()
            ];

            return new JsonResponse($data);
        }

        return new JsonResponse('user not found', 404);
    }

    #[Route('/', name: 'user_create',methods: ['POST'])]
    public function createUser(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        $entityManager = $doctrine->getManager();
   
        $dateNow = new \DateTime();
        $user = new User();
        $user->setEmail($request->request->get('email'));
        $user->setFirstname($request->request->get('firstname'));
        $user->setLastname($request->request->get('lastname'));
        $user->setActif((bool) $request->request->get('actif'));
        $user->setCreationDate($dateNow);
        $group = $doctrine
            ->getRepository(Group::class)
            ->find((int) $request->request->get('group'));
        $user->setUserGroup($group);

        $entityManager->persist($user);
        $entityManager->flush();
   
        $data = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'firstname' => $user->getFirstName(),
            'lastname' => $user->getLastname(),
            'actif' => $user->isActif(),
            'creationDate' => $user->getCreationDate(),
            'group' => $user->getUserGroup()->getName()
        ];

        return $this->json($data);
    }

    #[Route('/{id}', name: 'user_update',methods: ['PUT'])]
    public function updateUser(int $id, Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        $entityManager = $doctrine->getManager();

        $user = $doctrine
            ->getRepository(User::class)
            ->find($id);

        if ($user === null) {
            return new JsonResponse('user not found', 404);
        }
        $user->setEmail($request->request->get('email'));
        $user->setFirstname($request->request->get('firstname'));
        $user->setLastname($request->request->get('lastname'));
        $user->setActif((bool) $request->request->get('actif'));
        $group = $doctrine
            ->getRepository(Group::class)
            ->find((int) $request->request->get('group'));
        $user->setUserGroup($group);

        $entityManager->flush();

        $data = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'firstname' => $user->getFirstName(),
            'lastname' => $user->getLastname(),
            'actif' => $user->isActif(),
            'creationDate' => $user->getCreationDate(),
            'group' => $user->getUserGroup()->getName()
        ];

        return $this->json($data);
    }
}

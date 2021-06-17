<?php

namespace App\Controller;

use App\Repository\TaskRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

class TaskController extends AbstractController
{
    #[Route('/tasks/{projectId}', name: 'project_tasks', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Request $request, TaskRepository $taskRepository, PaginatorInterface $paginator, int $projectId): Response
    {
        $tasks = $taskRepository->getPagination($request, $paginator, ['project' => $projectId]);

        return $this->render('tasks.html.twig', [
            'tasks' => $tasks,
        ]);
    }
}

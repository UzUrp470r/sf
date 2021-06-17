<?php

namespace App\Controller\Api;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use App\Controller\Api\ApiBaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Knp\Component\Pager\PaginatorInterface;

class TaskController extends ApiBaseController
{
    /**
     * Set errors
     * Should be in a specific API error configuration preferably with error codes
     * @var array
     */
    private $errors = [
        'not_found' => 'Task not found',
        'no_tasks' => 'Project has no tasks',
    ];

    /**
     * List all tasks for a project and paginate them if needed
     *
     * @param TaskRepository $taskRepository
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @param SerializerInterface $serializer
     * @param int $taskId
     *
     * @return JsonResponse
     */
    #[Route('/api/project-tasks/{taskId}', name: 'api_project_tasks_index', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function index(TaskRepository $taskRepository, Request $request, PaginatorInterface $paginator, SerializerInterface $serializer, int $taskId): JsonResponse
    {
        $paginatorResult = $taskRepository->getPagination($request, $paginator, ['project' => $taskId]);

        $errors = [];
        if ($paginatorResult->getTotalItemCount() === 0) {
            $errors[] = $this->errors['no_tasks'];
        }

        return $this->createApiResponse(
            true,
            $this->getNormalisedArray($serializer, $paginatorResult->getItems()),
            $errors,
            $this->getApiPagination($paginatorResult)
        );
    }

    /**
     * Add a new task to a project
     * @param Request $request
     *
     * @return JsonResponse
     */
    #[Route('/api/task/new', name: 'api_task_new', methods: ['POST'])]
    public function new(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $taskStatusRepository = $this->getDoctrine()->getRepository('App:ProjectStatus');
        $newStatus = $taskStatusRepository->findOneBy(['status' => 'new']);  //Better to use predefined environment variable

        $task = new Task();
        $task->setStatus($newStatus);

        $form = $this->createForm(TaskType::class, $task);
        $form->submit($data, false);

        if ($form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($task);
            $entityManager->flush();

            return $this->createApiResponse(true, ['id' => $task->getId()]);
        }

        return $this->createApiResponse(false, [], $this->getFormErrors($form));
    }

    /**
     * Show a specific task by task ID
     *
     * @param TaskRepository $taskRepository
     * @param SerializerInterface $serializer
     * @param int $taskId
     *
     * @return JsonResponse
     */
    #[Route('/api/task/{taskId}', name: 'api_task_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(TaskRepository $taskRepository, SerializerInterface $serializer, int $taskId): JsonResponse
    {
        $task = $taskRepository->findOneBy(['id' => $taskId, 'deleted' => 0]);

        if (is_null($task)) {
            return $this->createApiResponse(false, [], [$this->errors['not_found']]);
        }

        return $this->createApiResponse(true, $this->getNormalisedArray($serializer, $task));
    }

    /**
     * Edit an existing task
     *
     * @param TaskRepository $taskRepository
     * @param Request $request
     * @param int $taskId
     *
     * @return JsonResponse
     */
    #[Route('/api/task/{taskId}', name: 'api_task_edit', requirements: ['taskId' => '\d+'], methods: ['PATCH'])]
    public function edit(TaskRepository $taskRepository, Request $request, int $taskId): JsonResponse
    {
        $task = $taskRepository->findOneBy(['id' => $taskId, 'deleted' => 0]);

        if (is_null($task)) {
            return $this->createApiResponse(false, [], [$this->errors['not_found']]);
        }

        $data = json_decode($request->getContent(), true);

        $form = $this->createForm(TaskType::class, $task);
        $form->submit($data, false);    //get only the sent request data

        if ($form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($task);
            $entityManager->flush();

            return $this->createApiResponse(true, ['id' => $task->getId()]);
        }

        return $this->createApiResponse(false, [], $this->getFormErrors($form));
    }

    /**
     * Delete a task (mask as deleted only)
     *
     * @param TaskRepository $taskRepository
     * @param int $taskId
     *
     * @return JsonResponse
     */
    #[Route('/api/task/{taskId}', name: 'api_task_delete', requirements: ['taskId' => '\d+'], methods: ['DELETE'])]
    public function delete(TaskRepository $taskRepository, int $taskId): JsonResponse
    {
        $task = $taskRepository->findOneBy(['id' => $taskId]);

        if (is_null($task)) {
            return $this->createApiResponse(false, [], [$this->errors['not_found']]);
        }

        $task->setDeleted(1);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($task);
        $entityManager->flush();

        return $this->createApiResponse(true, ['id' => $task->getId()]);
    }
}

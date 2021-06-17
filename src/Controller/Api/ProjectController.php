<?php

namespace App\Controller\Api;

use App\Controller\Api\ApiBaseController;
use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/api/project')]
class ProjectController extends ApiBaseController
{
    /**
     * Set errors
     * Should be in a specific API error configuration preferably with error codes
     * @var array
     */
    private $errors = [
        'not_found' => 'Project not found',
    ];

    #[Route('/', name: 'api_project_index', methods: ['GET'])]
    public function index(ProjectRepository $projectRepository, Request $request, PaginatorInterface $paginator, SerializerInterface $serializer): JsonResponse
    {
        $paginatorResult = $projectRepository->getPagination($request, $paginator);

        return $this->createApiResponse(
            true,
            $this->getNormalisedArray($serializer, $paginatorResult->getItems()),
            [],
            $this->getApiPagination($paginatorResult)
        );
    }

    #[Route('/', name: 'api_project_new', methods: ['POST'])]
    public function new(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->submit($data);

        if ($form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($project);
            $entityManager->flush();

            return $this->createApiResponse(true, ['id' => $project->getId()]);
        }

        return $this->createApiResponse(false, [], $this->getFormErrors($form));
    }

    #[Route('/{id}', name: 'api_project_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(ProjectRepository $projectRepository, SerializerInterface $serializer, int $id): JsonResponse
    {
        $project = $projectRepository->findOneBy(['id' => $id, 'deleted' => 0]);

        if (is_null($project)) {
            return $this->createApiResponse(false, [], [$this->errors['not_found']]);
        }

        return $this->createApiResponse(true, $this->getNormalisedArray($serializer, $project));
    }

    #[Route('/{id}', name: 'api_project_edit', requirements: ['id' => '\d+'], methods: ['PATCH'])]  //could be POST or PUT here as well
    public function edit(ProjectRepository $projectRepository, Request $request, int $id): JsonResponse
    {
        $project = $projectRepository->findOneBy(['id' => $id, 'deleted' => 0]);

        if (is_null($project)) {
            return $this->createApiResponse(false, [], [$this->errors['not_found']]);
        }

        $data = json_decode($request->getContent(), true);

        $form = $this->createForm(ProjectType::class, $project);
        $form->submit($data, false);    //get only the sent request data

        if ($form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($project);
            $entityManager->flush();

            return $this->createApiResponse(true, ['id' => $project->getId()]);
        }

        return $this->createApiResponse(false, [], $this->getFormErrors($form));
    }

    #[Route('/{id}', name: 'api_project_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function delete(ProjectRepository $projectRepository, int $id): JsonResponse
    {
        $project = $projectRepository->findOneBy(['id' => $id]);

        if (is_null($project)) {
            return $this->createApiResponse(false, [], [$this->errors['not_found']]);
        }

        $project->setDeleted(1);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($project);
        $entityManager->flush();

        return $this->createApiResponse(true, ['id' => $project->getId()]);
    }
}

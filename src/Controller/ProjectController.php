<?php

namespace App\Controller;

use App\Repository\ProjectRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;

class ProjectController extends AbstractController
{
    #[Route('/projects', name: 'projects')]
    public function index(ProjectRepository $projectRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $projects = $projectRepository->getPagination($request, $paginator);

        return $this->render('projects.html.twig', [
            'projects' => $projects,
        ]);
    }
}

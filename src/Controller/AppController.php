<?php

namespace App\Controller;

use App\Entity\Projects;
use App\Entity\Tasks;
use App\Entity\User;
use App\Form\ProjectNewType;
use App\Form\ProjectType;
use App\Form\TasksType;
use App\Repository\ProjectsRepository;
use App\Repository\TasksRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    #[Route('/', name: 'app_app')]
    public function index(ProjectsRepository $projectsRepository): Response
    {
        return $this->render('app/index.html.twig', [
            'projects' => $projectsRepository->getNotMyProject($this->getUser()),
            'myProject' => $projectsRepository->getMyProject($this->getUser())
        ]);
    }

    #[Route('/{id}', name: 'project_show', requirements: ['id' => '\d+'])]
    public function projectShow(Projects $projects, UserRepository $userRepository, Request $request, ProjectsRepository $projectsRepository): Response
    {
        $allUser = $userRepository->findAll();
        $form = $this->createForm(ProjectType::class, $projects);
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $projectsRepository->save($projects, true);

            return $this->redirectToRoute('project_show', ['id' => $projects->getId()]);
        }

        return $this->render('app/show.html.twig', [
            'project' => $projects,
            'users' => $allUser,
            'form' => $form->createView()
        ]);
    }

    #[Route('/new', name: 'project_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProjectsRepository $projectsRepository): Response
    {
        $project = new Projects();
        $form = $this->createForm(ProjectNewType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $project->setCreator($this->getUser());
            $projectsRepository->save($project, true);

            return $this->redirectToRoute('app_app', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('app/new.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }

    #[Route('/task/new/{id}', name: 'task_new', methods: ['GET', 'POST'])]
    public function task_new(Request $request, ProjectsRepository $projectsRepository, TasksRepository $repository, $id): Response
    {
        $tasks = new Tasks();
        $project = $projectsRepository->findOneBy(['id' => $id]);
        $form = $this->createForm(TasksType::class, $tasks);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tasks->setProjects($project);
            $repository->save($tasks, true);

            return $this->redirectToRoute('project_show', ['id' => $id], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('app/new.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }
}

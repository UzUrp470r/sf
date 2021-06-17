<?php

namespace App\EntityListener;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use DateTime;
use DateInterval;
use Doctrine\ORM\EntityManager;

class ProjectListener
{
    /**
     * @var EntityManager
     */
    protected $em;

    function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * PostLoad Entity event handling
     * @param Project $project
     *
     * @return [type]
     */
    public function postLoad(Project $project)
    {
        /**
         * Set a new status to all Project entities when loading for the first time
         */
        if ($project->getStatus() === null) {
            $projectStatusRepository = $this->em->getRepository('App:ProjectStatus');
            $newStatus = $projectStatusRepository->findOneBy(['status' => 'new']);  //Better to use predefined environment variable

            $project->setStatus($newStatus);
        }

        /**
         * Set a default interval for the project of 0 days
         */
        if ($project->getDuration() === null) {
            $project->setDuration(new DateInterval('P0D'));
        }

        $tasks = $project->getTasks();

        if (!$tasks->isEmpty()) {
            $start = new DateTime();
            $end = clone $start;

            $allDone = true;

            foreach ($tasks as $task) {
                $end->add($task->getDuration());

                /**
                 * Use simple checks to get the project status based on an imaginary priority
                 * One failed task = failed project
                 * One pending task = pending project
                 * All done tasks = done project
                 */

                $statusId = $task->getStatus()->getId();

                if ($statusId != ProjectRepository::STATUSES['done']) {
                    $allDone = false;
                }

                if ($statusId == ProjectRepository::STATUSES['failed']) {
                    $project->setStatus($task->getStatus());
                    break;
                }

                if ($statusId == ProjectRepository::STATUSES['pending']) {
                    $project->setStatus($task->getStatus());
                }
            }

            if ($allDone) {
                //use last task done status entity
                $project->setStatus($tasks->first()->getStatus());
            }

            $project->setDuration($start->diff($end));
        }
    }
}
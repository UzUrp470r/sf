<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Base API controller handling common response
 */
abstract class ApiBaseController extends AbstractController
{
    /**
     * Get the API pagination from the PaginationInterface
     * Possible ease of use additions
     * - next_page
     * - prev_page
     *
     * @param PaginationInterface $paginatorResult
     *
     * @return array
     */
    protected function getApiPagination(PaginationInterface $paginatorResult): array
    {
        if ($paginatorResult->getTotalItemCount() < $paginatorResult->getItemNumberPerPage()) {
            return [];  //no pagination needed
        }

        return [
            'page' => $paginatorResult->getCurrentPageNumber(),
            'items_per_page' => $paginatorResult->getItemNumberPerPage(),
            'total_items' => $paginatorResult->getTotalItemCount(),
        ];
    }

    /**
     * Common API response
     *
     * @param bool $status
     * @param array $data
     * @param array $errors
     *
     * @return JsonResponse
     */
    protected function createApiResponse(bool $status, array $data = [], array $errors = [], array $pagination = []): JsonResponse
    {
        $data = [
            'code' => $status ? '0' : '-1',
            'data' => $data,
            'validation_errors' => $errors,
        ];

        /**
         * Not in the assignment but better to be separated
         * Alternatevely the pagination could be sent in the headers
         */
        if (!empty($pagination)) {
            $data['pagination'] = $pagination;
        }

        return new JsonResponse($data);
    }

    /**
     * Get form errors as array for each field
     * @param Form $form
     *
     * @return array
     */
    protected function getFormErrors(Form $form): array
    {
        $errors = [];

        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = $this->getFormErrors($childForm)) {
                    $errors[$childForm->getName()] = $childErrors;
                }
            }
        }

        return $errors;
    }

    /**
     * Get the object(s) normalized and as array using Serializer
     *
     * Other possible options:
     *
     * Adding "Exclude" annotations would also remove the linked entities from the results
     * implementing JsonSerializable interface and adding jsonSerialize in the entity
     * toArray Entity functions similar to the previous approach depending on the needed output
     * A custom serializer for each Entity or a common one with configuration
     *
     * @param SerializerInterface $serializer
     * @param mixed $objects Single object or a collection of objects
     *
     * @return array
     */
    protected function getNormalisedArray(SerializerInterface $serializer, $objects): array
    {
        $objectArray = $serializer->normalize($objects, 'null', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            },
            AbstractNormalizer::IGNORED_ATTRIBUTES => [
                "__initializer__",
                "__cloner__",
                "__isInitialized__",
                "tasks",
                "project",
                "deleted",
            ]
        ]);

        return $objectArray;
    }
}

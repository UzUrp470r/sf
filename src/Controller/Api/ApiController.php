<?php

namespace App\Controller\Api;

use App\Controller\Api\ApiBaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Handles all API calls with specific endpoints /api/{api-endpoint}
 * In not specific endpoint/routing is found then return error
 */
class ApiController extends ApiBaseController
{
    #[Route('/api/{endpoint}', name: 'api', requirements: ["endpoint" => ".*"])]
    public function index(): JsonResponse
    {
        return $this->createApiResponse(false, [], [
            'No API endpoint found!',
        ]);
    }
}

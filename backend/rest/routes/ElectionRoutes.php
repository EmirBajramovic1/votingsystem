<?php

/**
 * @OA\Get(
 *     path="/elections",
 *     tags={"elections"},
 *     summary="Get all elections",
 *     security={{"BearerAuth": {}}},
 *     @OA\Response(response=200, description="List of elections")
 * )
 */
Flight::route('GET /elections', function () {
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::USER]);
    Flight::json(Flight::electionService()->getAll());
});

/**
 * @OA\Get(
 *     path="/elections/active",
 *     tags={"elections"},
 *     summary="Get active elections",
 *     security={{"BearerAuth": {}}},
 *     @OA\Response(response=200, description="Active elections")
 * )
 */
Flight::route('GET /elections/active', function () {
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::USER]);
    Flight::json(Flight::electionService()->getActiveElections());
});

/**
 * @OA\Post(
 *     path="/elections",
 *     tags={"elections"},
 *     summary="Create new election",
 *     security={{"BearerAuth": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"title","start_date","end_date"},
 *             @OA\Property(property="title", type="string"),
 *             @OA\Property(property="start_date", type="string"),
 *             @OA\Property(property="end_date", type="string")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Election created")
 * )
 */
Flight::route('POST /elections', function () {
    try {
        Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
        $data = Flight::request()->data->getData();

        $id = Flight::electionService()->createElection($data);

        Flight::json([
            'success' => true,
            'id' => $id
        ]);
    } catch (Exception $e) {
        Flight::json([
            'success' => false,
            'message' => $e->getMessage()
        ], 400);
    }
});

/**
 * @OA\Post(
 *     path="/elections/{electionId}/candidates/{candidateId}",
 *     tags={"elections"},
 *     summary="Assign candidate to election",
 *     security={{"BearerAuth": {}}},
 *     @OA\Parameter(
 *         name="electionId",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="candidateId",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=200, description="Candidate assigned to election")
 * )
 */
Flight::route('POST /elections/@electionId/candidates/@candidateId', function ($electionId, $candidateId) {
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);

    Flight::electionCandidateService()
        ->addCandidateToElection($electionId, $candidateId);

    Flight::json([
        "success" => true,
        "message" => "Candidate assigned to election"
    ]);
});

/**
 * @OA\Get(
 *     path="/elections/{id}/candidates",
 *     tags={"elections"},
 *     summary="Get candidates for an election",
 *     security={{"BearerAuth": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=200, description="Candidates for election")
 * )
 */
Flight::route('GET /elections/@id/candidates', function ($id) {
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::USER]);

    $candidates = Flight::candidateService()->getCandidatesByElection($id);
    Flight::json($candidates);
});

/**
 * @OA\Get(
 *     path="/elections/{electionId}/results",
 *     tags={"results"},
 *     summary="Get election results",
 *     security={{"BearerAuth": {}}},
 *     @OA\Parameter(
 *         name="electionId",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=200, description="Election results")
 * )
 */
Flight::route('GET /elections/@electionId/results', function ($electionId) {
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::USER]);

    $results = Flight::electionCandidateService()->getResultsByElection($electionId);

    Flight::json($results);
});

/**
 * @OA\Delete(
 *     path="/elections/{id}",
 *     tags={"elections"},
 *     summary="Delete election",
 *     security={{"BearerAuth": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=200, description="Election deleted")
 * )
 */
Flight::route('DELETE /elections/@id', function ($id) {
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);

    Flight::electionService()->deleteElection($id);

    Flight::json([
        "success" => true,
        "message" => "Election deleted"
    ]);
});
?>
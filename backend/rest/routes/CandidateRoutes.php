<?php
/**
 * @OA\Get(
 *     path="/candidates",
 *     tags={"candidates"},
 *     summary="Get all candidates",
 *     security={{"BearerAuth": {}}},
 *     @OA\Response(
 *         response=200, 
 *         description="List of all candidates"
 *     )
 * )
 */
Flight::route('GET /candidates', function () {
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::USER]);
    Flight::json(Flight::candidateService()->getAll());
});

/**
 * @OA\Get(
 *     path="/candidates/{id}",
 *     tags={"candidates"},
 *     security={{"BearerAuth": {}}},
 * )
 */
Flight::route('GET /candidates/@id', function($id){
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::USER]);
    Flight::json(Flight::candidateService()->getById($id));
});

/**
 * @OA\Post(
 *     path="/candidates",
 *     tags={"candidates"},
 *     summary="Create new candidate",
 *     security={{"BearerAuth": {}}},
 * )
 */
Flight::route('POST /candidates', function () {
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    Flight::json(
        Flight::candidateService()->create(Flight::request()->data->getData())
    );
});

/**
 * @OA\Delete(
 *     path="/candidates/{id}",
 *     tags={"candidates"},
 *     summary="Delete candidate",
 *     security={{"BearerAuth": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=200, description="Candidate deleted")
 * )
 */
Flight::route('DELETE /candidates/@id', function ($id) {
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);

    Flight::candidateService()->deleteCandidate($id);

    Flight::json([
        "success" => true,
        "message" => "Candidate deleted"
    ]);
});
?>
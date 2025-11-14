<?php
/**
 * @OA\Get(
 *     path="/candidates",
 *     tags={"candidates"},
 *     summary="Get all candidates",
 *     @OA\Response(
 *         response=200,
 *         description="List of all candidates"
 *     )
 * )
 */
Flight::route('GET /candidates', function(){
    Flight::json(Flight::candidateService()->getAll());
});

/**
 * @OA\Get(
 *     path="/candidates/{id}",
 *     tags={"candidates"},
 *     summary="Get candidate by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Candidate ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Candidate details"
 *     )
 * )
 */
Flight::route('GET /candidates/@id', function($id){
    Flight::json(Flight::candidateService()->getById($id));
});

/**
 * @OA\Get(
 *     path="/elections/{electionId}/candidates",
 *     tags={"candidates"},
 *     summary="Get candidates by election",
 *     @OA\Parameter(
 *         name="electionId",
 *         in="path",
 *         required=true,
 *         description="Election ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of candidates for the election"
 *     )
 * )
 */
Flight::route('GET /elections/@electionId/candidates', function($electionId){
    Flight::json(Flight::candidateService()->getCandidatesByElection($electionId));
});

/**
 * @OA\Post(
 *     path="/candidates",
 *     tags={"candidates"},
 *     summary="Create new candidate",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"first_name", "last_name", "party"},
 *             @OA\Property(property="first_name", type="string", example="Kamala"),
 *             @OA\Property(property="last_name", type="string", example="Harris"),
 *             @OA\Property(property="party", type="string", example="Democratic Party")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Candidate created successfully"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Candidate creation failed"
 *     )
 * )
 */
Flight::route('POST /candidates', function(){
    $data = Flight::request()->data->getData();
    try {
        $result = Flight::candidateService()->createCandidate($data);
        Flight::json(['success' => true, 'message' => 'Candidate created successfully', 'data' => $result]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Post(
 *     path="/elections/{electionId}/candidates/{candidateId}",
 *     tags={"candidates"},
 *     summary="Add candidate to election",
 *     @OA\Parameter(
 *         name="electionId",
 *         in="path",
 *         required=true,
 *         description="Election ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Parameter(
 *         name="candidateId",
 *         in="path",
 *         required=true,
 *         description="Candidate ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Candidate added to election"
 *     )
 * )
 */
Flight::route('POST /elections/@electionId/candidates/@candidateId', function($electionId, $candidateId){
    $result = Flight::electionCandidateService()->addCandidateToElection($electionId, $candidateId);
    Flight::json(['success' => $result, 'message' => $result ? 'Candidate added to election' : 'Failed to add candidate']);
});
?>
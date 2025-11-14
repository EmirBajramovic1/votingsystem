<?php
/**
 * @OA\Get(
 *     path="/elections",
 *     tags={"elections"},
 *     summary="Get all elections",
 *     @OA\Response(
 *         response=200,
 *         description="List of all elections"
 *     )
 * )
 */
Flight::route('GET /elections', function(){
    Flight::json(Flight::electionService()->getAll());
});

/**
 * @OA\Get(
 *     path="/elections/active",
 *     tags={"elections"},
 *     summary="Get active elections",
 *     @OA\Response(
 *         response=200,
 *         description="List of active elections"
 *     )
 * )
 */
Flight::route('GET /elections/active', function(){
    Flight::json(Flight::electionService()->getActiveElections());
});

/**
 * @OA\Get(
 *     path="/elections/upcoming",
 *     tags={"elections"},
 *     summary="Get upcoming elections",
 *     @OA\Response(
 *         response=200,
 *         description="List of upcoming elections"
 *     )
 * )
 */
Flight::route('GET /elections/upcoming', function(){
    Flight::json(Flight::electionService()->getUpcomingElections());
});

/**
 * @OA\Get(
 *     path="/elections/{id}",
 *     tags={"elections"},
 *     summary="Get election by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Election ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Election details"
 *     )
 * )
 */
Flight::route('GET /elections/@id', function($id){
    Flight::json(Flight::electionService()->getById($id));
});

/**
 * @OA\Post(
 *     path="/elections",
 *     tags={"elections"},
 *     summary="Create new election",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"title", "start_date", "end_date"},
 *             @OA\Property(property="title", type="string", example="Presidential Election 2024"),
 *             @OA\Property(property="description", type="string", example="National presidential election"),
 *             @OA\Property(property="start_date", type="string", format="date-time", example="2024-01-01 00:00:00"),
 *             @OA\Property(property="end_date", type="string", format="date-time", example="2024-01-31 23:59:59")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Election created successfully"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Election creation failed"
 *     )
 * )
 */
Flight::route('POST /elections', function(){
    $data = Flight::request()->data->getData();
    try {
        $result = Flight::electionService()->createElection($data);
        Flight::json(['success' => true, 'message' => 'Election created successfully', 'data' => $result]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Put(
 *     path="/elections/{id}",
 *     tags={"elections"},
 *     summary="Update election",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Election ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="title", type="string", example="Updated Election Title"),
 *             @OA\Property(property="description", type="string", example="Updated description"),
 *             @OA\Property(property="start_date", type="string", format="date-time"),
 *             @OA\Property(property="end_date", type="string", format="date-time")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Election updated"
 *     )
 * )
 */
Flight::route('PUT /elections/@id', function($id){
    $data = Flight::request()->data->getData();
    $result = Flight::electionService()->update($id, $data);
    Flight::json(['success' => $result, 'message' => $result ? 'Election updated' : 'Update failed']);
});

/**
 * @OA\Delete(
 *     path="/elections/{id}",
 *     tags={"elections"},
 *     summary="Delete election",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Election ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Election deleted"
 *     )
 * )
 */
Flight::route('DELETE /elections/@id', function($id){
    $result = Flight::electionService()->delete($id);
    Flight::json(['success' => $result, 'message' => $result ? 'Election deleted' : 'Delete failed']);
});
?>
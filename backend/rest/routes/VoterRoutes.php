<?php
/**
 * @OA\Get(
 *     path="/voters",
 *     tags={"voters"},
 *     summary="Get all voters",
 *     security={{"BearerAuth": {}}},
 *     @OA\Response(
 *         response=200,
 *         description="List of all voters"
 *     )
 * )
 */
Flight::route('GET /voters', function(){
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    Flight::json(Flight::voterService()->getAll());
});

/**
 * @OA\Get(
 *     path="/voters/{id}",
 *     tags={"voters"},
 *     summary="Get voter by ID",
 *     security={{"BearerAuth": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Voter ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Voter details"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Voter not found"
 *     )
 * )
 */
Flight::route('GET /voters/@id', function($id){
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    Flight::json(Flight::voterService()->getById($id));
});

/**
 * @OA\Post(
 *     path="/voters/register",
 *     tags={"voters"},
 *     summary="Register new voter",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"first_name", "last_name", "email", "password"},
 *             @OA\Property(property="first_name", type="string", example="John"),
 *             @OA\Property(property="last_name", type="string", example="Doe"),
 *             @OA\Property(property="email", type="string", example="john@example.com"),
 *             @OA\Property(property="password", type="string", example="password123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Voter registered successfully"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Registration failed"
 *     )
 * )
 */
Flight::route('POST /voters/register', function(){
    $data = Flight::request()->data->getData();
    try {
        $result = Flight::voterService()->registerVoter($data);
        Flight::json([
            'success' => true, 
            'message' => 'Voter registered successfully', 
            'data' => ['id' => $result]
        ], 200);
    } catch (Exception $e) {
        Flight::json([
            'success' => false, 
            'message' => $e->getMessage()
        ], 400);
    }
});

/**
 * @OA\Put(
 *     path="/voters/{id}/verify",
 *     tags={"voters"},
 *     summary="Verify voter",
 *     security={{"BearerAuth": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Voter ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Voter verified"
 *     )
 * )
 */
Flight::route('PUT /voters/@id/verify', function($id){
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    $result = Flight::voterService()->verifyVoter($id);
    Flight::json(['success' => $result, 'message' => $result ? 'Voter verified' : 'Verification failed']);
});

/**
 * @OA\Get(
 *     path="/voters/email/{email}",
 *     tags={"voters"},
 *     summary="Get voter by email",
 *     security={{"BearerAuth": {}}},
 *     @OA\Parameter(
 *         name="email",
 *         in="path",
 *         required=true,
 *         description="Voter email",
 *         @OA\Schema(type="string", example="john@example.com")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Voter details"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Voter not found"
 *     )
 * )
 */
Flight::route('GET /voters/email/@email', function($email){
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    $voter = Flight::voterService()->getByEmail($email);
    if ($voter) {
        Flight::json($voter);
    } else {
        Flight::json(['message' => 'Voter not found'], 404);
    }
});
?>
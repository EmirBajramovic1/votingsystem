<?php
/**
 * @OA\Post(
 *     path="/votes",
 *     tags={"votes"},
 *     summary="Cast a vote",
 *     security={{"BearerAuth": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"voter_id","election_id","candidate_id"},
 *             @OA\Property(property="voter_id", type="integer", example=1),
 *             @OA\Property(property="election_id", type="integer", example=1),
 *             @OA\Property(property="candidate_id", type="integer", example=2)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Vote cast successfully"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Voting error"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized"
 *     )
 * )
 */
Flight::route('POST /votes', function () {
    try {
        Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::USER]);

        $data = Flight::request()->data->getData();
        $ipAddress = Flight::request()->ip;

        Flight::voteService()->castVote(
            $data['voter_id'],
            $data['election_id'],
            $data['candidate_id'],
            $ipAddress
        );

        Flight::json([
            'success' => true,
            'message' => 'Vote submitted successfully'
        ]);
    } catch (Exception $e) {
        Flight::json([
            'success' => false,
            'message' => $e->getMessage()
        ], 400);
    }
});
?>
<?php
/**
 * @OA\Post(
 *     path="/votes",
 *     tags={"votes"},
 *     summary="Cast a vote",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"voter_id", "election_id", "candidate_id"},
 *             @OA\Property(property="voter_id", type="integer", example=1),
 *             @OA\Property(property="election_id", type="integer", example=1),
 *             @OA\Property(property="candidate_id", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Vote cast successfully"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Vote failed"
 *     )
 * )
 */
Flight::route('POST /votes', function(){
    $data = Flight::request()->data->getData();

    $ipAddress = Flight::request()->ip;
    
    try {
        $result = Flight::voteService()->castVote(
            $data['voter_id'], 
            $data['election_id'], 
            $data['candidate_id'], 
            $ipAddress
        );
        Flight::json(['success' => true, 'message' => 'Vote cast successfully']);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Get(
 *     path="/votes/check/{voterId}/{electionId}",
 *     tags={"votes"},
 *     summary="Check if voter has voted in election",
 *     @OA\Parameter(
 *         name="voterId",
 *         in="path",
 *         required=true,
 *         description="Voter ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Parameter(
 *         name="electionId",
 *         in="path",
 *         required=true,
 *         description="Election ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Voting status"
 *     )
 * )
 */
Flight::route('GET /votes/check/@voterId/@electionId', function($voterId, $electionId){
    $hasVoted = Flight::voteService()->hasVoted($voterId, $electionId);
    Flight::json(['hasVoted' => $hasVoted]);
});

/**
 * @OA\Get(
 *     path="/elections/{electionId}/results",
 *     tags={"votes"},
 *     summary="Get election results",
 *     @OA\Parameter(
 *         name="electionId",
 *         in="path",
 *         required=true,
 *         description="Election ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Election results"
 *     )
 * )
 */
Flight::route('GET /elections/@electionId/results', function($electionId){
    $results = Flight::electionCandidateService()->getResultsByElection($electionId);
    Flight::json($results);
});

/**
 * @OA\Get(
 *     path="/elections/{electionId}/votes/total",
 *     tags={"votes"},
 *     summary="Get total votes for election",
 *     @OA\Parameter(
 *         name="electionId",
 *         in="path",
 *         required=true,
 *         description="Election ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Total votes count"
 *     )
 * )
 */
Flight::route('GET /elections/@electionId/votes/total', function($electionId){
    $totalVotes = Flight::voteService()->getVotesByElection($electionId);
    Flight::json($totalVotes);
});
?>
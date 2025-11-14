<?php
require_once 'services/VoterService.php';
require_once 'services/ElectionService.php';
require_once 'services/CandidateService.php';
require_once 'services/VoteService.php';
require_once 'services/ElectionCandidateService.php';

$voterService = new VoterService();
try {
    $testVoter = $voterService->registerVoter([
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => 'test@example.com',
        'password' => 'test123'
    ]);
    echo "Voter registered successfully!\n";
} catch (Exception $e) {
    echo "Voter registration error: " . $e->getMessage() . "\n";
}

$electionService = new ElectionService();
$activeElections = $electionService->getActiveElections();
echo "Active elections: " . count($activeElections) . "\n";

$candidateService = new CandidateService();
$candidates = $candidateService->getAll();
echo "Total candidates: " . count($candidates) . "\n";
?>
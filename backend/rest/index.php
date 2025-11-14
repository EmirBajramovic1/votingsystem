<?php
require 'vendor/autoload.php';

require_once 'services/VoterService.php';
require_once 'services/ElectionService.php';
require_once 'services/CandidateService.php';
require_once 'services/VoteService.php';
require_once 'services/ElectionCandidateService.php';

Flight::register('voterService', 'VoterService');
Flight::register('electionService', 'ElectionService');
Flight::register('candidateService', 'CandidateService');
Flight::register('voteService', 'VoteService');
Flight::register('electionCandidateService', 'ElectionCandidateService');

require_once 'routes/VoterRoutes.php';
require_once 'routes/ElectionRoutes.php';
require_once 'routes/CandidateRoutes.php';
require_once 'routes/VoteRoutes.php';

Flight::route('/', function() {
    echo 'SecureVote API is running!';
});

Flight::start();
?>
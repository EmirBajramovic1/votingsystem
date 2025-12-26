<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization, Authentication");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

require 'vendor/autoload.php';
require_once 'config.php';

require_once 'services/VoterService.php';
require_once 'services/ElectionService.php';
require_once 'services/CandidateService.php';
require_once 'services/VoteService.php';
require_once 'services/ElectionCandidateService.php';
require_once 'services/AuthService.php';

require_once 'data/Roles.php';
require_once 'middleware/AuthMiddleware.php';

Flight::register('voterService', 'VoterService');
Flight::register('electionService', 'ElectionService');
Flight::register('candidateService', 'CandidateService');
Flight::register('voteService', 'VoteService');
Flight::register('electionCandidateService', 'ElectionCandidateService');
Flight::register('auth_service', 'AuthService');
Flight::register('auth_middleware', 'AuthMiddleware');

Flight::before('start', function () {

    $url = Flight::request()->url;

    if (
        strpos($url, '/auth/login') === 0 ||
        strpos($url, '/auth/register') === 0 ||
        strpos($url, '/public') === 0 ||
        $url === '/'
    ) {
        return true;
    }

    $authHeader = Flight::request()->getHeader('Authorization');

    if (!$authHeader) {
        Flight::json([
            'success' => false,
            'message' => 'Missing Authorization header'
        ], 401);
        exit;
    }

    try {
        Flight::auth_middleware()->verifyToken($authHeader);
    } catch (Exception $e) {
        Flight::json([
            'success' => false,
            'message' => 'Invalid or expired token'
        ], 401);
        exit;
    }
});

require_once 'routes/AuthRoutes.php';
require_once 'routes/VoterRoutes.php';
require_once 'routes/ElectionRoutes.php';
require_once 'routes/CandidateRoutes.php';
require_once 'routes/VoteRoutes.php';

Flight::route('/', function () {
    Flight::json([
        'status' => 'ok',
        'message' => 'SecureVote API is running'
    ]);
});

Flight::start();
?>
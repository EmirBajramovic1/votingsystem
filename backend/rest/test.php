<?php
require_once 'dao/VoterDao.php';
require_once 'dao/ElectionDao.php';
require_once 'dao/CandidateDao.php';
require_once 'dao/ElectionCandidateDao.php';
require_once 'dao/VoteDao.php';

$voterDao = new VoterDao();
$electionDao = new ElectionDao();
$candidateDao = new CandidateDao();
$electionCandidateDao = new ElectionCandidateDao();
$voteDao = new VoteDao();

$voterDao->insert([
    'first_name' => 'Emir',
    'last_name' => 'Bajramovic',
    'email' => 'eb123@example.com',
    'password' => password_hash('pass123', PASSWORD_DEFAULT),
    'is_verified' => 1
]);

$voters = $voterDao->getAll();
print_r($voters);

$activeElections = $electionDao->getActiveElections();
print_r($activeElections);


?>
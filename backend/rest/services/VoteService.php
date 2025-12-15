<?php
require_once 'BaseService.php';
require_once __DIR__ . '/../dao/VoteDao.php';
require_once __DIR__ . '/../dao/ElectionCandidateDao.php';

class VoteService extends BaseService {
    private $electionCandidateDao;
    
    public function __construct() {
        $dao = new VoteDao();
        $this->electionCandidateDao = new ElectionCandidateDao();
        parent::__construct($dao);
    }
    
    public function castVote($voterId, $electionId, $candidateId, $ipAddress) {
        if ($this->dao->hasVoted($voterId, $electionId)) {
            throw new Exception('You have already voted in this election.');
        }
        
        $voteResult = $this->dao->castVote($voterId, $electionId, $candidateId, $ipAddress);
        
        if ($voteResult) {
            $this->electionCandidateDao->addVote($electionId, $candidateId);
        }
        
        return $voteResult;
    }
    
    public function hasVoted($voterId, $electionId) {
        return $this->dao->hasVoted($voterId, $electionId);
    }
    
    public function getVotesByElection($electionId) {
        return $this->dao->getVotesByElection($electionId);
    }

    public function deleteByCandidate($candidateId) {
        return $this->dao->deleteByCandidate($candidateId);
    }
}
?>
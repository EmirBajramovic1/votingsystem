<?php
require_once 'BaseService.php';
require_once __DIR__ . '/../dao/ElectionCandidateDao.php';

class ElectionCandidateService extends BaseService {
    public function __construct() {
        $dao = new ElectionCandidateDao();
        parent::__construct($dao);
    }
    
    public function getResultsByElection($electionId) {
        return $this->dao->getResultsByElection($electionId);
    }
    
    public function addCandidateToElection($electionId, $candidateId) {
        $data = [
            'election_id' => $electionId,
            'candidate_id' => $candidateId,
            'votes_received' => 0
        ];
        return $this->create($data);
    }
    
    public function addVote($electionId, $candidateId) {
        return $this->dao->addVote($electionId, $candidateId);
    }

    public function deleteByCandidate($candidateId) {
        return $this->dao->deleteByCandidate($candidateId);
    }
}
?>
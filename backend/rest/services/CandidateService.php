<?php
require_once 'BaseService.php';
require_once __DIR__ . '/../dao/CandidateDao.php';

class CandidateService extends BaseService {
    public function __construct() {
        $dao = new CandidateDao();
        parent::__construct($dao);
    }
    
    public function getCandidatesByElection($electionId) {
        return $this->dao->getCandidatesByElection($electionId);
    }
    
    public function createCandidate($data) {
        if (empty($data['first_name']) || empty($data['last_name']) || empty($data['party'])) {
            throw new Exception('First name, last name, and party are required.');
        }
        
        return $this->create($data);
    }
}
?>
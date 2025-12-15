<?php
require_once 'BaseService.php';
require_once __DIR__ . '/../dao/ElectionDao.php';

class ElectionService extends BaseService {
    public function __construct() {
        $dao = new ElectionDao();
        parent::__construct($dao);
    }
    
    public function getActiveElections() {
        return $this->dao->getActiveElections();
    }
    
    public function getUpcomingElections() {
        return $this->dao->getUpcomingElections();
    }
    
    public function createElection($data) {
        if (strtotime($data['start_date']) >= strtotime($data['end_date'])) {
            throw new Exception('End date must be after start date.');
        }
        
        if (empty($data['title'])) {
            throw new Exception('Election title is required.');
        }
        
        return $this->create($data);
    }

    public function deleteElection($id) {
        $candidateIds = $this->dao->getCandidateIdsByElection($id);

        foreach ($candidateIds as $candidateId) {
            Flight::candidateService()->deleteCandidate($candidateId);
        }

        return $this->delete($id);
    }
}
?>
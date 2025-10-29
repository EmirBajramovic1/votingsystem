<?php
require_once 'BaseDao.php';

class CandidateDao extends BaseDao {
    public function __construct() {
        parent::__construct("candidates");
    }

    public function getCandidatesByElection($electionId) {
        $stmt = $this->connection->prepare("
            SELECT c.*, ec.votes_received 
            FROM candidates c 
            JOIN election_candidates ec ON c.id = ec.candidate_id 
            WHERE ec.election_id = :election_id
        ");
        $stmt->bindParam(':election_id', $electionId);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>
<?php
require_once 'BaseDao.php';

class ElectionCandidateDao extends BaseDao {
    public function __construct() {
        parent::__construct("election_candidates");
    }

    public function addVote($electionId, $candidateId) {
        $stmt = $this->connection->prepare("
            UPDATE election_candidates 
            SET votes_received = votes_received + 1 
            WHERE election_id = :election_id AND candidate_id = :candidate_id
        ");
        $stmt->bindParam(':election_id', $electionId);
        $stmt->bindParam(':candidate_id', $candidateId);
        return $stmt->execute();
    }

    public function getResultsByElection($electionId) {
        $stmt = $this->connection->prepare("
            SELECT c.first_name, c.last_name, c.party, ec.votes_received 
            FROM election_candidates ec 
            JOIN candidates c ON ec.candidate_id = c.id 
            WHERE ec.election_id = :election_id 
            ORDER BY ec.votes_received DESC
        ");
        $stmt->bindParam(':election_id', $electionId);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>
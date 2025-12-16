<?php
require_once 'BaseDao.php';

class VoteDao extends BaseDao {
    public function __construct() {
        parent::__construct("votes");
    }

    public function hasVoted($voterId, $electionId) {
        $stmt = $this->connection->prepare("
            SELECT COUNT(*) as count 
            FROM votes 
            WHERE voter_id = :voter_id AND election_id = :election_id
        ");
        $stmt->bindParam(':voter_id', $voterId);
        $stmt->bindParam(':election_id', $electionId);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }

    public function castVote($voterId, $electionId, $candidateId, $ipAddress) {
        $data = [
            'voter_id' => $voterId,
            'election_id' => $electionId,
            'candidate_id' => $candidateId,
            'ip_address' => $ipAddress,
            'voted_at' => date('Y-m-d H:i:s')
        ];
        return $this->insert($data);
    }

    public function getVotesByElection($electionId) {
        $stmt = $this->connection->prepare("
            SELECT COUNT(*) as total_votes 
            FROM votes 
            WHERE election_id = :election_id
        ");
        $stmt->bindParam(':election_id', $electionId);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function deleteByCandidate($candidateId) {
        $stmt = $this->connection->prepare(
            "DELETE FROM votes WHERE candidate_id = :id"
        );
        return $stmt->execute(['id' => $candidateId]);
    }
}
?>
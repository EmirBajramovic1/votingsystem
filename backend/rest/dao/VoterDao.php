<?php
require_once 'BaseDao.php';

class VoterDao extends BaseDao {
    public function __construct() {
        parent::__construct("voters");
    }

    public function getByEmail($email) {
        $stmt = $this->connection->prepare("SELECT * FROM voters WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function verifyVoter($voterId) {
        $stmt = $this->connection->prepare("UPDATE voters SET is_verified = 1 WHERE id = :id");
        $stmt->bindParam(':id', $voterId);
        return $stmt->execute();
    }
}
?>
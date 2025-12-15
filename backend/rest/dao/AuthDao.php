<?php
require_once 'BaseDao.php';

class AuthDao extends BaseDao {

    public function __construct() {
        parent::__construct('voters');
    }

    public function getUserByEmail($email) {
        $stmt = $this->connection->prepare("SELECT * FROM voters WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch();
    }
}
?>
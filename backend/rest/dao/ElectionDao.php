<?php
require_once 'BaseDao.php';

class ElectionDao extends BaseDao {
    public function __construct() {
        parent::__construct("elections");
    }

    public function getActiveElections() {
        $stmt = $this->connection->prepare("SELECT * FROM elections WHERE start_date <= NOW() AND end_date >= NOW()");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getUpcomingElections() {
        $stmt = $this->connection->prepare("SELECT * FROM elections WHERE start_date > NOW()");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>
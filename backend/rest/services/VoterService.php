<?php
require_once 'BaseService.php';
require_once __DIR__ . '/../dao/VoterDao.php';

class VoterService extends BaseService {
    public function __construct() {
        $dao = new VoterDao();
        parent::__construct($dao);
    }
    
    public function registerVoter($data) {
        if (empty($data['first_name']) || empty($data['last_name']) || empty($data['email']) || empty($data['password'])) {
            throw new Exception('All fields are required: first_name, last_name, email, password.');
        }
        
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email format.');
        }
        
        $existingVoter = $this->dao->getByEmail($data['email']);
        if ($existingVoter) {
            throw new Exception('Email already registered.');
        }
        
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $data['is_verified'] = 0;
        
        return $this->create($data);
    }
    
    public function verifyVoter($voterId) {
        return $this->dao->verifyVoter($voterId);
    }
    
    public function getByEmail($email) {
        return $this->dao->getByEmail($email);
    }
}
?>
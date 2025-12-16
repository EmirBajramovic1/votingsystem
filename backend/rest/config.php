<?php
class Config {
    public static function JWT_SECRET() {
        return 'securevote_local_secret_2025';
    }

    public static function DB_HOST() {
        return 'localhost';
    }

    public static function DB_NAME() {
        return 'securevote_db';
    }

    public static function DB_USER() {
        return 'root';
    }

    public static function DB_PASSWORD() {
        return '';
    }
}

class Database {
    private static $connection = null;

    public static function connect() {
        if (self::$connection === null) {
            self::$connection = new PDO(
                "mysql:host=" . Config::DB_HOST() . ";dbname=" . Config::DB_NAME(),
                Config::DB_USER(),
                Config::DB_PASSWORD(),
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        }
        return self::$connection;
    }
}
?>
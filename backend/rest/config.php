<?php

class Config {
    public static function is_local() {
        return in_array($_SERVER['SERVER_NAME'], ['localhost', '127.0.0.1']);
    }

    public static function DB_HOST() {
        return self::is_local() ? "127.0.0.1" : "db-mysql-fra1-86197-do-user-30780613-0.h.db.ondigitalocean.com";
    }

    public static function DB_NAME() {
        return "securevote_db";
    }

    public static function DB_USER() {
        return self::is_local() ? "root" : "doadmin";
    }

    public static function DB_PASSWORD() {
        return self::is_local() ? "" : "PASSWORD";
    }

    public static function DB_PORT() {
        return self::is_local() ? "3306" : "25060";
    }

    public static function JWT_SECRET() {
        return "securevote_local_secret_2025";
    }
}

class Database {
    private static $connection = null;

    public static function connect() {
        if (self::$connection === null) {

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ];

            if (!Config::is_local()) {
                $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
            }

            self::$connection = new PDO(
                "mysql:host=" . Config::DB_HOST() .
                ";port=" . Config::DB_PORT() .
                ";dbname=" . Config::DB_NAME(),
                Config::DB_USER(),
                Config::DB_PASSWORD(),
                $options
            );
        }
        return self::$connection;
    }
}
?>
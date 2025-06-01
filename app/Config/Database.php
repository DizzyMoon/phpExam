<?php
namespace App\Config;

use PDO;
use PDOException;

class Database {
  private static $host = "localhost";
  private static $db_name = "Chinook";
  private static $username = "root";
  private static $password = "";
  private static $conn = null;

  public static function getConnection(): PDO {
    if (self::$conn === null) {
      try {
        self::$conn = new PDO("mysql:host=" . self::$host . ";dbname=" . self::$db_name, self::$username, self::$password);
        self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      } catch (PDOException $e) {
        die("Database connection error: " . $e->getMessage());
      }
    }
    return self::$conn;
  }
}
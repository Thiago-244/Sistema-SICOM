<?php

namespace App\Models;

use App\Database;

class Dependencia {
    public static function all(): array {
        $db = Database::getInstance();
        return $db->query("SELECT * FROM dependencias WHERE estado = 'Activo' ORDER BY nombre ASC")->fetchAll();
    }
}

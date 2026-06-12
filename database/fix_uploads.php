<?php
require_once __DIR__ . '/../app/autoload.php';
use App\Core\Database;
try {
    Database::execute("UPDATE usuarios SET profile_photo = CONCAT('profiles/', profile_photo) WHERE profile_photo IS NOT NULL AND profile_photo != '' AND profile_photo NOT LIKE '%/%'");
    echo 'Profiles: ' . Database::execute("UPDATE cursos SET image_url = CONCAT('courses/', image_url) WHERE image_url IS NOT NULL AND image_url != '' AND image_url NOT LIKE '%/%'") . " courses updated\n";
    echo 'Payments: ' . Database::execute("UPDATE pagos SET proof_image_url = CONCAT('payments/', proof_image_url) WHERE proof_image_url IS NOT NULL AND proof_image_url != '' AND proof_image_url NOT LIKE '%/%'") . " payment proofs updated\n";
    echo 'Movements: ' . Database::execute("UPDATE movimientos SET soporte_url = CONCAT('documents/', soporte_url) WHERE soporte_url IS NOT NULL AND soporte_url != '' AND soporte_url NOT LIKE '%/%'") . " movement docs updated\n";
    echo "Done.\n";
} catch (Exception $e) {
    echo 'ERROR: ' . $e->getMessage() . "\n";
}

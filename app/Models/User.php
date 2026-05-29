<?php
namespace App\Models;
use App\Core\Model;
class User extends Model
{
    protected static $table = 'usuarios';
    public static function create($data)
    {
        $data['password_hash'] = \App\Helpers\Security::hashPassword($data['password']);
        unset($data['password']);
        $cols = implode(',', array_keys($data));
        $vals = implode(',', array_fill(0, count($data), '?'));
        return \App\Core\Database::insert("INSERT INTO usuarios ({$cols}) VALUES ({$vals})", array_values($data));
    }
    public static function updateUser($id, $data)
    {
        if (!empty($data['password'])) {
            $data['password_hash'] = \App\Helpers\Security::hashPassword($data['password']);
        }
        unset($data['password'], $data['id']);
        if (empty($data['password_hash'])) {
            unset($data['password_hash']);
        }
        $sets = implode('=?, ', array_keys($data)) . '=?';
        \App\Core\Database::execute("UPDATE usuarios SET {$sets} WHERE id=?", array_merge(array_values($data), [$id]));
    }
    public static function findByUsername($username)
    {
        return \App\Core\Database::fetchOne("SELECT * FROM usuarios WHERE username=?", [$username]);
    }
    public static function findByEmail($email)
    {
        return \App\Core\Database::fetchOne("SELECT * FROM usuarios WHERE email=?", [$email]);
    }
}

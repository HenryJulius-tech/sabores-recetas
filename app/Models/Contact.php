<?php
namespace App\Models;
use App\Core\Model;
class Contact extends Model
{
    protected static $table = 'contactos';
    public static function create($data)
    {
        $cols = implode(',', array_keys($data));
        $vals = implode(',', array_fill(0, count($data), '?'));
        return \App\Core\Database::insert("INSERT INTO contactos ({$cols}) VALUES ({$vals})", array_values($data));
    }
}

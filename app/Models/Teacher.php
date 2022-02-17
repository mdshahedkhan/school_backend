<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = ['create_by', 'name', 'gender', 'religion', 'phone', 'email', 'uu_id','address', 'date_of_birth', 'join_date', 'photo', 'username', 'password', 'status'];

    protected function status(): Attribute
    {
        return new Attribute(
            get: fn($value) => ucfirst($value), set: fn($value) => strtolower($value)
        );
    }
}

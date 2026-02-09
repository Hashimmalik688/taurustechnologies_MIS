<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'contact_info',
        'emergency_contact',
        'cnic',
        'position',
        'area_of_residence',
        'status',
        'mis',
        'passport_image',
    ];
}

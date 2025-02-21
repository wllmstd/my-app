<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class UserRequest extends Model
{
    use HasFactory;

    protected $table = 'requests';
    protected $primaryKey = 'Request_ID';
    public $timestamps = false; // Since we manually handle timestamps

    protected $fillable = [
        'Status',
        'First_Name',
        'Last_Name',
        'Nationality',
        'Location',
        'Format',
        'Attachment',
        'Date_Created',
        'Updated_Time',
        'Users_ID',
        'Accepted_By', // New column
    ];    

    // Relationship: Each request belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class, 'Users_ID');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'Users_ID');
    }

    // Relationship: The user who accepted the request
    public function accepter()
    {
        return $this->belongsTo(User::class, 'Accepted_By');
    }
}

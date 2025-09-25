<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = ['name', 'email', 'contact_subject_id', 'message','is_read'];

    public function subject()
    {
        return $this->belongsTo(ContactSubject::class, 'contact_subject_id');
    }
}

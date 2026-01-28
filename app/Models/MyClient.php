<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MyClient extends Model
{
    use SoftDeletes;

    protected $table = 'my_client';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name',
        'slug',
        'is_project',
        'self_capture',
        'client_prefix',
        'client_logo',
        'address',
        'phone_number',
        'city',
    ];

    public $timestamps = true;

    public function setSlugAttribute($value): void
    {
        $this->attributes['slug'] = trim((string)$value);
    }

    public function getSlugAttribute($value): string
    {
        return rtrim((string)$value);
    }

    public function setNameAttribute($value): void
    {
        $this->attributes['name'] = trim((string)$value);
    }

    public function getNameAttribute($value): string
    {
        return rtrim((string)$value);
    }

    public function getClientPrefixAttribute($value): string
    {
        return rtrim((string)$value);
    }

    public function getCityAttribute($value): ?string
    {
        return $value === null ? null : rtrim((string)$value);
    }

    public function getPhoneNumberAttribute($value): ?string
    {
        return $value === null ? null : rtrim((string)$value);
    }

    public function getClientLogoAttribute($value): string
    {
        return rtrim((string)$value);
    }
}
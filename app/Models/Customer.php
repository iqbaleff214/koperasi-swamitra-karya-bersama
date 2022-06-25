<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function collaterals()
    {
        return $this->hasMany(Collateral::class);
    }

    public function foreclosures()
    {
        return $this->hasMany(Foreclosure::class);
    }

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }
}

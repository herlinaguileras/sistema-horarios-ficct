<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Semestre extends Model
{
   /**
     * Un semestre tiene muchos Grupos.
     */
    public function grupos()
    {
        return $this->hasMany(Grupo::class);
    }
    //
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\GenProfesional;
use App\Models\Paciente;
use Carbon\Carbon;
use Debugbar;

class AmFichas extends Model
{
    use HasFactory;
    // esquema de conexion
    protected $connection = 'SINERGIA';

    // nombre de la tabla
    protected $table = 'AGEINC.AM_FICHAS';

    // definiendo que esta sera la clave primaria.
    protected $primaryKey = 'id_ambulatorio';

    public $timestamps = false;

    // relaciones

    // funciones
}

<?php

namespace Modules\Primavera\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PrimaveraClientsContacts extends Model
{
    use HasFactory;

    protected $table = 'primavera_clients_contacts';
    protected $primaryKey = 'id';

    protected $fillable = [
        'primavera_id',
        'cod_postal',
        'cod_postal_local',
        'contacto',
        'localidade',
        'morada',
        'pais',
        'telefone',
        'telefone_2',
        'telemovel',
        'email',
        'primeiro_nome',
        'ultimo_nome',
        'zona',
        'descricao_zona',
    ];
}

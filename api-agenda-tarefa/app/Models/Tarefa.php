<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tarefa extends Model
{
    protected $table = 'tarefas';

    protected $fillable = ['idusuario', 'idcategoria', 
    'descricao', 'data', 'cumprida'];

    protected $primaryKey = 'idtarefa';

    public function user(){
        return $this->belongsTo(User::class, 
            'idusuario', 'id');
    }

    public function categoria(){
        return $this->belongsTo(Categoria::class, 
            'idcategoria', 'idcategoria');
    }    
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccessToken extends Model{

	protected $table = 'tb_access_token';
    protected $primaryKey = 'ID';
}
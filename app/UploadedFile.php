<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UploadedFile extends Model{

	protected $table = 'tb_files';
    protected $primaryKey = 'ID';
}
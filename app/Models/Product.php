<?php

namespace App\Models;

use App\Models\Traits\CreateLog;
use App\Models\Traits\TableName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use CreateLog, TableName;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = [
	    'id','title','description','price','image'
    ];

    /**
     * @param string $ext
     * @return string
     */
    public static function getUniqueImageName($ext = 'jpg') {
	    $name = uniqid();
	    if(static::where('image','%LIKE%',$name)->exists()) {
	       self::getUniqueImageName($ext);
        }
        return Str::lower($name.'.'.$ext);
    }
}

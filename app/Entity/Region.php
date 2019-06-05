<?php

namespace App\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Region extends Model
{
    protected $fillable=['name','slug','parent_id','id'];




    public function parent()
    {
        return$this->belongsTo(static::class,'parent_id','id');
    }
    public function children()
    {
        return $this->hasMany(static::class,'parent_id','id');
    }

    public function getPath(): string
    {
        return ($this->parent ? $this->parent->getPath() . '/' : '') . $this->slug;
    }

    public function getAddress():string
    {
        return($this->parent ? $this->parent->getAddress(). ',' :'')  .$this->name;
    }
    public function scopeRoots(Builder $query)
    {
        return $query->where('parent_id',null);
    }

}

<?php

namespace App\Entity\Adverts;

use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;

class Category extends Model
{
   use NodeTrait;
   protected $table='advert_categories';
   public $timestamps=false;
   protected $fillable=['name','slug','parent_id'];

    public function attributes()
    {
        return $this->hasMany(Attribute::class,'category_id','id');
    }
    public function parentAttributes()
    {
        return $this->parent ? $this->parent->allAttributes() : [];
    }
    public function allAttributes()
    {
        return array_merge($this->parentAttributes(), $this->attributes()->orderBy('sort')->getModels());
    }
    public function getPath(): string
    {
        return implode('/', array_merge($this->ancestors()->defaultOrder()->pluck('slug')->toArray(), [$this->slug]));
    }


}

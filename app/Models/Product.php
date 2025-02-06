<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\FindsModelOrFail;

class Product extends Model
{
    use HasFactory, FindsModelOrFail;

    /**
     *  Los atributos que son asignables en masa.
     *  @var array<string>
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'image',
    ];

    /**
     *  Los atributos que son casteados a tipos nativos.
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
    ];

    /**
     *  Obtiene la URL de la imagen del producto.
     */
    public function getImageUrlAttribute(): string
    {
        return asset('storage/' . $this->image);
    }
}

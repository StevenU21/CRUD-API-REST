<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

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
    public function image(): string
    {
        if ($this->image) {
            return asset('storage/products/' . $this->image);
        } else {
            return 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRwm0rdbOAslibv0mLIxWKZ6C6r9m8fujTIBA&s';
        }
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ZipCode extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'zip_codes';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'd_codigo',
        'd_asenta',
        'd_tipo_asenta',
        'd_mnpio',
        'd_estado',
        'd_ciudad',
        'd_cp',
        'c_estado',
        'c_oficina',
        'c_cp',
        'c_tipo_asenta',
        'c_mnpio',
        'id_asenta_cpcons',
        'd_zona',
        'c_cve_ciudad',
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

     /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'c_estado' => 'int',
        'c_mnpio' => 'int',
        'id_asenta_cpcons' => 'int',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array<int, string>
     */
    protected $dates = [];

    /**
     * d_estado mutator/accesor
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function dEstado(): Attribute
    {
        return Attribute::make(
            fn () => Str::upper(Str::ascii($this->attributes['d_estado'])),
        )->shouldCache();
    }

    /**
     * d_mnpio mutator/accessor
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function dMnpio(): Attribute
    {
        return Attribute::make(
            fn () => Str::upper(Str::ascii($this->attributes['d_mnpio'])),
        )->shouldCache();
    }

    /**
     * d_assenta mutator/accessor
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function dAsenta(): Attribute
    {
        return Attribute::make(
            fn () => Str::upper(Str::ascii($this->attributes['d_asenta'])),
        )->shouldCache();
    }

    /**
     * d_zona mutator/accessor
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function dZona(): Attribute
    {
        return Attribute::make(
            fn () => Str::upper($this->attributes['d_zona']),
        )->shouldCache();
    }

    /**
     * d_ciudad mutator/accessor
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function dCiudad(): Attribute
    {
        return Attribute::make(
            fn () => Str::upper(Str::ascii($this->attributes['d_ciudad'])),
        )->shouldCache();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = [
        'divisi',
        'material_sap',
        'material_description',
        'base_unit_measure',
        'status',
        'lokasi_sistem',
        'lokasi_fisik',
        'store_room',
        'penempatan_alat',
        'deskripsi_penempatan',
        'photo',
        'qty'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Scopes for filtering
    public function scopeByDivisi($query, $divisi)
    {
        if ($divisi === 'others') {
            return $query->whereNotIn('divisi', ['RTG', 'ME', 'CC']);
        }
        return $query->where('divisi', $divisi);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPenempatan($query, $penempatan)
    {
        return $query->where('penempatan_alat', $penempatan);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('material_sap', 'LIKE', "%{$search}%")
              ->orWhere('material_description', 'LIKE', "%{$search}%");
        });
    }
}

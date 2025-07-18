<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Material;
use Illuminate\Support\Facades\DB;

class MaterialController extends Controller
{
    public function index()
    {
        return view('dashboard.dashboard');
    }



    public function getMaterials(Request $request)
    {
        $query = Material::query();

        // Filter based on request parameters
        if ($request->get('divisi')) {
            $divisi = strtoupper($request->get('divisi'));
            if ($divisi === 'OTHERS' || $divisi === 'LAIN') {
                $query->whereNotIn('divisi', ['RTG', 'ME', 'CC']);
            } else {
                $query->where('divisi', $divisi);
            }
        }

        if ($request->get('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->get('penempatan') && $request->get('penempatan') !== 'NULL') {
            $query->where('penempatan_alat', $request->get('penempatan'));
        }

        if ($request->get('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('material_description', 'LIKE', "%{$search}%")
                  ->orWhere('material_sap', 'LIKE', "%{$search}%");
            });
        }

        // Pagination
        $page = $request->get('page', 1);
        $perPage = 20;
        $total = $query->count();
        
        $materials = $query->select([
            'id', 'divisi', 'material_sap', 'material_description', 
            'base_unit_measure', 'status', 'lokasi_sistem', 
            'lokasi_fisik', 'penempatan_alat', 'photo'
        ])->offset(($page - 1) * $perPage)
                          ->limit($perPage)
                          ->get();

        return response()->json([
            'data' => $materials,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage)
        ]);
    }

    public function getStats()
    {
        // Total materials
        $totalMaterials = Material::count();

        // Division stats
        $divisiStats = Material::select('divisi')
            ->selectRaw('count(*) as count')
            ->groupBy('divisi')
            ->pluck('count', 'divisi')
            ->toArray();

        // Status stats
        $statusStats = Material::select('status')
            ->selectRaw('count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Penempatan stats (only for RTG)
        $penempatanStats = Material::where('divisi', 'RTG')
            ->whereNotNull('penempatan_alat')
            ->where('penempatan_alat', '!=', 'NULL')
            ->select('penempatan_alat')
            ->selectRaw('count(*) as count')
            ->groupBy('penempatan_alat')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->pluck('count', 'penempatan_alat')
            ->toArray();

        // Get last update time (most recent material update)
        $lastUpdate = Material::max('updated_at');

        return response()->json([
            'total_materials' => $totalMaterials,
            'divisi_stats' => $divisiStats,
            'status_stats' => $statusStats,
            'penempatan_stats' => $penempatanStats,
            'last_update' => $lastUpdate
        ]);
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Material;
use Illuminate\Support\Facades\DB;

class FixMaterialsData extends Command
{
    protected $signature = 'materials:fix';
    protected $description = 'Fix material data by removing duplicates and ensuring data consistency';

    public function handle()
    {
        $this->info('Starting material data fix...');

        // Begin transaction
        DB::beginTransaction();

        try {
            // 1. Remove duplicates based on material_sap
            $this->info('Checking for duplicates...');
            $duplicates = DB::table('materials')
                ->select('material_sap')
                ->groupBy('material_sap')
                ->havingRaw('COUNT(*) > 1')
                ->get();

            foreach ($duplicates as $duplicate) {
                // Keep the most recent entry and delete others
                $materials = Material::where('material_sap', $duplicate->material_sap)
                    ->orderBy('created_at', 'desc')
                    ->get();
                
                // Skip the first one (most recent)
                $materials->slice(1)->each->delete();
            }

            $this->info('Removed ' . count($duplicates) . ' duplicate entries');

            // 2. Standardize division values
            $this->info('Standardizing division values...');
            DB::table('materials')
                ->whereNotIn('divisi', ['RTG', 'ME', 'CC'])
                ->update(['divisi' => 'LAIN']);

            // 3. Ensure all material_sap values are properly formatted
            $this->info('Formatting material_sap values...');
            $materials = Material::all();
            foreach ($materials as $material) {
                $material->material_sap = trim($material->material_sap);
                $material->save();
            }

            DB::commit();
            $this->info('Data fix completed successfully!');
            
            // Show final counts
            $totalCount = Material::count();
            $divisionCounts = Material::select('divisi', DB::raw('count(*) as count'))
                ->groupBy('divisi')
                ->get();
            
            $this->info("\nFinal counts:");
            $this->info("Total materials: " . $totalCount);
            foreach ($divisionCounts as $count) {
                $this->info($count->divisi . ": " . $count->count);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error fixing data: ' . $e->getMessage());
            return 1;
        }
    }
}

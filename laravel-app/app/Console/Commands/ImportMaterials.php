<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\Material;
use Illuminate\Support\Facades\DB;

class ImportMaterials extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'materials:import {--fresh : Truncate table before import}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import materials from CSV file to database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting material import...');

        // Check if fresh import is requested
        if ($this->option('fresh')) {
            $this->info('Truncating materials table...');
            Material::truncate();
        }

        // Read CSV file
        $csvPath = '/Users/macos/Documents/UNIV/Magang/TPS/Dashboard/Dataset.csv';
        
        if (!file_exists($csvPath)) {
            $this->error("CSV file not found: {$csvPath}");
            return 1;
        }

        $csvFile = file_get_contents($csvPath);
        if (!$csvFile) {
            $this->error("Could not read CSV file: {$csvPath}");
            return 1;
        }

        $lines = explode("\n", $csvFile);
        $header = str_getcsv(array_shift($lines));

        $this->info('CSV Header: ' . implode(', ', $header));

        $successCount = 0;
        $errorCount = 0;
        $batchSize = 500;
        $batch = [];

        $bar = $this->output->createProgressBar(count($lines));
        $bar->start();

        foreach ($lines as $lineNumber => $line) {
            $bar->advance();
            
            if (trim($line) === '') {
                continue;
            }

            $data = str_getcsv($line);
            
            if (count($data) < count($header)) {
                $errorCount++;
                continue;
            }

            $materialData = array_combine($header, $data);

            // Map CSV columns to database columns
            $mappedData = [
                'divisi' => trim($materialData['DIVISI'] ?? 'Lain'),  // Trim whitespace
                'material_sap' => trim($materialData['Material SAP'] ?? ''),
                'material_description' => trim($materialData['Material Description Maximo'] ?? ''),
                'base_unit_measure' => trim($materialData['Base Unit of Measure'] ?? 'PC'),
                'status' => strtoupper(trim($materialData['Status'] ?? '')) === 'ACTIVE' ? 'ACTIVE' : 'INACTIVE',
                'lokasi_sistem' => !empty(trim($materialData['Lokasi Sistem'] ?? '')) ? trim($materialData['Lokasi Sistem']) : null,
                'lokasi_fisik' => !empty(trim($materialData['Lokasi Fisik'] ?? '')) ? trim($materialData['Lokasi Fisik']) : null,
                'store_room' => !empty(trim($materialData['StoreRoom'] ?? '')) ? trim($materialData['StoreRoom']) : null,
                'penempatan_alat' => (trim($materialData['Penempatan pada Alat'] ?? 'NULL') === 'NULL') ? null : trim($materialData['Penempatan pada Alat']),
                'deskripsi_penempatan' => (trim($materialData['Deskripsi Penempatan'] ?? 'No Placement Information Available') === 'No Placement Information Available') ? null : trim($materialData['Deskripsi Penempatan']),
                'created_at' => now(),
                'updated_at' => now()
            ];

            // Skip if material_sap is empty
            if (empty($mappedData['material_sap'])) {
                $errorCount++;
                continue;
            }

            $batch[] = $mappedData;

            // Insert batch when it reaches the batch size
            if (count($batch) >= $batchSize) {
                try {
                    Material::insert($batch);
                    $successCount += count($batch);
                    $batch = [];
                } catch (\Exception $e) {
                    $this->error("Error inserting batch: " . $e->getMessage());
                    $errorCount += count($batch);
                    $batch = [];
                }
            }
        }

        // Insert remaining batch
        if (!empty($batch)) {
            try {
                Material::insert($batch);
                $successCount += count($batch);
            } catch (\Exception $e) {
                $this->error("Error inserting final batch: " . $e->getMessage());
                $errorCount += count($batch);
            }
        }

        $bar->finish();
        $this->newLine();

        $this->info("Import completed!");
        $this->info("Successfully imported: {$successCount} materials");
        $this->info("Errors: {$errorCount}");

        // Show some statistics
        $this->info("\nDatabase Statistics:");
        $this->info("Total materials: " . Material::count());
        $this->info("RTG materials: " . Material::where('divisi', 'RTG')->count());
        $this->info("ME materials: " . Material::where('divisi', 'ME')->count());
        $this->info("Active materials: " . Material::where('status', 'ACTIVE')->count());

        return 0;
    }
}

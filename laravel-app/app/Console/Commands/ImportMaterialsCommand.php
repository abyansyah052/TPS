<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Material;
use Illuminate\Support\Facades\DB;

class ImportMaterialsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'materials:import';

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
        $filePath = '/Users/macos/Documents/UNIV/Magang/TPS/Dashboard/Dataset.csv';
        
        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        $this->info("Starting import from: {$filePath}");
        
        // Open CSV file
        $file = fopen($filePath, 'r');
        if (!$file) {
            $this->error("Could not open file: {$filePath}");
            return 1;
        }

        // Read header
        $header = fgetcsv($file);
        if (!$header) {
            $this->error("Could not read CSV header");
            fclose($file);
            return 1;
        }

        $this->info("CSV Header: " . implode(', ', $header));
        
        // Clear existing data
        $this->info("Clearing existing materials...");
        Material::truncate();

        $imported = 0;
        $errors = 0;
        $batchSize = 500;
        $batch = [];

        $progressBar = $this->output->createProgressBar();
        $progressBar->start();

        // Process each row
        while (($row = fgetcsv($file)) !== false) {
            if (count($row) < count($header)) {
                $errors++;
                continue;
            }

            $data = array_combine($header, $row);
            
            // Clean and map data
            $materialData = [
                'divisi' => $data['DIVISI'] ?? '',
                'material_sap' => $data['Material SAP'] ?? '',
                'material_description' => $data['Material Description Maximo'] ?? '',
                'base_unit_measure' => $data['Base Unit of Measure'] ?? '',
                'status' => $this->normalizeStatus($data['Status'] ?? ''),
                'lokasi_sistem' => $this->nullIfEmpty($data['Lokasi Sistem'] ?? ''),
                'lokasi_fisik' => $this->nullIfEmpty($data['Lokasi Fisik'] ?? ''),
                'store_room' => $this->nullIfEmpty($data['StoreRoom'] ?? ''),
                'penempatan_alat' => $this->nullIfEmpty($data['Penempatan pada Alat'] ?? ''),
                'deskripsi_penempatan' => $this->nullIfEmpty($data['Deskripsi Penempatan'] ?? ''),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Skip if material_sap is empty
            if (empty($materialData['material_sap'])) {
                $errors++;
                continue;
            }

            $batch[] = $materialData;

            // Insert batch when it reaches batch size
            if (count($batch) >= $batchSize) {
                $this->insertBatch($batch);
                $imported += count($batch);
                $batch = [];
                $progressBar->advance(count($batch));
            }
        }

        // Insert remaining batch
        if (!empty($batch)) {
            $this->insertBatch($batch);
            $imported += count($batch);
        }

        $progressBar->finish();
        $this->newLine();

        fclose($file);

        $this->info("Import completed!");
        $this->info("Total imported: {$imported}");
        $this->info("Total errors: {$errors}");

        return 0;
    }

    private function insertBatch($batch)
    {
        try {
            DB::table('materials')->insert($batch);
        } catch (\Exception $e) {
            $this->error("Error inserting batch: " . $e->getMessage());
            
            // Try inserting individually to identify problem records
            foreach ($batch as $item) {
                try {
                    DB::table('materials')->insert($item);
                } catch (\Exception $e2) {
                    $this->error("Error inserting material: " . $item['material_sap'] . " - " . $e2->getMessage());
                }
            }
        }
    }

    private function normalizeStatus($status)
    {
        $status = strtoupper(trim($status));
        return in_array($status, ['ACTIVE', 'INACTIVE']) ? $status : 'ACTIVE';
    }

    private function nullIfEmpty($value)
    {
        $value = trim($value);
        return (empty($value) || $value === 'NULL') ? null : $value;
    }
}

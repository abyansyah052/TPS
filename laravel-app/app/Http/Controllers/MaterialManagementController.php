<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class MaterialManagementController extends Controller
{
    public function index()
    {
        return view('management.index');
    }

    public function getMaterials(Request $request)
    {
        // Handle placement options request
        if ($request->get('get_placements')) {
            $placements = Material::distinct()->pluck('penempatan_alat')->filter()->sort()->values();
            return response()->json(['placements' => $placements]);
        }

        $query = Material::query();

        // Apply filters similar to MaterialController
        if ($request->get('divisi')) {
            if ($request->get('divisi') === 'Lain') {
                $query->whereNotIn('divisi', ['RTG', 'ME', 'CC']);
            } else {
                $query->where('divisi', $request->get('divisi'));
            }
        }

        if ($request->get('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->get('penempatan') && $request->get('penempatan') !== 'NULL') {
            $query->where('penempatan_alat', $request->get('penempatan'));
        }

        if ($request->get('search')) {
            $search = trim($request->get('search'));
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('material_description', 'LIKE', "%{$search}%")
                      ->orWhere('material_sap', 'LIKE', "%{$search}%");
                });
            }
        }

        // Pagination
        $page = $request->get('page', 1);
        $perPage = 20;
        $total = $query->count();
        
        $materials = $query->select([
            'id', 'divisi', 'material_sap', 'material_description', 
            'base_unit_measure', 'status', 'lokasi_sistem', 
            'lokasi_fisik', 'penempatan_alat', 'photo', 'qty'
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

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers - all columns except photo
        $headers = [
            'A1' => 'ID',
            'B1' => 'Division',
            'C1' => 'Material SAP',
            'D1' => 'Description',
            'E1' => 'Unit',
            'F1' => 'Status',
            'G1' => 'System Location',
            'H1' => 'Physical Location',
            'I1' => 'Placement',
            'J1' => 'Quantity'
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
            $sheet->getStyle($cell)->getFont()->setBold(true);
            $sheet->getStyle($cell)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FF4472C4');
            $sheet->getStyle($cell)->getFont()->getColor()->setARGB('FFFFFFFF');
        }

        // Add example row in orange
        $sheet->setCellValue('A2', '(Example)');
        $sheet->setCellValue('B2', 'ME');
        $sheet->setCellValue('C2', '11-100-001739');
        $sheet->setCellValue('D2', 'DIODE 1911-59008');
        $sheet->setCellValue('E2', 'UN');
        $sheet->setCellValue('F2', 'ACTIVE');
        $sheet->setCellValue('G2', 'II 5');
        $sheet->setCellValue('H2', 'II 5');
        $sheet->setCellValue('I2', 'ELECTRICAL_SYSTEM');
        $sheet->setCellValue('J2', '10');

        // Style example row with orange background
        $sheet->getStyle('A2:J2')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFF8C00');

        // Auto-size columns
        foreach (range('A', 'J') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'material_template_' . date('Y-m-d_H-i-s') . '.xlsx';

        // Return as download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }
    
    private function createNewTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = [
            'A1' => 'ID',
            'B1' => 'Division',
            'C1' => 'Material SAP',
            'D1' => 'Description',
            'E1' => 'Unit',
            'F1' => 'Status',
            'G1' => 'System Location',
            'H1' => 'Physical Location',
            'I1' => 'Placement',
            'J1' => 'Photo URL',
            'K1' => 'Quantity'
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Get all materials
        $materials = Material::all();
        $row = 2;

        foreach ($materials as $material) {
            $sheet->setCellValue('A' . $row, $material->id);
            $sheet->setCellValue('B' . $row, $material->divisi);
            $sheet->setCellValue('C' . $row, $material->material_sap);
            $sheet->setCellValue('D' . $row, $material->material_description);
            $sheet->setCellValue('E' . $row, $material->base_unit_measure);
            $sheet->setCellValue('F' . $row, $material->status);
            $sheet->setCellValue('G' . $row, $material->lokasi_sistem);
            $sheet->setCellValue('H' . $row, $material->lokasi_fisik);
            $sheet->setCellValue('I' . $row, $material->penempatan_alat);
            $sheet->setCellValue('J' . $row, $material->photo);
            $sheet->setCellValue('K' . $row, $material->qty);
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'K') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'material_template_' . date('Y-m-d_H-i-s') . '.xlsx';

        // Return as download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    public function uploadData(Request $request)
    {
        // File validation is handled by FileUploadSecurity middleware
        // Additional server-side validation
        if (!$request->hasFile('file')) {
            return response()->json([
                'success' => false,
                'message' => 'No file uploaded'
            ], 400);
        }

        try {
            $file = $request->file('file');
            
            // Log upload attempt
            \Log::info('File upload processing started', [
                'filename' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'ip' => $request->ip(),
                'timestamp' => now()
            ]);
            
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            // Skip header row (row 1) and orange example row (row 2)
            // Start processing from row 3 (index 2)
            $dataRows = array_slice($rows, 2);

            $updateCount = 0;
            $createCount = 0;
            $errorCount = 0;
            $errors = [];

            foreach ($dataRows as $index => $row) {
                // Skip empty rows (check if at least material_sap or description exists)
                if (empty($row[2]) && empty($row[3])) {
                    continue;
                }

                try {
                    $materialData = [
                        'divisi' => !empty($row[1]) ? $row[1] : 'LAIN',
                        'material_sap' => !empty($row[2]) ? $row[2] : '',
                        'material_description' => !empty($row[3]) ? $row[3] : '',
                        'base_unit_measure' => !empty($row[4]) ? $row[4] : '',
                        'status' => !empty($row[5]) ? strtoupper($row[5]) : 'INACTIVE',
                        'lokasi_sistem' => !empty($row[6]) ? $row[6] : null,
                        'lokasi_fisik' => !empty($row[7]) ? $row[7] : null,
                        'penempatan_alat' => !empty($row[8]) ? $row[8] : null,
                        'qty' => !empty($row[9]) ? (int)$row[9] : 0
                    ];

                    // If ID is provided (column A), try to update existing material
                    if (!empty($row[0])) {
                        $material = Material::find($row[0]);
                        if ($material) {
                            $material->update($materialData);
                            $updateCount++;
                        } else {
                            $errors[] = "Row " . ($index + 3) . ": Material with ID {$row[0]} not found";
                            $errorCount++;
                        }
                    } 
                    // If no ID provided, try to find by material_sap or create new
                    else {
                        if (!empty($materialData['material_sap'])) {
                            $material = Material::where('material_sap', $materialData['material_sap'])->first();
                            if ($material) {
                                $material->update($materialData);
                                $updateCount++;
                            } else {
                                // Create new material
                                Material::create($materialData);
                                $createCount++;
                            }
                        } else {
                            $errors[] = "Row " . ($index + 3) . ": Material SAP is required";
                            $errorCount++;
                        }
                    }
                } catch (\Exception $e) {
                    $errors[] = "Row " . ($index + 3) . ": " . $e->getMessage();
                    $errorCount++;
                }
            }

            $message = "";
            if ($createCount > 0) {
                $message .= "Created {$createCount} new materials. ";
            }
            if ($updateCount > 0) {
                $message .= "Updated {$updateCount} materials. ";
            }
            if ($errorCount > 0) {
                $message .= "{$errorCount} errors occurred.";
            }
            
            if (empty($message)) {
                $message = "No data processed.";
            }

            return response()->json([
                'success' => true,
                'message' => trim($message),
                'created_count' => $createCount,
                'updated_count' => $updateCount,
                'error_count' => $errorCount,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing file: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportData(Request $request)
    {
        // Security: Enhanced password validation and logging
        $password = $request->input('password');
        $correctPassword = config('security.export.default_password', 'TPS123');
        
        if (empty($password) || $password !== $correctPassword) {
            // Log failed export attempt
            \Log::warning('Export attempt with invalid password', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now(),
                'provided_password_length' => strlen($password ?? '')
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Invalid password'
            ], 403);
        }
        
        // Log successful export initiation
        \Log::info('Data export initiated', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now()
        ]);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers - all columns except photo
        $headers = [
            'A1' => 'ID',
            'B1' => 'Division',
            'C1' => 'Material SAP',
            'D1' => 'Description',
            'E1' => 'Unit',
            'F1' => 'Status',
            'G1' => 'System Location',
            'H1' => 'Physical Location',
            'I1' => 'Placement',
            'J1' => 'Quantity',
            'K1' => 'Created At',
            'L1' => 'Updated At'
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
            $sheet->getStyle($cell)->getFont()->setBold(true);
        }

        // Get all materials
        $materials = Material::all();
        $row = 2;

        foreach ($materials as $material) {
            $sheet->setCellValue('A' . $row, $material->id);
            $sheet->setCellValue('B' . $row, $material->divisi);
            $sheet->setCellValue('C' . $row, $material->material_sap);
            $sheet->setCellValue('D' . $row, $material->material_description);
            $sheet->setCellValue('E' . $row, $material->base_unit_measure);
            $sheet->setCellValue('F' . $row, $material->status);
            $sheet->setCellValue('G' . $row, $material->lokasi_sistem);
            $sheet->setCellValue('H' . $row, $material->lokasi_fisik);
            $sheet->setCellValue('I' . $row, $material->penempatan_alat);
            $sheet->setCellValue('J' . $row, $material->qty);
            $sheet->setCellValue('K' . $row, $material->created_at);
            $sheet->setCellValue('L' . $row, $material->updated_at);
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'L') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'material_export_' . date('Y-m-d_H-i-s') . '.xlsx';

        // Return as download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    public function updateMaterial(Request $request, $id)
    {
        $request->validate([
            'divisi' => 'required|string|max:50',
            'base_unit_measure' => 'required|string|max:10',
            'status' => 'required|in:ACTIVE,INACTIVE',
            'lokasi_sistem' => 'nullable|string|max:100',
            'lokasi_fisik' => 'nullable|string|max:100', 
            'penempatan_alat' => 'nullable|string|max:100',
            'photo' => 'nullable|url',
            'qty' => 'required|integer|min:0'
        ]);

        try {
            $material = Material::findOrFail($id);
            
            // Update only editable fields (not material_sap and material_description)
            $material->update([
                'divisi' => $request->divisi,
                'base_unit_measure' => $request->base_unit_measure,
                'status' => $request->status,
                'lokasi_sistem' => $request->lokasi_sistem,
                'lokasi_fisik' => $request->lokasi_fisik,
                'penempatan_alat' => $request->penempatan_alat,
                'photo' => $request->photo,
                'qty' => $request->qty
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Material updated successfully',
                'material' => $material
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating material: ' . $e->getMessage()
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ConfigController extends Controller
{
    public function index()
    {
        return view('admin.config.index');
    }

    public function manage($group = 'footer')
    {
        $allowedGroups = ['footer', 'about_us', 'how_it_works', 'general'];

        if (!in_array($group, $allowedGroups)) {
            abort(404);
        }

        $settings = DB::table('system_config')
            ->where('config_group', $group)
            ->orderBy('id')
            ->get();

        return view('admin.config.manage', compact('settings', 'group'));
    }

    public function update(Request $request, $group)
    {
        $validator = Validator::make($request->all(), [
            'config.*' => 'nullable',
            'image_about_us_image_1' => 'nullable|mimes:jpg,jpeg,png,gif,pdf,doc,docx|max:5120',
            'image_about_us_image_2' => 'nullable|mimes:jpg,jpeg,png,gif,pdf,doc,docx|max:5120',
            'image_How_Works_For_Buyers_image' => 'nullable|mimes:jpg,jpeg,png,gif,pdf,doc,docx|max:5120',
            'image_How_Works_For_Farmer_image' => 'nullable|mimes:jpg,jpeg,png,gif,pdf,doc,docx|max:5120',
            'legal_footer_privacy_policy' => 'nullable|mimes:pdf,doc,docx,txt|max:5120',
            'legal_footer_terms_of_service' => 'nullable|mimes:pdf,doc,docx,txt|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();
            $userId = Auth::id();
            $data = $request->input('config', []);
            $imageFields = [
                'about_us_image_1',
                'about_us_image_2',
                'How_Works_For_Buyers_image',
                'How_Works_For_Farmer_image'
            ];

            $legalFields = [
                'footer_privacy_policy',
                'footer_terms_of_service'
            ];

            foreach ($data as $key => $value) {
                $configKey = str_replace('_', ' ', $key);
                $configKey = ucwords($configKey);

                DB::table('system_config')->updateOrInsert(
                    ['config_key' => $key],
                    [
                        'config_value' => $value,
                        'config_group' => $group,
                        'description' => $configKey,
                        'is_public' => true,
                        'updated_by' => $userId,
                        'updated_at' => now()
                    ]
                );
            }

            foreach ($imageFields as $imageField) {
                if ($request->hasFile("image_$imageField")) {
                    $file = $request->file("image_$imageField");
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $path = 'assets/images/';

                    $file->move(public_path($path), $filename);

                    DB::table('system_config')->updateOrInsert(
                        ['config_key' => $imageField],
                        [
                            'config_value' => $filename,
                            'config_group' => $group === 'how_it_works' ? 'how_it_works' : 'about_us',
                            'description' => ucwords(str_replace('_', ' ', $imageField)),
                            'is_public' => true,
                            'updated_by' => $userId,
                            'updated_at' => now()
                        ]
                    );
                }
            }

            foreach ($legalFields as $legalField) {
                if ($request->hasFile("legal_$legalField")) {
                    $file = $request->file("legal_$legalField");
                    $filename = time() . '_' . uniqid() . '_' . $legalField . '.' . $file->getClientOriginalExtension();
                    $path = 'uploads/Legal Documents/';

                    if (!file_exists(public_path($path))) {
                        mkdir(public_path($path), 0777, true);
                    }

                    $file->move(public_path($path), $filename);

                    DB::table('system_config')->updateOrInsert(
                        ['config_key' => $legalField],
                        [
                            'config_value' => $filename,
                            'config_group' => 'footer',
                            'description' => ucwords(str_replace('_', ' ', $legalField)),
                            'is_public' => true,
                            'updated_by' => $userId,
                            'updated_at' => now()
                        ]
                    );
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Configuration updated successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error updating configuration: ' . $e->getMessage()
            ], 500);
        }
    }

    public function backup(Request $request)
    {
        $type = $request->get('type', 'csv');

        $host = env('DB_HOST', '127.0.0.1');
        $port = env('DB_PORT', '5432');
        $database = env('DB_DATABASE');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');

        $conn = pg_connect("host=$host port=$port dbname=$database user=$username password=$password");

        if (!$conn) {
            return response()->json([
                'success' => false,
                'message' => 'Database connection failed'
            ], 500);
        }

        $date = date('Y-m-d_H-i-s');
        $filename = "db_backup_$date." . ($type === 'csv' ? 'csv' : 'txt');

        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: text/plain");

        $output = fopen("php://output", "w");

        $tablesResult = pg_query($conn, "
            SELECT tablename
            FROM pg_tables
            WHERE schemaname = 'public'
            ORDER BY tablename
        ");

        while ($table = pg_fetch_assoc($tablesResult)) {
            $tableName = $table['tablename'];
            $dataResult = pg_query($conn, "SELECT * FROM \"$tableName\"");

            if (!$dataResult) continue;

            $numFields = pg_num_fields($dataResult);
            $columns = [];

            for ($i = 0; $i < $numFields; $i++) {
                $columns[] = pg_field_name($dataResult, $i);
            }

            fwrite($output, "\n\n" . str_repeat("=", 60) . "\n");
            fwrite($output, "TABLE NAME : $tableName\n");
            fwrite($output, str_repeat("=", 60) . "\n\n");

            if ($type === 'csv') {
                fputcsv($output, $columns);

                while ($row = pg_fetch_assoc($dataResult)) {
                    fputcsv($output, $row);
                }
            } else {
                $rows = [];
                $widths = array_map('strlen', $columns);

                while ($row = pg_fetch_assoc($dataResult)) {
                    $rows[] = $row;
                    foreach (array_values($row) as $i => $value) {
                        $widths[$i] = max($widths[$i], strlen((string)$value));
                    }
                }

                $line = "+";
                foreach ($widths as $w) {
                    $line .= str_repeat("-", $w + 2) . "+";
                }
                $line .= "\n";

                fwrite($output, $line);
                fwrite($output, "|");
                foreach ($columns as $i => $col) {
                    fwrite($output, " " . str_pad($col, $widths[$i]) . " |");
                }
                fwrite($output, "\n");
                fwrite($output, $line);

                foreach ($rows as $row) {
                    fwrite($output, "|");
                    $i = 0;
                    foreach ($row as $value) {
                        fwrite($output, " " . str_pad((string)$value, $widths[$i]) . " |");
                        $i++;
                    }
                    fwrite($output, "\n");
                }

                fwrite($output, $line);
            }
        }

        fclose($output);
        pg_close($conn);
        exit;
    }
}

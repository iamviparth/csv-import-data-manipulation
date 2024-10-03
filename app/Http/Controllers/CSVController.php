<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class CSVController extends Controller
{
    public function index()
    {
        return view('upload');
    }

    public function upload(Request $request)
    {
        $file = $request->file('file');
        $data = Excel::toArray([], $file);
        $columns = $data[0][0];
        $rows = array_slice($data[0], 1);


        return view('map_columns', ['columns' => $columns, 'rows' => $rows]);
    }

    private function isNumericColumn($csvColumnData)
    {
        foreach ($csvColumnData as $value) {
            if (!is_numeric($value) && !is_null($value)) {
                return false;
            }
        }
        return true;
    }

    public function createTable(Request $request)
    {
        $columns = $request->input('columns');
        $csvData = json_decode($request->input('rows'), true);

        $tableName = 'dynamic_table_' . time();

        $columns = array_filter($columns, function($dbColumn) {
            return !empty($dbColumn);
        });

        if (isset($columns['Id']) || isset($columns['id'])) {
            unset($columns['Id']);
            unset($columns['id']);
        }

        Schema::create($tableName, function (Blueprint $table) use ($columns, $csvData) {
            $table->id();
            foreach ($columns as $csvColumn => $dbColumn) {
                if (!empty($csvColumn)) {
                    $columnData = array_column($csvData, $csvColumn);
                    if ($this->isNumericColumn($columnData)) {
                        $table->integer($dbColumn)->nullable();
                    } else {
                        $table->string($dbColumn)->nullable();
                    }
                }
            }
            $table->timestamps();
        });

        return response()->json(['message' => 'Table created successfully!', 'table' => $tableName]);
    }


    public function filterData(Request $request)
    {
        $tableName = $request->input('table_name');

        if (!$tableName) {
            return response()->json(['error' => 'Table name is required'], 400);
        }

        $id = $request->input('id');
        $results = DB::table($tableName)->where('id', $id)->get();

        return response()->json($results);
    }

}

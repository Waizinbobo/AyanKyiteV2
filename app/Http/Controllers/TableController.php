<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TableController extends Controller
{
    public function index()
    {
        $tables = DB::table('tables')->get();
        return response()->json(['success' => true, 'data' => $tables]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'number' => 'required|integer|unique:tables',
            'seats' => 'required|integer|min:1|max:20',
            'location' => 'required|string',
            'status' => 'required|string',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $tableId = DB::table('tables')->insertGetId([
            'name' => $request->name,
            'number' => $request->number,
            'seats' => $request->seats,
            'location' => $request->location,
            'status' => $request->status,
            'description' => $request->description,
            'is_active' => $request->is_active ?? true,
            'qr_code_url' => $this->generateQRCodeUrl($request->number),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json(['success' => true, 'message' => 'Table created successfully', 'data' => ['id' => $tableId]]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'number' => 'required|integer|unique:tables,number,' . $id,
            'seats' => 'required|integer|min:1|max:20',
            'location' => 'required|string',
            'status' => 'required|string',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        DB::table('tables')->where('id', $id)->update([
            'name' => $request->name,
            'number' => $request->number,
            'seats' => $request->seats,
            'location' => $request->location,
            'status' => $request->status,
            'description' => $request->description,
            'is_active' => $request->is_active ?? true,
            'qr_code_url' => $this->generateQRCodeUrl($request->number),
            'updated_at' => now()
        ]);

        return response()->json(['success' => true, 'message' => 'Table updated successfully']);
    }

    public function destroy($id)
    {
        DB::table('tables')->where('id', $id)->delete();
        return response()->json(['success' => true, 'message' => 'Table deleted successfully']);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|string']);

        DB::table('tables')->where('id', $id)->update([
            'status' => $request->status,
            'updated_at' => now()
        ]);

        return response()->json(['success' => true, 'message' => 'Table status updated successfully']);
    }

    public function stats()
    {
        $stats = [
            'total' => DB::table('tables')->count(),
            'available' => DB::table('tables')->where('status', 'available')->count(),
            'occupied' => DB::table('tables')->where('status', 'occupied')->count(),
            'reserved' => DB::table('tables')->where('status', 'reserved')->count(),
            'maintenance' => DB::table('tables')->where('status', 'maintenance')->count(),
            'active' => DB::table('tables')->where('is_active', true)->count()
        ];

        return response()->json(['success' => true, 'data' => $stats]);
    }

    public function getQRCode($tableNumber)
    {
        $table = DB::table('tables')->where('number', $tableNumber)->first();

        if (!$table) {
            return response()->json(['success' => false, 'message' => 'Table not found'], 404);
        }

        // Generate QR code using Google Charts API
        $qrCodeUrl = "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=" . urlencode($table->qr_code_url);

        return response()->json(['success' => true, 'data' => ['qr_code_url' => $qrCodeUrl]]);
    }

    private function generateQRCodeUrl($tableNumber)
    {
        return url('/menu?table=' . $tableNumber);
    }
}

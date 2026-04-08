<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterKey;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MasterKeyController extends Controller
{
    public function index()
    {
        $keys       = MasterKey::with('tenant')->latest()->paginate(20);
        $activeKey  = MasterKey::where('is_active', true)->latest()->first();
        $totalKeys  = MasterKey::count();

        return view('admin.keys.index', compact('keys', 'activeKey', 'totalKeys'));
    }

    public function rotate()
    {
        try {
            $result = DB::selectOne("
                SELECT rotate_master_key(
                    encode(gen_random_bytes(32), 'hex')
                ) AS result
            ");

            $data = json_decode($result->result);

            ActivityLog::create([
                'tenant_id'    => Auth::user()->tenant_id,
                'action'       => 'rotate',
                'ip_address'   => request()->ip(),
                'status'       => 'success',
                'message'      => 'Rotation effectuée. Version ' . $data->new_version . '. ' . $data->total_rotated . ' enregistrement(s) migré(s).',
                'performed_at' => now(),
            ]);

            return back()->with('success',
                '✅ Rotation réussie ! Nouvelle version : v' . $data->new_version .
                ' — ' . $data->total_rotated . ' enregistrement(s) migré(s).'
            );

        } catch (\Exception $e) {
            ActivityLog::create([
                'tenant_id'    => Auth::user()->tenant_id,
                'action'       => 'rotate',
                'ip_address'   => request()->ip(),
                'status'       => 'failed',
                'message'      => $e->getMessage(),
                'performed_at' => now(),
            ]);

            return back()->with('error', '❌ Erreur lors de la rotation : ' . $e->getMessage());
        }
    }

    public function status()
    {
        try {
            $result = DB::selectOne("SELECT get_rotation_status() AS result");
            $data   = json_decode($result->result);
            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        $query = Complaint::with(['complainant', 'againstUser', 'order'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('complaint_type', $request->type);
        }

        if ($request->filled('fromDate')) {
            $query->whereDate('created_at', '>=', $request->fromDate);
        }

        if ($request->filled('toDate')) {
            $query->whereDate('created_at', '<=', $request->toDate);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhereHas('complainant', function($q) use ($search) {
                      $q->where('username', 'like', "%{$search}%");
                  });
            });
        }

        $complaints = $query->paginate(4);

        // Fetch facilitators for the alert feature
        $facilitatorsList = DB::table('facilitators')
            ->join('users', 'facilitators.user_id', '=', 'users.id')
            ->where('users.is_active', true)
            ->select('facilitators.user_id', 'facilitators.name', 'facilitators.assigned_division')
            ->get();

        return view('admin.complaints.index', compact('complaints', 'facilitatorsList'));
    }

    public function alert(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:complaints,id',
            'facilitator_id' => 'required|exists:users,id'
        ]);

        try {
            $complaint = Complaint::findOrFail($request->id);
            $facilitatorId = $request->facilitator_id;

            // Create notification for the facilitator
            \App\Models\Notification::create([
                'user_id' => $facilitatorId,
                'recipient_type' => 'user',
                'recipient_address' => null,
                'notification_type' => 'admin_alert',
                'title' => 'New Complaint Alert',
                'message' => "You have been alerted regarding complaint #{$complaint->id}. Please review it.",
                'is_read' => false,
                'related_id' => $complaint->id
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Facilitator alerted successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Facilitator Alert failed: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to alert facilitator: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:new,in_progress,resolved,rejected'
        ]);

        DB::beginTransaction();
        try {
            $complaint = Complaint::findOrFail($id);
            $oldStatus = $complaint->status;
            $complaint->status = $request->status;

            if ($request->status === 'resolved' && auth()->user()->role === 'admin') {
                $complaint->resolved_by_facilitator_id = auth()->id();
            }

            $complaint->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Complaint status updated successfully!',
                'status' => $complaint->status,
                'old_status' => $oldStatus
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $complaint = Complaint::with(['complainant', 'againstUser', 'order', 'resolvedBy'])
            ->findOrFail($id);

        return view('admin.complaints.show', compact('complaint'));
    }

    public function getComplaintDetails($id)
    {
        $complaint = Complaint::with(['complainant', 'againstUser', 'order'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'complaint' => $complaint
        ]);
    }

    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'complaint_ids' => 'required|array',
            'complaint_ids.*' => 'exists:complaints,id',
            'status' => 'required|in:new,in_progress,resolved,rejected'
        ]);

        DB::beginTransaction();
        try {
            $updatedCount = Complaint::whereIn('id', $request->complaint_ids)
                ->update([
                    'status' => $request->status,
                    'updated_at' => now()
                ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Successfully updated {$updatedCount} complaints to {$request->status} status!",
                'updated_count' => $updatedCount
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to bulk update status: ' . $e->getMessage()
            ], 500);
        }
    }
}

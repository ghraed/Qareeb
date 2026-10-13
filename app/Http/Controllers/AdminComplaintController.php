<?php

namespace App\Http\Controllers;

use App\Enums\ReportStatus;
use App\Models\Complaint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminComplaintController extends Controller
{
    public function index(): View
    {
        return view('admin.complaints', ['complaints' => Complaint::with(['user', 'provider'])->latest()->orderByDesc('id')->simplePaginate(20)]);
    }

    public function update(Request $request, Complaint $complaint): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', Rule::enum(ReportStatus::class)], 'internal_notes' => ['nullable', 'string', 'max:5000']]);
        $complaint->update([...$data, 'reviewed_by' => $request->user()->id, 'reviewed_at' => now()]);

        return back()->with('status', 'Complaint review saved.');
    }
}

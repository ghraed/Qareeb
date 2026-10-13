<?php

namespace App\Http\Controllers;

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use App\Http\Requests\StoreComplaintRequest;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SeekerComplaintController extends Controller
{
    public function index(): View
    {
        return view('seeker.complaints.index', ['complaints' => Complaint::where('user_id', request()->user()->id)->latest()->orderByDesc('id')->simplePaginate(10)]);
    }

    public function create(): View
    {
        return view('seeker.complaints.create', ['providers' => User::where('role', UserRole::ServiceProvider->value)
            ->where('account_status', AccountStatus::Active->value)->whereHas('businessProfile')->with('businessProfile')->orderBy('name')->get()]);
    }

    public function store(StoreComplaintRequest $request): RedirectResponse
    {
        $data = $request->safe()->only(['type', 'description', 'provider_id', 'submission_token']);
        $data['provider_id'] = isset($data['provider_id']) ? (int) $data['provider_id'] : null;
        $fingerprint = hash('sha256', json_encode([$request->user()->id, $data['type'], trim($data['description']), $data['provider_id'] ?? null]));
        $complaint = DB::transaction(function () use ($request, $data, $fingerprint): Complaint {
            User::whereKey($request->user()->id)->lockForUpdate()->firstOrFail();
            $existing = Complaint::where('user_id', $request->user()->id)->where(function ($query) use ($data, $fingerprint): void {
                $query->where('submission_token', $data['submission_token'])->orWhere(fn ($query) => $query->where('fingerprint', $fingerprint)->where('created_at', '>=', now()->subMinutes(10)));
            })->first();

            return $existing ?? Complaint::create([...$data, 'user_id' => $request->user()->id, 'fingerprint' => $fingerprint]);
        });

        return redirect()->route('seeker.complaints.show', $complaint)->with('status', 'Your complaint has been received for private review.');
    }

    public function show(Complaint $complaint): View
    {
        abort_unless($complaint->user_id === request()->user()->id, 404);

        return view('seeker.complaints.show', ['complaint' => $complaint]);
    }
}

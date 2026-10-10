<?php

namespace App\Http\Controllers;

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use App\Http\Requests\ProviderDirectoryRequest;
use App\Models\BusinessProfile;
use Illuminate\View\View;

class ProviderDirectoryController extends Controller
{
    public function __invoke(ProviderDirectoryRequest $request): View
    {
        $filters = $request->safe()->except('page');
        $providers = BusinessProfile::query()->whereHas('user', fn ($query) => $query
            ->where('role', UserRole::ServiceProvider->value)->where('account_status', AccountStatus::Active->value))
            ->with('user');
        if ($request->filled('category')) {
            $providers->containingSelection('categories', $request->string('category')->toString());
        }

        return view('providers.index', ['providers' => $providers->orderBy('id')->paginate(12)->appends($filters), 'filters' => $filters]);
    }
}

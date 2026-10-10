<div class="space-y-2">
    <label class="flex min-h-11 items-start gap-3" for="terms_accepted">
        <input class="mt-1 h-5 w-5 shrink-0" id="terms_accepted" type="checkbox" name="terms_accepted" value="1" required @checked(old('terms_accepted'))>
        <span>I have read and accept the <a class="font-semibold underline" href="{{ route('terms.show') }}" target="_blank" rel="noopener">Terms and Conditions</a>@unless(config('terms.approved')) (draft pending owner/legal approval)@endunless.</span>
    </label>
    <input type="hidden" name="terms_version" value="{{ config('terms.version') }}">
    @error('terms_accepted')<p class="text-sm text-rose-700" role="alert">{{ $message }}</p>@enderror
    @error('terms_version')<p class="text-sm text-rose-700" role="alert">The Terms version changed. Read the current Terms and submit again.</p>@enderror
</div>

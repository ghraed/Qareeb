<?php

require __DIR__.'/../../vendor/autoload.php';
$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
if (config('database.default') !== 'sqlite' || config('database.connections.sqlite.database') !== ':memory:') {
    throw new RuntimeException('UI fixtures require SQLite :memory:.');
}
Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
$root = $argv[1];
if (! str_starts_with($root, '/tmp/qareeb-ui-')) {
    throw new RuntimeException('UI fixtures require a temporary output directory.');
}
@mkdir($root, 0700, true);
view()->share('errors', new Illuminate\Support\ViewErrorBag);
$provider = App\Models\User::factory()->serviceProvider()->create(['name' => 'Qareeb Fixture', 'email' => 'fixture@example.com']);
$profile = $provider->businessProfile()->create([
    'business_name' => 'Photography and Transportation Services', 'phone' => '+96170123456', 'description' => 'Local services available by direct contact.',
    'categories' => ['Photography', 'Transportation'], 'address' => 'Main street', 'state' => ['Beirut'], 'city' => ['Achrafieh'], 'is_available_for_hire' => true,
]);
foreach ([true, false] as $available) {
    $profile->update(['is_available_for_hire' => $available]);
    $html = app(App\Http\Controllers\PublicServiceProviderController::class)->show($provider->fresh())->render();
    $html = preg_replace('#<link[^>]+(?:fonts\.bunny|preconnect)[^>]*>#', '', $html);
    $html = str_replace(['http://localhost/build/', 'http://localhost/images/'], ['/build/', '/images/'], $html);
    $html = str_replace('</body>', '<script>window.addEventListener("load",()=>{let result=document.createElement("output");result.id="ui-check";result.dataset.overflow=String(document.documentElement.scrollWidth>innerWidth);result.dataset.hiring=String(Boolean(document.querySelector(\'[aria-label="Available for work"]\')));document.body.append(result);});</script></body>', $html);
    file_put_contents($root.'/provider-'.($available ? 'available' : 'unavailable').'.html', $html);
}
foreach (['build', 'images'] as $folder) {
    if (! is_link($root.'/'.$folder)) {
        symlink(public_path($folder), $root.'/'.$folder);
    }
}
echo $root;

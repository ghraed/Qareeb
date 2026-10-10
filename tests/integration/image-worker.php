<?php

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use App\Models\BusinessProfile;
use App\Models\User;
use App\Services\BusinessPhotoService;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Database\QueryException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

require __DIR__.'/../../vendor/autoload.php';
$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$root = getenv('QAREEB_IMAGE_RACE_ROOT');
if (! $root || ! str_starts_with($root, '/tmp/qareeb-image-race-') || config('database.default') !== 'sqlite' || config('database.connections.sqlite.database') !== $root.'/database.sqlite') {
    throw new RuntimeException('Concurrency verification requires its isolated temporary SQLite database.');
}
Storage::set('public', Storage::build(['driver' => 'local', 'root' => $root.'/images', 'throw' => true]));

if ($argv[1] === 'setup') {
    Artisan::call('migrate', ['--force' => true]);
    $user = User::create(['name' => 'Concurrent fixture', 'username' => 'fixture', 'password' => 'test-password', 'role' => UserRole::ServiceProvider, 'account_status' => AccountStatus::Active]);
    $user->businessProfile()->create(['work_samples' => ['fixture1.jpg', 'fixture2.jpg', 'fixture3.jpg', 'fixture4.jpg', 'fixture5.jpg']]);
} elseif ($argv[1] === 'upload') {
    $profile = BusinessProfile::firstOrFail();
    $deadline = microtime(true) + 10;
    while (! file_exists($root.'/start')) {
        if (microtime(true) > $deadline) {
            throw new RuntimeException('Concurrent verification barrier timed out.');
        }
        usleep(1000);
    }
    try {
        app(BusinessPhotoService::class)->addWorkSamples($profile, [UploadedFile::fake()->image('race.jpg')]);
        echo 'saved';
    } catch (ValidationException|QueryException $exception) {
        echo 'blocked';
    }
} else {
    echo json_encode(['samples' => count(BusinessProfile::firstOrFail()->work_samples), 'files' => count(Storage::disk('public')->allFiles())]);
}

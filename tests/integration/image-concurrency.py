"""Verify overlapping image requests using only disposable SQLite data/files."""
import json
import os
from pathlib import Path
import subprocess
import tempfile

with tempfile.TemporaryDirectory(prefix='qareeb-image-race-', dir='/tmp') as root:
    Path(root, 'database.sqlite').touch()
    env = {**os.environ, 'APP_ENV': 'testing', 'APP_KEY': 'base64:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA=',
           'DB_CONNECTION': 'sqlite', 'DB_DATABASE': root + '/database.sqlite', 'QAREEB_IMAGE_RACE_ROOT': root,
           'CACHE_STORE': 'array', 'SESSION_DRIVER': 'array'}
    command = ['php', 'tests/integration/image-worker.php']
    subprocess.run(command + ['setup'], env=env, check=True, capture_output=True)
    workers = [subprocess.Popen(command + ['upload'], env=env, stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True) for _ in range(2)]
    Path(root, 'start').touch()
    results = []
    for worker in workers:
        output, error = worker.communicate(timeout=15)
        assert worker.returncode == 0, error
        results.append(output)
    state = json.loads(subprocess.check_output(command + ['inspect'], env=env, text=True))
    assert sorted(results) == ['blocked', 'saved'], results
    assert state == {'samples': 6, 'files': 1}, state
    print('PASS: two overlapping uploads; one saved, one safely blocked; six gallery references, one new file, no orphan files.')

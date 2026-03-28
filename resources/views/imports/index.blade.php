<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-sky-600">Import Center</p>
                <h1 class="page-title">Import CSV, Excel, or public Google Drive datasets.</h1>
                <p class="page-subtitle">
                    Supported headings: Business Name, Area, City, Mobile No, Category, Sub Category, and Address.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
        <div class="panel p-6">
            <h2 class="text-lg font-bold text-slate-950">New import</h2>
            <p class="mt-1 text-sm text-slate-500">Upload a local file or paste a public Google Drive file URL.</p>

            <form id="import-form" method="POST" action="{{ route('imports.store') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
                @csrf

                <div>
                    <label for="upload_file" class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Local file</label>
                    <input id="upload_file" name="upload_file" type="file" class="filter-input mt-2" accept=".csv,.txt,.xlsx,.xls">
                    <p class="mt-2 text-xs text-slate-500">Use CSV or spreadsheet files up to 20MB.</p>
                </div>

                <div class="flex items-center gap-3">
                    <div class="h-px flex-1 bg-slate-200"></div>
                    <span class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">or</span>
                    <div class="h-px flex-1 bg-slate-200"></div>
                </div>

                <div>
                    <label for="google_drive_url" class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Google Drive file URL</label>
                    <input
                        id="google_drive_url"
                        name="google_drive_url"
                        type="url"
                        value="{{ old('google_drive_url') }}"
                        class="filter-input mt-2"
                        placeholder="https://drive.google.com/file/d/..."
                    >
                    <p class="mt-2 text-xs text-slate-500">Paste a direct public file link, not a folder URL.</p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <div class="flex items-center justify-between text-sm">
                        <span class="font-semibold text-slate-700">Upload progress</span>
                        <span id="upload-progress-label" class="text-slate-500">Waiting to start</span>
                    </div>
                    <div class="mt-3 h-3 overflow-hidden rounded-full bg-slate-200">
                        <div id="upload-progress-bar" class="h-full w-0 rounded-full bg-sky-500 transition-all duration-300"></div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <button id="import-submit" type="submit" class="btn-primary">Start Import</button>
                    <a href="{{ route('businesses.index') }}" class="btn-secondary">View Records</a>
                </div>
            </form>
        </div>

        <div class="panel p-6">
            <h2 class="text-lg font-bold text-slate-950">Import tips</h2>
            <div class="mt-4 space-y-4 text-sm leading-7 text-slate-600">
                <div class="panel-muted p-4">
                    Header row names are normalized automatically, so `Business Name`, `business_name`, and `Business-Name` are all accepted.
                </div>
                <div class="panel-muted p-4">
                    Duplicate detection runs after each import using normalized business name, area, city, and address values.
                </div>
                <div class="panel-muted p-4">
                    Incomplete listings are still saved so they can be corrected later from the review pages.
                </div>
            </div>
        </div>
    </div>

    <div class="panel mt-6 overflow-hidden">
        <div class="border-b border-slate-200 px-6 py-5">
            <h2 class="text-lg font-bold text-slate-950">Import history</h2>
            <p class="mt-1 text-sm text-slate-500">Every import is logged for traceability and review.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="table-shell">
                <thead>
                    <tr>
                        <th>File</th>
                        <th>Source</th>
                        <th>Status</th>
                        <th>Processed</th>
                        <th>Inserted</th>
                        <th>Duplicates</th>
                        <th>Invalid</th>
                        <th>Started</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($importLogs as $log)
                        <tr>
                            <td>
                                <p class="font-semibold text-slate-900">{{ $log->file_name }}</p>
                                @if ($log->notes)
                                    <p class="mt-1 text-xs text-slate-500">{{ $log->notes }}</p>
                                @endif
                            </td>
                            <td>{{ ucfirst(str_replace('_', ' ', $log->source_type)) }}</td>
                            <td>
                                <span class="{{ $log->status === 'completed' ? 'badge-success' : ($log->status === 'failed' ? 'badge-danger' : 'badge-warning') }}">
                                    {{ ucfirst($log->status) }}
                                </span>
                            </td>
                            <td>{{ $log->imported_rows }}</td>
                            <td>{{ $log->inserted_rows }}</td>
                            <td>{{ $log->duplicate_rows }}</td>
                            <td>{{ $log->invalid_rows }}</td>
                            <td>{{ optional($log->started_at)->format('d M Y, h:i A') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-sm text-slate-500">No import history available yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 px-6 py-4">
            {{ $importLogs->links() }}
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const form = document.getElementById('import-form');
                const submitButton = document.getElementById('import-submit');
                const progressBar = document.getElementById('upload-progress-bar');
                const progressLabel = document.getElementById('upload-progress-label');

                form.addEventListener('submit', (event) => {
                    event.preventDefault();

                    const formData = new FormData(form);
                    submitButton.disabled = true;
                    submitButton.textContent = 'Importing...';
                    progressLabel.textContent = 'Uploading...';

                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', form.action, true);
                    xhr.setRequestHeader('Accept', 'application/json');
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

                    xhr.upload.addEventListener('progress', (progressEvent) => {
                        if (!progressEvent.lengthComputable) {
                            return;
                        }

                        const percent = Math.round((progressEvent.loaded / progressEvent.total) * 100);
                        progressBar.style.width = `${percent}%`;
                        progressLabel.textContent = `Uploading ${percent}%`;
                    });

                    xhr.addEventListener('load', () => {
                        submitButton.disabled = false;
                        submitButton.textContent = 'Start Import';

                        if (xhr.status >= 200 && xhr.status < 300) {
                            progressBar.style.width = '100%';
                            progressLabel.textContent = 'Processing completed';
                            const response = JSON.parse(xhr.responseText);
                            window.location.href = response.redirect;
                            return;
                        }

                        progressBar.style.width = '0%';
                        progressLabel.textContent = 'Import failed';
                        const response = JSON.parse(xhr.responseText || '{}');
                        alert(response.message || 'The import failed. Please review the file and try again.');
                    });

                    xhr.addEventListener('error', () => {
                        submitButton.disabled = false;
                        submitButton.textContent = 'Start Import';
                        progressBar.style.width = '0%';
                        progressLabel.textContent = 'Network error';
                        alert('The import request could not be completed.');
                    });

                    xhr.send(formData);
                });
            });
        </script>
    @endpush
</x-app-layout>

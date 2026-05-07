@extends('install.layout')

@section('content')
    <div class="stepbar">
        <span class="step done">1. Requirements</span>
        <span class="step active">2. Database</span>
        <span class="step">3. Settings</span>
        <span class="step">4. Finalize</span>
        <span class="step">5. Complete</span>
    </div>

    <form method="POST" action="{{ route('install.database.store') }}" class="card stack">
        @csrf
        <div>
            <h2 style="margin:0 0 6px;font-size:18px">Database credentials</h2>
            <p class="muted small" style="margin:0">Enter the database details now. The installer will test the connection here and use it during the final install step.</p>
        </div>

        <div class="grid grid-2">
            <div style="grid-column: 1 / -1">
                <label>Database Type</label>
                <select name="connection" id="db_connection" onchange="toggleDbFields()">
                    <option value="mysql" {{ old('connection', $database['connection'] ?? 'mysql') === 'mysql' ? 'selected' : '' }}>
                        MySQL / MariaDB ({{ $drivers['mysql'] ? 'Enabled' : 'Disabled' }})
                    </option>
                    <option value="pgsql" {{ old('connection', $database['connection'] ?? 'mysql') === 'pgsql' ? 'selected' : '' }}>
                        PostgreSQL ({{ $drivers['pgsql'] ? 'Enabled' : 'Disabled' }})
                    </option>
                    <option value="sqlite" {{ old('connection', $database['connection'] ?? 'mysql') === 'sqlite' ? 'selected' : '' }}>
                        SQLite ({{ $drivers['sqlite'] ? 'Enabled' : 'Disabled' }})
                    </option>
                </select>
            </div>

            <div class="db-field-remote">
                <label>Host</label>
                <input type="text" name="host" value="{{ old('host', $database['host'] ?? '127.0.0.1') }}" placeholder="127.0.0.1">
            </div>
            <div class="db-field-remote">
                <label>Port</label>
                <input type="number" name="port" id="db_port" value="{{ old('port', $database['port'] ?? '3306') }}" placeholder="3306">
            </div>
            <div>
                <label id="db_name_label">Database Name</label>
                <input type="text" name="database" id="db_database" value="{{ old('database', $database['database'] ?? '') }}" placeholder="microshophub_db">
            </div>
            <div class="db-field-remote">
                <label>Username</label>
                <input type="text" name="username" value="{{ old('username', $database['username'] ?? '') }}" placeholder="root">
            </div>
            <div class="db-field-remote">
                <label>Password</label>
                <input type="password" name="password" value="{{ old('password', $database['password'] ?? '') }}" placeholder="Optional">
            </div>
            <div>
                <label>Table Prefix</label>
                <input type="text" name="prefix" value="{{ old('prefix', $database['prefix'] ?? '') }}" placeholder="Optional">
            </div>
        </div>

        <div class="btn-row">
            <a class="btn btn-soft" href="{{ route('install.requirements') }}">Back</a>
            <button class="btn btn-primary" type="submit">Test & Continue</button>
        </div>
    </form>

    <script>
        function toggleDbFields() {
            const connection = document.getElementById('db_connection').value;
            const remoteFields = document.querySelectorAll('.db-field-remote');
            const portInput = document.getElementById('db_port');
            const dbNameLabel = document.getElementById('db_name_label');
            const dbDatabaseInput = document.getElementById('db_database');

            if (connection === 'sqlite') {
                remoteFields.forEach(el => el.style.display = 'none');
                dbNameLabel.textContent = 'Database Path';
                if (!dbDatabaseInput.value) {
                    dbDatabaseInput.value = 'database/database.sqlite';
                }
            } else {
                remoteFields.forEach(el => el.style.display = 'block');
                dbNameLabel.textContent = 'Database Name';
                
                if (connection === 'pgsql') {
                    if (portInput.value === '3306' || !portInput.value) portInput.value = '5432';
                } else if (connection === 'mysql') {
                    if (portInput.value === '5432' || !portInput.value) portInput.value = '3306';
                }
            }
        }

        // Initialize on load
        document.addEventListener('DOMContentLoaded', toggleDbFields);
    </script>
@endsection

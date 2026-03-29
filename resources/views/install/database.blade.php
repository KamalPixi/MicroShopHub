@extends('install.layout')

@section('content')
    <div class="stepbar">
        <span class="step done">1. Requirements</span>
        <span class="step active">2. Database</span>
        <span class="step">3. Settings</span>
        <span class="step">4. Finish</span>
    </div>

    <form method="POST" action="{{ route('install.database.store') }}" class="card stack">
        @csrf
        <div>
            <h2 style="margin:0 0 6px;font-size:18px">Database credentials</h2>
            <p class="muted small" style="margin:0">Enter the database details now. The installer will validate and use them during the final install step.</p>
        </div>

        <div class="grid grid-2">
            <div>
                <label>Host</label>
                <input type="text" name="host" value="{{ old('host', $database['host'] ?? '127.0.0.1') }}" placeholder="127.0.0.1">
            </div>
            <div>
                <label>Port</label>
                <input type="number" name="port" value="{{ old('port', $database['port'] ?? '3306') }}" placeholder="3306">
            </div>
            <div>
                <label>Database</label>
                <input type="text" name="database" value="{{ old('database', $database['database'] ?? '') }}" placeholder="micro_shop_hub_db">
            </div>
            <div>
                <label>Username</label>
                <input type="text" name="username" value="{{ old('username', $database['username'] ?? '') }}" placeholder="root">
            </div>
            <div>
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
            <button class="btn btn-primary" type="submit">Continue</button>
        </div>
    </form>
@endsection

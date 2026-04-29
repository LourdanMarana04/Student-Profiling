@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold mb-4">Audit Settings</h2>

        @if(session('status'))
            <div class="mb-4 text-green-700">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('admin.audit-settings.update') }}">
            @csrf
            @method('PATCH')

            <div class="mb-4">
                <label class="block font-semibold mb-1">Record read/access events</label>
                <input type="checkbox" name="record_reads" value="1" {{ $recordReads ? 'checked' : '' }} /> Enable
            </div>

            <div class="mb-4">
                <label class="block font-semibold mb-1">Redacted keys (one per line)</label>
                <textarea name="redact" rows="6" class="w-full border rounded p-2">{{ $redactList }}</textarea>
                <p class="text-sm text-gray-500 mt-1">Keys are case-insensitive. Values will be replaced with the configured placeholder.</p>
            </div>

            <div>
                <button class="bg-amber-600 text-white px-4 py-2 rounded font-bold">Save settings</button>
            </div>
        </form>
    </div>
</div>
@endsection

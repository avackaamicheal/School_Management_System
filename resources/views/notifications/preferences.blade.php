@extends('layouts.app')
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <h1 class="m-0">Notification Preferences</h1>
                <p class="text-muted">Choose which notifications you want to receive.</p>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form action="{{ route('notification.preferences') }}" method="POST">
                    @csrf @method('POST')

                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Notification Settings</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table m-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 50%">Notification Type</th>
                                        <th class="text-center">In-App</th>
                                        <th class="text-center">Email</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($types as $key => $label)
                                        @php
                                            $pref = $preferences[$key] ?? null;
                                            $isCritical = in_array($key, \App\Models\NotificationPreference::CRITICAL);
                                            $inAppEnabled = $pref ? $pref->in_app_enabled : true;
                                            $emailEnabled = $pref ? $pref->email_enabled : true;
                                        @endphp
                                        <tr>
                                            <td class="align-middle">
                                                <strong>{{ $label }}</strong>
                                                @if ($isCritical)
                                                    <span class="badge badge-warning ml-1">Critical</span>
                                                @endif
                                            </td>
                                            <td class="text-center align-middle">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input"
                                                        id="in_app_{{ $key }}" name="in_app_{{ $key }}"
                                                        value="1" {{ $inAppEnabled ? 'checked' : '' }}>
                                                    <label class="custom-control-label"
                                                        for="in_app_{{ $key }}"></label>
                                                </div>
                                            </td>
                                            <td class="text-center align-middle">
                                                @if ($isCritical)
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="email_{{ $key }}" name="email_{{ $key }}"
                                                            value="1" {{ $emailEnabled ? 'checked' : '' }}>
                                                        <label class="custom-control-label"
                                                            for="email_{{ $key }}"></label>
                                                    </div>
                                                @else
                                                    <span class="text-muted text-sm">In-app only</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer text-right">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Preferences
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </section>
    </div>
@endsection

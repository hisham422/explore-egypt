@extends('admin.layouts.app', [
    'title' => 'Admin | Manage Period Attractions',
    'heading' => 'Manage Period Attractions',
    'subheading' => $period->title . ' (' . $period->civilization->name . ')',
])

@section('content')
    <div class="admin-toolbar">
        <a href="{{ route('admin.civilization-periods.index') }}" class="admin-btn admin-btn-muted">← Back to Periods</a>
    </div>

    <!-- Status Messages -->
    @if ($errors->any())
        <div class="admin-alert admin-alert-danger" style="margin-bottom: 16px;">
            <strong>Error:</strong>
            <ul style="margin: 6px 0 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('status'))
        <div class="admin-alert admin-alert-success" style="margin-bottom: 16px;">
            {{ session('status') }}
        </div>
    @endif

    @if (session('error'))
        <div class="admin-alert admin-alert-danger" style="margin-bottom: 16px;">
            {{ session('error') }}
        </div>
    @endif

    <!-- Period Info Card -->
    <div class="admin-card" style="margin-bottom: 24px;">
        <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 16px; align-items: center;">
            <div>
                <h3 style="margin: 0 0 4px; color: var(--admin-primary); font-size: 1.1rem;">{{ $period->title }}</h3>
                <p style="margin: 0; color: var(--admin-muted); font-size: 0.9rem;">{{ $period->civilization->name }}</p>
            </div>
            <div>
                <p style="margin: 0 0 4px; color: var(--admin-muted); font-size: 0.85rem; text-transform: uppercase;">Years</p>
                <p style="margin: 0; font-weight: 600;">{{ $period->formatted_year_range }}</p>
            </div>
            <div>
                <p style="margin: 0 0 4px; color: var(--admin-muted); font-size: 0.85rem; text-transform: uppercase;">Linked</p>
                <p style="margin: 0; font-weight: 600; color: var(--admin-primary); font-size: 1.2rem;">{{ count($linkedAttractions) }}</p>
            </div>
            <div>
                <p style="margin: 0 0 4px; color: var(--admin-muted); font-size: 0.85rem; text-transform: uppercase;">Available</p>
                <p style="margin: 0; font-weight: 600; color: var(--admin-primary); font-size: 1.2rem;">{{ count($availableAttractions) }}</p>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <!-- Linked Attractions -->
        <div>
            <h3 style="margin: 0 0 12px; color: var(--admin-primary); font-size: 1rem;">Linked Attractions</h3>
            @if ($linkedAttractions->isEmpty())
                <div class="admin-card-empty">
                    <p style="margin:0;">No attractions linked to this period yet.</p>
                </div>
            @else
                <div style="display: grid; gap: 8px;">
                    @foreach ($linkedAttractions as $attraction)
                        <div class="admin-card" style="display: flex; justify-content: space-between; align-items: center; padding: 12px 14px;">
                            <div style="flex: 1; min-width: 0;">
                                <p style="margin: 0; font-weight: 500; color: var(--admin-text);">{{ $attraction->name }}</p>
                                <p style="margin: 4px 0 0; color: var(--admin-muted); font-size: 0.9rem;">
                                    @if ($attraction->location)
                                        📍 {{ $attraction->location }}
                                    @endif
                                </p>
                            </div>
                            <form method="POST" 
                                  action="{{ route('admin.period-attractions.destroy', ['period' => $period, 'attraction' => $attraction]) }}"
                                  style="margin-left: 12px;"
                                  onsubmit="return confirm('Remove this attraction from the period?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="admin-btn admin-btn-danger" style="font-size: 0.85rem; padding: 6px 10px;">
                                    Remove
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Available Attractions -->
        <div>
            <h3 style="margin: 0 0 12px; color: var(--admin-primary); font-size: 1rem;">Available Attractions</h3>
            @if ($availableAttractions->isEmpty())
                <div class="admin-card-empty">
                    <p style="margin:0;">All attractions from {{ $period->civilization->name }} are already linked.</p>
                </div>
            @else
                <div style="display: grid; gap: 8px;">
                    @foreach ($availableAttractions as $attraction)
                        <div class="admin-card" style="display: flex; justify-content: space-between; align-items: center; padding: 12px 14px;">
                            <div style="flex: 1; min-width: 0;">
                                <p style="margin: 0; font-weight: 500; color: var(--admin-text);">{{ $attraction->name }}</p>
                                <p style="margin: 4px 0 0; color: var(--admin-muted); font-size: 0.9rem;">
                                    @if ($attraction->location)
                                        📍 {{ $attraction->location }}
                                    @endif
                                </p>
                            </div>
                            <form method="POST" 
                                  action="{{ route('admin.period-attractions.store', ['period' => $period]) }}"
                                  style="margin-left: 12px;">
                                @csrf
                                <input type="hidden" name="attraction_id" value="{{ $attraction->id }}">
                                <button type="submit" class="admin-btn admin-btn-primary" style="font-size: 0.85rem; padding: 6px 10px;">
                                    Add
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Alert Styles -->
    <style>
        .admin-alert {
            padding: 12px 14px;
            border-radius: 8px;
            border: 1px solid;
            font-size: 0.9rem;
        }

        .admin-alert-success {
            background: rgba(34, 197, 94, 0.1);
            border-color: rgba(34, 197, 94, 0.2);
            color: #15803d;
        }

        .admin-alert-danger {
            background: rgba(239, 68, 68, 0.1);
            border-color: rgba(239, 68, 68, 0.2);
            color: #7f1d1d;
        }

        body.dark .admin-alert-success {
            color: #86efac;
        }

        body.dark .admin-alert-danger {
            color: #fca5a5;
        }
    </style>
@endsection

@php($headers = $headers ?? [])

<div class="admin-table-wrap">
    <table class="admin-table">
        <thead>
        <tr>
            @foreach($headers as $header)
                <th>{{ $header }}</th>
            @endforeach
        </tr>
        </thead>
        <tbody>
        {{ $slot ?? '' }}
        </tbody>
    </table>
</div>

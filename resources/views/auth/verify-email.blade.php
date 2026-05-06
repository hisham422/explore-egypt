<x-tourism-layout title="Explore Egypt | Verify Email">
    <section class="auth-shell auth-page">
        <div class="auth-card">
            <h1>Verify your email</h1>
            <p>Check your inbox and click the verification link before continuing.</p>

            @if (session('status') == 'verification-link-sent')
                <x-auth-session-status class="meta" :status="'A new verification link has been sent.'" />
            @endif

            <div class="auth-actions">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary">Resend verification email</button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline">Logout</button>
                </form>
            </div>
        </div>
    </section>
</x-tourism-layout>

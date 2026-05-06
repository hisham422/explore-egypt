<x-tourism-layout title="Explore Egypt | Forgot Password">
    <section class="auth-shell auth-page">
        <div class="auth-card">
            <h1>Reset your password</h1>
            <p>Enter your email and we will send a secure reset link.</p>

            @if (session('status'))
                <p class="meta">{{ session('status') }}</p>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="field">
                    <x-input-label for="email" value="Email" />
                    <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus />
                    @error('email')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <div class="auth-actions">
                    <a href="{{ route('login') }}" class="btn btn-outline">Back to login</a>
                    <button type="submit" class="btn btn-primary">Send reset link</button>
                </div>
            </form>
        </div>
    </section>
</x-tourism-layout>

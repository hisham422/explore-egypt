<x-tourism-layout title="Explore Egypt | Confirm Password">
    <section class="auth-shell auth-page">
        <div class="auth-card">
            <h1>Confirm your password</h1>
            <p>For security, re-enter your password to continue.</p>

            <form method="POST" action="{{ route('password.confirm') }}">
                @csrf

                <div class="field">
                    <x-input-label for="password" value="Password" />
                    <x-text-input id="password" type="password" name="password" required autocomplete="current-password" />
                    @error('password')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <div class="auth-actions">
                    <a href="{{ route('home') }}" class="btn btn-outline">Cancel</a>
                    <button type="submit" class="btn btn-primary">Confirm</button>
                </div>
            </form>
        </div>
    </section>
</x-tourism-layout>

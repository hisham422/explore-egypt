<x-tourism-layout title="Explore Egypt | Reset Password">
    <section class="auth-shell auth-page">
        <div class="auth-card">
            <h1>Create new password</h1>
            <p>Choose a strong password to secure your account.</p>

            <form method="POST" action="{{ route('password.store') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div class="field">
                    <x-input-label for="email" value="Email" />
                    <x-text-input id="email" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
                    @error('email')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field">
                    <x-input-label for="password" value="Password" />
                    <x-text-input id="password" type="password" name="password" required autocomplete="new-password" />
                    @error('password')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field">
                    <x-input-label for="password_confirmation" value="Confirm password" />
                    <x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" />
                    @error('password_confirmation')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <div class="auth-actions">
                    <a href="{{ route('login') }}" class="btn btn-outline">Back to login</a>
                    <button type="submit" class="btn btn-primary">Reset password</button>
                </div>
            </form>
        </div>
    </section>
</x-tourism-layout>

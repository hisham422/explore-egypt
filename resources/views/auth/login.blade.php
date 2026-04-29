<x-tourism-layout title="Explore Egypt | Login">
    <section class="auth-shell">
        <div class="auth-card">
            <h1>Welcome back</h1>
            <p>Log in to manage your favorites and profile.</p>

            @if (session('status'))
                <p class="meta">{{ session('status') }}</p>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="field">
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                    @error('email')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field">
                    <label for="password">Password</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password">
                    @error('password')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <div class="login-utility-row">
                    <label class="auth-remember field" for="remember_me">
                        <input id="remember_me" type="checkbox" name="remember">
                        <span>Remember me</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="login-inline-link">Forgot password?</a>
                    @endif
                </div>

                <div class="auth-actions login-actions">
                    <button type="submit" class="btn btn-primary login-primary-btn">Log in</button>

                    <div class="login-secondary-actions">
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-outline login-secondary-btn login-register-btn">Create account</a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </section>
</x-tourism-layout>

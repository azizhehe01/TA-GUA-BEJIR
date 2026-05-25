<?php

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Features;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth.split')] class extends Component {
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        $user = $this->validateCredentials();

        if (! $user->active) {
            session()->put('pending_approval_user', $user->id);
                    
            $this->redirect(route('pending-approval'), navigate: true);
            return;
        }

        if (Features::canManageTwoFactorAuthentication() && $user->hasEnabledTwoFactorAuthentication()) {
            Session::put([
                'login.id' => $user->getKey(),
                'login.remember' => $this->remember,
            ]);

            $this->redirect(route('two-factor.login'), navigate: true);

            return;
        }

        Auth::login($user, $this->remember);

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }

    /**
     * Validate the user's credentials.
     */
    protected function validateCredentials(): User
    {
        $user = Auth::getProvider()->retrieveByCredentials(['email' => $this->email, 'password' => $this->password]);

        if (! $user || ! Auth::getProvider()->validateCredentials($user, ['password' => $this->password])) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        return $user;
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }
}; ?>

<x-slot:aside>
    <div id="robot-area" class="relative z-20 flex h-full items-center justify-center p-8">
        <div id="robot-wrap" class="relative w-72 h-84 perspective-800">
            <style>
                .perspective-800 { perspective: 900px; }
                .robot-scene { transform-style: preserve-3d; width:100%; height:100%; transition: transform 360ms cubic-bezier(.2,.9,.2,1); }
                .robot-layer { transform-style: preserve-3d; position:absolute; left:0; top:0; width:100%; height:100%; }
                .robot-shadow { filter: blur(12px) opacity(.28); transform: translateY(8px) scale(.98); }
                .eye-pupil { transition: transform 120ms linear; transform-origin: center; }
                #robot svg { width:100%; height:100%; display:block; }
                @media (pointer: coarse) { #robot-wrap { pointer-events: none; } }
            </style>

            <!-- soft oval shadow -->
            <svg class="robot-layer robot-shadow" viewBox="0 0 240 240" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <ellipse cx="120" cy="205" rx="70" ry="14" fill="rgba(0,0,0,0.36)"/>
            </svg>

            <!-- Scene -->
            <div id="robot-scene" class="robot-scene robot-layer" aria-hidden="true">
                <svg id="robot" viewBox="0 0 240 240" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Robot">
                    <defs>
                        <linearGradient id="gBody" x1="0" x2="1">
                            <stop offset="0" stop-color="#ffffff"/>
                            <stop offset="1" stop-color="#e9eef6"/>
                        </linearGradient>
                        <linearGradient id="gArmor" x1="0" x2="1">
                            <stop offset="0" stop-color="#f7f7f9"/>
                            <stop offset="1" stop-color="#dfe7ef"/>
                        </linearGradient>
                        <radialGradient id="gChest" cx="50%" cy="40%" r="60%">
                            <stop offset="0" stop-color="#5eead4"/>
                            <stop offset="1" stop-color="#22c1d8"/>
                        </radialGradient>
                        <radialGradient id="gEyeGlow" cx="50%" cy="40%" r="60%">
                            <stop offset="0" stop-color="#fff" stop-opacity="0.95"/>
                            <stop offset="1" stop-color="#fff" stop-opacity="0"/>
                        </radialGradient>
                        <filter id="softBlur" x="-20%" y="-20%" width="140%" height="140%">
                            <feGaussianBlur stdDeviation="1.2" />
                        </filter>
                    </defs>

                    <!-- Body group (slightly lower for parallax) -->
                    <g id="bodyGroup" transform="translate(0,36)">
                        <!-- torso (rounded) -->
                        <ellipse cx="120" cy="160" rx="52" ry="60" fill="url(#gBody)" stroke="#e3eaf2" stroke-width="1.8"/>
                        <!-- bottom glow -->
                        <ellipse cx="120" cy="196" rx="24" ry="6" fill="#d9eef7" opacity="0.7"/>
                        <!-- chest circle -->
                        <circle cx="120" cy="148" r="20" fill="url(#gChest)" stroke="#b6f0e6" stroke-width="1.5"/>
                        <!-- subtle red lines like the PNG -->
                        <path d="M95 160 C110 150,130 150,145 160" stroke="#fb7185" stroke-width="1.4" fill="none" stroke-linecap="round" opacity="0.9"/>
                        <!-- small side arms (rounded) -->
                        <g id="armLeft" transform="translate(-34,0)">
                            <rect x="34" y="132" width="22" height="34" rx="8" fill="url(#gArmor)"/>
                            <circle cx="45" cy="166" r="6" fill="#ffffff"/>
                        </g>
                        <g id="armRight" transform="translate(34,0)">
                            <rect x="184" y="132" width="22" height="34" rx="8" fill="url(#gArmor)"/>
                            <circle cx="200" cy="166" r="6" fill="#ffffff"/>
                        </g>
                    </g>

                    <!-- neck -->
                    <rect x="112" y="96" width="16" height="10" rx="3" fill="#dfe7ef"/>

                    <!-- Head group (this will rotate) -->
                    <g id="headGroup" transform="translate(0,0)" style="transform-origin: 120px 74px;">
                        <!-- head outer (rounded rectangle) -->
                        <rect x="70" y="36" width="100" height="80" rx="18" fill="url(#gArmor)" stroke="#d6dfe9" stroke-width="1.6"/>
                        <!-- top red dot (matching PNG) -->
                        <circle cx="120" cy="26" r="8" fill="#fb7185" stroke="#ffd6dd" stroke-width="0.6"/>
                        <!-- face panel (slightly inset) -->
                        <rect x="86" y="58" width="68" height="44" rx="12" fill="#f4f6fb" />
                        <!-- mouth / smile line like PNG -->
                        <path d="M98 94 C110 102,130 102,142 94" stroke="#fb7185" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" opacity="0.95"/>
                        <!-- eyes background -->
                        <g id="eyesGroup">
                            <ellipse id="eyeLeft" cx="100" cy="76" rx="10" ry="14" fill="#ffffff" />
                            <ellipse id="eyeRight" cx="140" cy="76" rx="10" ry="14" fill="#ffffff" />
                            <!-- pupils (moveable) -->
                            <circle id="pupilLeft" class="eye-pupil" cx="100" cy="76" r="4.2" fill="#222" />
                            <circle id="pupilRight" class="eye-pupil" cx="140" cy="76" r="4.2" fill="#222" />
                            <!-- eye gloss -->
                            <circle cx="96" cy="70" r="6" fill="url(#gEyeGlow)" opacity="0.2"/>
                            <circle cx="136" cy="70" r="6" fill="url(#gEyeGlow)" opacity="0.2"/>
                        </g>
                        <!-- small ear / side caps for shape -->
                        <rect x="62" y="68" width="10" height="18" rx="4" fill="#eef3f9"/>
                        <rect x="168" y="68" width="10" height="18" rx="4" fill="#eef3f9"/>
                    </g>

                    <!-- hover glow / highlight -->
                    <g style="mix-blend-mode:screen; opacity:0.06">
                        <ellipse cx="120" cy="60" rx="60" ry="20" fill="#ffffff"/>
                    </g>
                </svg>
            </div>
        </div>

        <!-- caption -->
        <div class="absolute bottom-8 text-white text-center w-full">
            <div class="text-lg font-semibold">WAZHUB</div>
        </div>
    </div>

    <!-- Interaction script: head rotation + pupils follow -->
    <script>
        (function () {
            const wrap = document.getElementById('robot-wrap');
            const scene = document.getElementById('robot-scene');
            const head = document.getElementById('headGroup');
            const body = document.getElementById('bodyGroup');
            const pupilL = document.getElementById('pupilLeft');
            const pupilR = document.getElementById('pupilRight');

            const maxRotate = 16;
            const pupilRange = 3.6;

            let targetRX = 0, targetRY = 0;
            let currentRX = 0, currentRY = 0;

            // disable on touch devices
            if (('ontouchstart' in window) || navigator.maxTouchPoints > 1) return;

            function onMove(e) {
                const rect = wrap.getBoundingClientRect();
                const cx = rect.left + rect.width / 2;
                const cy = rect.top + rect.height / 2;
                const nx = (e.clientX - cx) / (rect.width / 2);
                const ny = (e.clientY - cy) / (rect.height / 2);

                const mx = Math.max(-1, Math.min(1, nx));
                const my = Math.max(-1, Math.min(1, ny));

                targetRY = mx * maxRotate;         // yaw
                targetRX = -my * maxRotate * 0.6;  // pitch (lesser)

                // pupils move a little
                const px = mx * pupilRange;
                const py = my * pupilRange * 0.85;
                pupilL.setAttribute('transform', `translate(${px}, ${py})`);
                pupilR.setAttribute('transform', `translate(${px}, ${py})`);
            }

            function rafLoop() {
                currentRX += (targetRX - currentRX) * 0.12;
                currentRY += (targetRY - currentRY) * 0.12;

                // apply subtle 3D tilt to whole scene
                scene.style.transform = `rotateX(${currentRX}deg) rotateY(${currentRY}deg)`;

                // rotate head group around visual center (120,74)
                head.setAttribute('transform', `translate(0,0) rotate(${currentRY * 0.9} 120 74)`);

                // body parallax (opposite tiny)
                body.setAttribute('transform', `translate(${ -currentRY * 0.25 },${ currentRX * 0.6 })`);

                requestAnimationFrame(rafLoop);
            }

            function onLeave() {
                targetRX = 0; targetRY = 0;
                pupilL.setAttribute('transform', 'translate(0,0)');
                pupilR.setAttribute('transform', 'translate(0,0)');
            }

            window.addEventListener('mousemove', onMove);
            window.addEventListener('mouseleave', onLeave);
            wrap.addEventListener('mouseenter', () => { /* could add scale effect */ });

            rafLoop();
        })();
    </script>
</x-slot:aside>



<div class="flex flex-col justify-center flex-1 h-full">
    <div class="w-full max-w-sm mx-auto">
        <x-auth-header :title="__('Log in to your account')" :description="__('Enter your email and password below to log in')" />

        <!-- Session Status -->
        <x-auth-session-status class="mt-6 text-center" :status="session('status')" />

        <form method="POST" wire:submit="login" class="flex flex-col gap-6 mt-6">
            <!-- Email Address -->
            <flux:input
                wire:model="email"
                :label="__('Email address')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="email@example.com"
            />

            <!-- Password -->
            <div class="relative">
                <flux:input
                    wire:model="password"
                    :label="__('Password')"
                    type="password"
                    required
                    autocomplete="current-password"
                    :placeholder="__('Password')"
                    viewable
                />

                @if (Route::has('password.request'))
                    <flux:link class="absolute top-0 text-sm end-0" :href="route('password.request')" wire:navigate>
                        {{ __('Forgot your password?') }}
                    </flux:link>
                @endif
            </div>

            <!-- Remember Me -->
            <flux:checkbox wire:model="remember" :label="__('Remember me')" />

            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit" class="w-full" data-test="login-button">
                    {{ __('Log in') }}
                </flux:button>
            </div>
        </form>

        @if (Route::has('register'))
            <div class="mt-6 space-x-1 text-sm text-center rtl:space-x-reverse text-zinc-600 dark:text-zinc-400">
                <span>{{ __("Don't have an account?") }}</span>
                <flux:link :href="route('register')" wire:navigate>{{ __('Sign up') }}</flux:link>
            </div>
        @endif
    </div>
</div>
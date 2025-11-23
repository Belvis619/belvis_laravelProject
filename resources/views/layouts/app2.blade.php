<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
  @include('partials.head')
</head>
<body class="min-h-screen flex bg-white dark:bg-zinc-800">

  <flux:sidebar sticky stashable class="min-h-screen border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
    <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

    <a href="{{ route('home') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
      <x-app-logo />
    </a>

    <flux:navlist variant="outline">
      <flux:navlist.group :heading="__('Donation Tracking')" class="grid">
        <flux:navlist.item :href="route('home')" :current="request()->routeIs('home')" wire:navigate>
          {{ __('Dashboard') }}
        </flux:navlist.item>

        <flux:navlist.item icon="tag" :href="route('donation-types.index')" :current="request()->routeIs('donation-types.*')" wire:navigate>
          {{ __('Donation Types') }}
        </flux:navlist.item>
      </flux:navlist.group>
    </flux:navlist>

    <flux:spacer />

    {{-- Authenticated user menu --}}
    @auth
      <flux:dropdown class="hidden lg:block" position="bottom" align="start">
        <flux:profile
          :name="auth()->user()?->name ?? 'User'"
          :initials="auth()->user()?->initials() ?? 'NA'"
          icon:trailing="chevrons-up-down"
        />

        <flux:menu class="w-[220px]">
          <flux:menu.radio.group>
            <div class="p-0 text-sm font-normal">
              <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                  <span class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                    {{ auth()->user()?->initials() ?? 'NA' }}
                  </span>
                </span>

                <div class="grid flex-1 text-start text-sm leading-tight">
                  <span class="truncate font-semibold">{{ auth()->user()?->name ?? 'User' }}</span>
                  <span class="truncate text-xs">{{ auth()->user()?->email ?? '' }}</span>
                </div>
              </div>
            </div>
          </flux:menu.radio.group>

          <flux:menu.separator />

          <flux:menu.radio.group>
            <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
          </flux:menu.radio.group>

          <flux:menu.separator />

          <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
              {{ __('Log Out') }}
            </flux:menu.item>
          </form>
        </flux:menu>
      </flux:dropdown>
    @else
      {{-- Guest quick links (desktop) --}}
      <div class="hidden lg:block p-4">
        <a href="{{ route('login') }}" class="block py-2 text-sm">{{ __('Sign in') }}</a>
        @if (Route::has('register'))
          <a href="{{ route('register') }}" class="block py-2 text-sm">{{ __('Register') }}</a>
        @endif
      </div>
    @endauth

  </flux:sidebar>

  <!-- Mobile header / user menu -->
  <flux:header class="lg:hidden">
    <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
    <flux:spacer />

    <flux:dropdown position="top" align="end">
      @auth
        <flux:profile :initials="auth()->user()?->initials() ?? 'NA'" icon-trailing="chevron-down" />
        <flux:menu>
          <flux:menu.radio.group>
            <div class="p-0 text-sm font-normal">
              <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                  <span class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                    {{ auth()->user()?->initials() ?? 'NA' }}
                  </span>
                </span>

                <div class="grid flex-1 text-start text-sm leading-tight">
                  <span class="truncate font-semibold">{{ auth()->user()?->name ?? 'User' }}</span>
                  <span class="truncate text-xs">{{ auth()->user()?->email ?? '' }}</span>
                </div>
              </div>
            </div>
          </flux:menu.radio.group>

          <flux:menu.separator />
          <flux:menu.radio.group>
            <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
          </flux:menu.radio.group>

          <flux:menu.separator />
          <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">{{ __('Log Out') }}</flux:menu.item>
          </form>
        </flux:menu>
      @else
        <flux:menu>
          <flux:menu.item :href="route('login')" wire:navigate>{{ __('Sign in') }}</flux:menu.item>
          @if (Route::has('register'))
            <flux:menu.item :href="route('register')" wire:navigate>{{ __('Register') }}</flux:menu.item>
          @endif
        </flux:menu>
      @endauth
    </flux:dropdown>
  </flux:header>

  {{-- page content --}}
  @yield('content')

  @fluxScripts
  <script src="https://unpkg.com/alpinejs@3.12.0/dist/cdn.min.js" defer></script>
</body>
</html>
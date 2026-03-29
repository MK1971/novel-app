{{--
  Desktop: position fixed (not sticky) — always pinned left under the bar; no flex/ancestor bugs.
  Mobile: hidden here; hamburger drawer in app layout.
  top/h height match sticky nav (~py-4 + one line). Tweak --app-shell-nav-h in app/guest layout if bar height changes.
--}}
<aside
    class="hidden md:flex md:flex-col fixed left-0 z-30 overflow-y-auto border-r border-amber-200/60 bg-white/50 backdrop-blur-sm"
    style="top: var(--app-shell-nav-h, 4.5rem); width: var(--app-shell-rail-w, 18rem); min-width: var(--app-shell-rail-w, 18rem); height: calc(100dvh - var(--app-shell-nav-h, 4.5rem)); max-height: calc(100dvh - var(--app-shell-nav-h, 4.5rem));"
>
    @include('layouts.partials.sidebar-inner')
</aside>

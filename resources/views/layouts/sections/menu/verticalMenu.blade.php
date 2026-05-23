@php
use App\Support\SispaqMenu;
use Illuminate\Support\Facades\Route;

$configData = Helper::appClasses();
$user = auth()->user();
$role = $user?->role ?? 'guest';
$menuItems = SispaqMenu::itemsForRole($role);
@endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu" @foreach ($configData['menuAttributes'] as $attribute=> $value)
  {{ $attribute }}="{{ $value }}" @endforeach>

  @if (!isset($navbarFull))
  <div class="app-brand demo py-3">
    <a href="{{ url('/') }}" class="app-brand-link">
      @include('_partials.um-brand')
    </a>

    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
      <i class="icon-base ti menu-toggle-icon d-none d-xl-block"></i>
      <i class="icon-base ti tabler-x d-block d-xl-none"></i>
    </a>
  </div>
  @endif

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">
    @foreach ($menuItems as $item)
      @if (!SispaqMenu::isVisible($item['visible_when'] ?? null, $user))
        @continue
      @endif

      @if (($item['type'] ?? 'link') === 'header')
        <li class="menu-header small">
          <span class="menu-header-text">{{ $item['label'] }}</span>
        </li>
      @else
        @php
          $params = $item['route_params'] ?? [];
          $href = Route::has($item['route']) ? route($item['route'], $params) : '#';
          $isActive = SispaqMenu::isActive($item);
        @endphp
        <li class="menu-item {{ $isActive ? 'active' : '' }}">
          <a href="{{ $href }}" class="menu-link">
            <i class="menu-icon icon-base ti {{ $item['icon'] }}"></i>
            <div>{{ $item['label'] }}</div>
          </a>
        </li>
      @endif
    @endforeach
  </ul>

  <div class="px-4 py-3 mt-2 border-top">
    <p class="small text-body-secondary mb-0 lh-sm text-center">
      &#169;
      <script>document.write(new Date().getFullYear());</script>
      {{ __('sispaq.footer.copyright') }}
    </p>
  </div>

</aside>

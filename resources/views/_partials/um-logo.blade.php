@props(['height' => 36, 'class' => ''])

<img
  src="{{ asset('assets/img/branding/um-logo.png') }}"
  alt="Universiti Malaya"
  class="{{ $class }}"
  style="height: {{ $height }}px; width: auto; object-fit: contain;"
/>

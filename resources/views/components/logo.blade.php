@props(['size' => 44])

<svg {{ $attributes }} width="{{ $size }}" height="{{ $size }}" viewBox="0 0 44 44" role="img" aria-label="JD">
    <rect x="1" y="1" width="42" height="42" fill="var(--color-bg)" stroke="var(--color-text)" stroke-width="2"></rect>
    <text x="7" y="29" font-family="Archivo, system-ui, sans-serif" font-weight="800" font-size="19"
          letter-spacing="-0.5" fill="var(--color-text)">JD</text>
    <rect x="32" y="24" width="5" height="5" fill="var(--color-accent)"></rect>
</svg>

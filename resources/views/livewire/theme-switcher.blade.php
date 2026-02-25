<div class="fab">
  <div tabindex="0" role="button" class="btn btn-lg btn-circle btn-info">F</div>

  <div class="fab-close">
    Tutup <span class="btn btn-circle btn-lg btn-error">✕</span>
  </div>

  @foreach($themes as $theme)
    <div>
        <span class="capitalize">{{ $theme }}</span>
        <button
            wire:click="setTheme('{{ $theme }}')"
            class="btn btn-lg btn-circle {{ session('theme') === $theme ? 'btn-primary' : '' }}"
        >
            {{ strtoupper(substr($theme, 0, 1)) }}
        </button>
    </div>
  @endforeach
</div>

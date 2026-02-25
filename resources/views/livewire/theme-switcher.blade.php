<div class="dropdown dropdown-end">
    <div tabindex="0" role="button" class="gap-2 normal-case btn btn-ghost btn-sm">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.098 19.903a9.75 9.75 0 0 1 15.804-11.703M11.446 13.09c.053.013.107.02.162.02.552 0 1-.448 1-1 0-.055-.007-.11-.02-.162m-1.246.828a2.25 2.25 0 1 1-3.483-2.503 2.25 2.25 0 0 1 3.483 2.503Z" />
        </svg>
        <span>Tema: {{ session('theme', 'maybecity') }}</span>
        <svg width="12px" height="12px" class="inline-block w-2 h-2 fill-current opacity-60" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 2048 2048"><path d="M1799 349l242 241-1017 1017L7 590l242-241 775 775 775-775z"></path></svg>
    </div>
    <ul tabindex="0" class="dropdown-content z-[100] menu p-2 shadow-2xl bg-base-300 rounded-box w-52 mt-4 border border-white/10">
        @foreach($themes as $theme)
            <li>
                <button
                    wire:click="setTheme('{{ $theme }}')"
                    class="{{ session('theme') === $theme ? 'active' : '' }} capitalize"
                >
                    {{ $theme }}
                </button>
            </li>
        @endforeach
    </ul>
</div>

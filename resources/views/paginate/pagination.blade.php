@if ($paginator->hasPages())
    <div class="flex flex-col sm:flex-row items-center justify-between px-4 py-2 gap-2">
        
        {{-- Info text --}}
        <div class="text-xs text-gray-600">
            Showing
            <span class="font-semibold">{{ $paginator->firstItem() }}</span>
            to
            <span class="font-semibold">{{ $paginator->lastItem() }}</span>
            of
            <span class="font-semibold">{{ $paginator->total() }}</span>
            results
        </div>

        {{-- Pagination --}}
        <div class="join">
            {{-- Previous Page --}}
            @if ($paginator->onFirstPage())
                <button class="btn btn-xs join-item btn-disabled">«</button>
            @else
                <button wire:click="previousPage" type="button" class="btn btn-xs join-item">«</button>
            @endif

            {{-- Page Numbers --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <button class="btn btn-xs join-item btn-disabled">…</button>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <button class="btn btn-xs join-item btn-active">{{ $page }}</button>
                        @else
                            <button wire:click="gotoPage({{ $page }})" type="button" class="btn btn-xs join-item">{{ $page }}</button>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page --}}
            @if ($paginator->hasMorePages())
                <button wire:click="nextPage" type="button" class="btn btn-xs join-item">»</button>
            @else
                <button class="btn btn-xs join-item btn-disabled">»</button>
            @endif
        </div>
    </div>
@endif

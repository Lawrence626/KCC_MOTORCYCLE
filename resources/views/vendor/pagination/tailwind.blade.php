@if ($paginator->total() > 0)
    <div class="border-t border-slate-200 px-6 py-4 flex items-center justify-between">
        <p class="text-sm text-slate-600">Showing {{ $paginator->firstItem() ?? 0 }} - {{ $paginator->lastItem() ?? 0 }} of {{ $paginator->total() }} entries</p>
        @if ($paginator->hasPages())
            <nav role="navigation" aria-label="Pagination Navigation">
                <ul class="inline-flex items-center gap-2">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <li>
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-[10px] bg-white border border-slate-200 text-slate-400">&lsaquo; Previous</span>
                        </li>
                    @else
                        <li>
                            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center gap-1 px-3 py-1 rounded-[10px] bg-white border border-slate-200 text-slate-600 hover:bg-slate-50">&lsaquo; Previous</a>
                        </li>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <li><span class="inline-flex items-center px-3 py-1 rounded-[10px] bg-white border border-slate-200 text-slate-400">{{ $element }}</span></li>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                        <li aria-current="page"><span class="inline-flex items-center px-3 py-1 rounded-[10px] bg-black/10 text-slate-600 font-semibold shadow-sm">{{ $page }}</span></li>
                                    @else
                                        <li><a href="{{ $url }}" class="inline-flex items-center px-3 py-1 rounded-[10px] bg-white border border-slate-200 text-slate-600 hover:bg-slate-50">{{ $page }}</a></li>
                                    @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <li>
                            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center gap-1 px-3 py-1 rounded-[10px] bg-white border border-slate-200 text-slate-600 hover:bg-slate-50">Next &rsaquo;</a>
                        </li>
                    @else
                        <li>
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-[10px] bg-white border border-slate-200 text-slate-400">Next &rsaquo;</span>
                        </li>
                    @endif
                </ul>
            </nav>
        @endif
    </div>
@endif

@if ($paginator->total() > 0)
    <div class="flex items-center justify-between border-t border-slate-200 bg-slate-50 px-3 py-2 text-xs">
        <p class="text-slate-600">Showing {{ $paginator->firstItem() ?? 0 }}-{{ $paginator->lastItem() ?? 0 }} of {{ $paginator->total() }} entries</p>
        @if ($paginator->hasPages())
            <nav role="navigation" aria-label="Pagination Navigation">
                <ul class="inline-flex items-center gap-1">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <li>
                            <span class="inline-flex items-center justify-center h-8 rounded-[10px] border border-slate-300 bg-white px-2.5 text-xs font-semibold text-slate-700 opacity-50 cursor-not-allowed">← Prev</span>
                        </li>
                    @else
                        <li>
                            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center justify-center h-8 rounded-[10px] border border-slate-300 bg-white px-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">← Prev</a>
                        </li>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <li><span class="inline-flex items-center justify-center rounded-[10px] border border-slate-300 bg-white text-slate-400 w-8 h-8 text-xs font-semibold">{{ $element }}</span></li>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <li aria-current="page"><span class="inline-flex items-center justify-center rounded-[10px] bg-black/10 text-slate-900 w-8 h-8 text-xs font-semibold">{{ $page }}</span></li>
                                @else
                                    <li><a href="{{ $url }}" class="inline-flex items-center justify-center rounded-[10px] border border-slate-300 bg-white text-slate-700 w-8 h-8 text-xs font-semibold hover:bg-slate-50 transition">{{ $page }}</a></li>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <li>
                            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center justify-center h-8 rounded-[10px] border border-slate-300 bg-white px-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">Next →</a>
                        </li>
                    @else
                        <li>
                            <span class="inline-flex items-center justify-center h-8 rounded-[10px] border border-slate-300 bg-white px-2.5 text-xs font-semibold text-slate-700 opacity-50 cursor-not-allowed">Next →</span>
                        </li>
                    @endif
                </ul>
            </nav>
        @endif
    </div>
@endif

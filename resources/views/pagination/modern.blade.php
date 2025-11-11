@if ($paginator->hasPages())
    <div class="modern-pagination">

        {{-- Liens de pagination --}}
        <div class="pagination-links">
            {{-- Lien précédent --}}
            @if ($paginator->onFirstPage())
                <span class="pagination-btn pagination-disabled">
                    <i class="fas fa-chevron-left"></i>
                    Précédent
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="pagination-btn pagination-prev">
                    <i class="fas fa-chevron-left"></i>
                    Précédent
                </a>
            @endif

            {{-- Numéros de page --}}
            <div class="pagination-numbers">
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <span class="pagination-dots">{{ $element }}</span>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="pagination-number pagination-active">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="pagination-number">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

            {{-- Lien suivant --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="pagination-btn pagination-next">
                    Suivant
                    <i class="fas fa-chevron-right"></i>
                </a>
            @else
                <span class="pagination-btn pagination-disabled">
                    Suivant
                    <i class="fas fa-chevron-right"></i>
                </span>
            @endif
        </div>
    </div>
@endif

<style>
    /* Pagination moderne */
.modern-pagination {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 30px;
    padding: 20px 0;
    border-top: 1px solid #e9ecef;
}

.pagination-info {
    flex: 1;
}

.pagination-text {
    color: #6c757d;
    font-size: 0.9rem;
}

.pagination-text strong {
    color: #333;
}

.pagination-links {
    display: flex;
    align-items: center;
    gap: 10px;
}

.pagination-numbers {
    display: flex;
    gap: 5px;
}

.pagination-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    border: 1px solid #fea219;
    border-radius: 8px;
    color: #fea219;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    background: white;
}

.pagination-btn:hover {
    background: #fea219;
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(254, 162, 25, 0.3);
}

.pagination-disabled {
    background: #f8f9fa;
    border-color: #dee2e6;
    color: #6c757d;
    cursor: not-allowed;
}

.pagination-disabled:hover {
    background: #f8f9fa;
    color: #6c757d;
    transform: none;
    box-shadow: none;
}

.pagination-number {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 40px;
    height: 40px;
    padding: 0 12px;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    color: #333;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    background: white;
}

.pagination-number:hover {
    border-color: #fea219;
    color: #fea219;
    transform: translateY(-1px);
}

.pagination-active {
    background: #fea219;
    border-color: #fea219;
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(254, 162, 25, 0.3);
}

.pagination-dots {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 40px;
    height: 40px;
    color: #6c757d;
}

/* Responsive */
@media (max-width: 768px) {
    .modern-pagination {
        flex-direction: column;
        gap: 20px;
        text-align: center;
    }

    .pagination-links {
        flex-wrap: wrap;
        justify-content: center;
    }

    .pagination-numbers {
        order: -1;
        width: 100%;
        justify-content: center;
        margin-bottom: 10px;
    }

    .pagination-btn {
        padding: 6px 12px;
        font-size: 0.9rem;
    }

    .pagination-number {
        min-width: 35px;
        height: 35px;
        font-size: 0.9rem;
    }
}

@media (max-width: 480px) {
    .pagination-numbers {
        flex-wrap: wrap;
    }

    .pagination-info {
        text-align: center;
    }
}
</style>
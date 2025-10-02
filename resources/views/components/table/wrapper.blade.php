@props([
    'id' => 'data-table',
    'searchable' => true,
    'searchInputId' => 'search-input',
    'headers' => [],
    'emptyMessage' => 'Tidak ada data tersedia',
    'createButton' => null
])

<!-- Kontrol Searching dan Create -->
@if($searchable || $createButton)
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4">
    <div class="p-4">
        <div class="flex items-center justify-between gap-4">
            @if($searchable)
                <div class="flex-1 max-w-md">
                    <div class="relative">
                        <input type="text" 
                               id="{{ $searchInputId }}"
                               placeholder="Cari data..."
                               class="block w-full pl-10 pr-10 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <x-heroicon-o-magnifying-glass class="h-5 w-5 text-gray-400" />
                        </div>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <button type="button" 
                                    id="clear-search"
                                    class="text-gray-400 hover:text-gray-600 focus:outline-none hidden">
                                <x-heroicon-o-x-mark class="h-5 w-5" />
                            </button>
                        </div>
                    </div>
                </div>
            @else
                <div class="flex-1"></div>
            @endif

            <div class="flex items-center gap-4">
                @if($searchable)
                    <span id="results-counter" class="text-sm text-gray-500"></span>
                @endif
                
                @if($createButton)
                    {!! $createButton !!}
                @endif
            </div>
        </div>
    </div>
</div>
@endif

<!-- Table -->
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="overflow-x-auto">
        <table id="{{ $id }}" class="min-w-full divide-y divide-gray-200">
            @if(count($headers) > 0)
                <thead class="bg-gray-50">
                    <tr>
                        @foreach($headers as $header)
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ $header }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
            @endif
            
            <tbody class="bg-white divide-y divide-gray-200">
                {{ $slot }}
            </tbody>
        </table>
    </div>

    <!-- State Kosong -->
    <div id="empty-state" class="hidden p-8 text-center">
        <x-heroicon-o-document-text class="mx-auto h-12 w-12 text-gray-400" />
        <h3 class="mt-2 text-sm font-medium text-gray-900">{{ $emptyMessage }}</h3>
    </div>
</div>

@if($searchable)
@push('scripts')
<script>
// Live search untuk tabel {{ $id }}
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('{{ $searchInputId }}');
    const table = document.getElementById('{{ $id }}');
    const clearBtn = document.getElementById('clear-search');
    const emptyState = document.getElementById('empty-state');

    if (!table || !searchInput) {
        return;
    }

    const getRows = () => Array.from(table.querySelectorAll('tbody tr'));

    const handleSearch = () => {
        const rows = getRows();
        const searchTerm = searchInput.value.toLowerCase().trim();
        let visibleCount = 0;

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const shouldShow = searchTerm === '' || text.includes(searchTerm);

            row.style.display = shouldShow ? '' : 'none';
            if (shouldShow) {
                visibleCount++;
            }
        });

        updateResultsCounter(visibleCount, rows.length);

        if (clearBtn) {
            clearBtn.classList.toggle('hidden', searchTerm === '');
        }

        if (emptyState) {
            const noRows = rows.length === 0;
            emptyState.style.display = noRows || (visibleCount === 0 && searchTerm !== '') ? 'block' : 'none';
        }
    };

    searchInput.addEventListener('input', handleSearch);

    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            searchInput.value = '';
            handleSearch();
            searchInput.focus();
        });
    }

    document.addEventListener('bahan-baku:table-updated', handleSearch);
    table.addEventListener('table:refresh', handleSearch);

    handleSearch();

    function updateResultsCounter(visible, total) {
        const counter = document.getElementById('results-counter');
        if (!counter) {
            return;
        }

        if (total === 0) {
            counter.textContent = '0 data';
            return;
        }

        counter.textContent = visible === total
            ? `${total} data`
            : `${visible} dari ${total} data`;
    }
});
</script>
@endpush
@endif
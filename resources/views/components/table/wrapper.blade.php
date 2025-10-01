@props([
    'id' => 'data-table',
    'searchable' => true,
    'searchInputId' => 'search-input',
    'headers' => [],
    'emptyMessage' => 'Tidak ada data tersedia'
])

<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    @if($searchable)
        <!-- Search Bar -->
        <div class="p-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex-1 max-w-lg">
                    <div class="relative">
                        <input type="text" 
                               id="{{ $searchInputId }}"
                               placeholder="Cari data..."
                               class="block w-full pl-10 pr-10 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <button type="button" 
                                    id="clear-search"
                                    class="text-gray-400 hover:text-gray-600 focus:outline-none hidden">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="ml-4">
                    <span id="results-counter" class="text-sm text-gray-500"></span>
                </div>
            </div>
        </div>
    @endif

    <!-- Table -->
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

    <!-- Empty State -->
    <div id="empty-state" class="hidden p-8 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
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
    const rows = table ? table.querySelectorAll('tbody tr') : [];
    
    if (searchInput && rows.length > 0) {
        // Update counter awal
        updateResultsCounter(rows.length, rows.length);
        
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            let visibleCount = 0;
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const shouldShow = searchTerm === '' || text.includes(searchTerm);
                
                row.style.display = shouldShow ? '' : 'none';
                if (shouldShow) visibleCount++;
            });
            
            // Update counter
            updateResultsCounter(visibleCount, rows.length);
            
            // Show/hide clear button
            if (clearBtn) {
                clearBtn.classList.toggle('hidden', searchTerm === '');
            }
            
            // Show empty state jika tidak ada hasil
            const emptyState = document.getElementById('empty-state');
            if (emptyState) {
                emptyState.style.display = (visibleCount === 0 && searchTerm !== '') ? 'block' : 'none';
            }
        });
        
        // Clear search functionality
        if (clearBtn) {
            clearBtn.addEventListener('click', function() {
                searchInput.value = '';
                searchInput.dispatchEvent(new Event('input'));
                searchInput.focus();
            });
        }
    }
    
    function updateResultsCounter(visible, total) {
        const counter = document.getElementById('results-counter');
        if (counter) {
            if (visible === total) {
                counter.textContent = `${total} data`;
            } else {
                counter.textContent = `${visible} dari ${total} data`;
            }
        }
    }
});
</script>
@endpush
@endif
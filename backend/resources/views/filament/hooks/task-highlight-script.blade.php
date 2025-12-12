<script>
    (function() {
        const STORAGE_KEY = 'filament_tasks_table_state';
        
        function saveTableState() {
            const urlParams = new URLSearchParams(window.location.search);
            const state = {};
            
            urlParams.forEach((value, key) => {
                if (key.startsWith('table')) {
                    state[key] = value;
                }
            });
            
            if (typeof Livewire !== 'undefined') {
                const livewireComponents = Livewire.all();
                for (const component of livewireComponents) {
                    if (component.$wire && component.$wire.get && typeof component.$wire.get === 'function') {
                        try {
                            const tableRecords = component.$wire.get('tableRecords');
                            if (tableRecords !== undefined) {
                                state.tableRecords = tableRecords;
                            }
                            
                            const tableSearch = component.$wire.get('tableSearch');
                            if (tableSearch !== undefined) {
                                state.tableSearch = tableSearch;
                            }
                            
                            const tableFilters = component.$wire.get('tableFilters');
                            if (tableFilters !== undefined && Object.keys(tableFilters).length > 0) {
                                state.tableFilters = JSON.stringify(tableFilters);
                            }
                            
                            const tableSortColumn = component.$wire.get('tableSortColumn');
                            if (tableSortColumn !== undefined) {
                                state.tableSortColumn = tableSortColumn;
                            }
                            
                            const tableSortDirection = component.$wire.get('tableSortDirection');
                            if (tableSortDirection !== undefined) {
                                state.tableSortDirection = tableSortDirection;
                            }
                        } catch (e) {
                        }
                    }
                }
            }
            
            if (Object.keys(state).length > 0) {
                localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
            }
        }
        
        function restoreTableState() {
            const savedState = localStorage.getItem(STORAGE_KEY);
            if (!savedState) return false;
            
            try {
                const state = JSON.parse(savedState);
                const urlParams = new URLSearchParams(window.location.search);
                let hasChanges = false;
                
                for (const [key, value] of Object.entries(state)) {
                    if (key === 'tableFilters') {
                        try {
                            const filters = JSON.parse(value);
                            if (typeof Livewire !== 'undefined') {
                                const livewireComponents = Livewire.all();
                                for (const component of livewireComponents) {
                                    if (component.$wire && component.$wire.set && typeof component.$wire.set === 'function') {
                                        component.$wire.set('tableFilters', filters);
                                        hasChanges = true;
                                    }
                                }
                            }
                        } catch (e) {
                        }
                    } else if (!urlParams.has(key)) {
                        urlParams.set(key, value);
                        hasChanges = true;
                    }
                }
                
                if (hasChanges) {
                    const newUrl = window.location.pathname + '?' + urlParams.toString();
                    window.history.replaceState({}, '', newUrl);
                    
                    if (typeof Livewire !== 'undefined') {
                        setTimeout(function() {
                            Livewire.visit(newUrl, { 
                                preserveScroll: true,
                                preserveState: true 
                            });
                        }, 100);
                        return true;
                    }
                }
            } catch (e) {
                console.error('Error restoring table state:', e);
            }
            
            return false;
        }
        
        const urlParams = new URLSearchParams(window.location.search);
        const highlightId = urlParams.get('highlight');
        
        if (highlightId) {
            const restored = restoreTableState();
            
            function highlightRow() {
                let row = null;
                
                const rows = document.querySelectorAll('tbody tr, [role="row"]');
                for (let i = 0; i < rows.length; i++) {
                    const rowElement = rows[i];
                    const editLink = rowElement.querySelector('a[href*="/' + highlightId + '/edit"], button[href*="/' + highlightId + '/edit"]');
                    if (editLink) {
                        row = editLink.closest('tr, [role="row"]');
                        break;
                    }
                    
                    const wireKey = rowElement.getAttribute('wire:key');
                    if (wireKey && wireKey.includes(highlightId)) {
                        row = rowElement;
                        break;
                    }
                }
                
                if (row) {
                    row.id = 'task-row-' + highlightId;
                    
                    setTimeout(function() {
                        const targetRow = document.getElementById('task-row-' + highlightId);
                        if (targetRow) {
                            targetRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            targetRow.style.transition = 'all 0.3s ease';
                            targetRow.style.backgroundColor = 'rgba(251, 191, 36, 0.2)';
                            targetRow.style.boxShadow = '0 0 0 2px rgba(251, 191, 36, 0.5)';
                            
                            setTimeout(function() {
                                if (targetRow) {
                                    targetRow.style.transition = 'all 0.5s ease';
                                    targetRow.style.backgroundColor = '';
                                    targetRow.style.boxShadow = '';
                                }
                                
                                const url = new URL(window.location);
                                url.searchParams.delete('highlight');
                                window.history.replaceState({}, '', url);
                                localStorage.removeItem(STORAGE_KEY);
                            }, 3000);
                        }
                    }, 100);
                }
            }
            
            if (restored) {
                if (typeof Livewire !== 'undefined') {
                    document.addEventListener('livewire:navigated', function() {
                        setTimeout(highlightRow, 500);
                    });
                }
            } else {
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', function() {
                        setTimeout(highlightRow, 500);
                    });
                } else {
                    setTimeout(highlightRow, 500);
                }
                
                if (typeof Livewire !== 'undefined') {
                    document.addEventListener('livewire:load', function() {
                        setTimeout(highlightRow, 300);
                    });
                    
                    if (Livewire.hook) {
                        Livewire.hook('morph.updated', function() {
                            setTimeout(highlightRow, 200);
                        });
                    }
                }
            }
        } else {
            document.addEventListener('click', function(e) {
                const editLink = e.target.closest('a[href*="/edit"], button[href*="/edit"], [wire\\:click*="edit"]');
                if (editLink) {
                    const href = editLink.href || editLink.getAttribute('href') || '';
                    if (href.includes('/tasks/') && href.includes('/edit')) {
                        saveTableState();
                    } else if (editLink.getAttribute('wire:click') && editLink.getAttribute('wire:click').includes('edit')) {
                        saveTableState();
                    }
                }
            }, true);
            
            if (typeof Livewire !== 'undefined') {
                document.addEventListener('livewire:navigating', function() {
                    saveTableState();
                });
            }
        }
    })();
</script>

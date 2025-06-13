// Archivo JavaScript principal

document.addEventListener('DOMContentLoaded', function() {
    // Añadir campo de búsqueda sobre la tabla
    const tableContainer = document.querySelector('.table-responsive');
    if (tableContainer) {
        // Crear el campo de búsqueda
        const searchContainer = document.createElement('div');
        searchContainer.className = 'mb-3';
        searchContainer.innerHTML = `
            <div class="input-group">
                <input type="text" id="searchInput" class="form-control" placeholder="Buscar en la tabla...">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                        <i class="fa fa-times"></i> Limpiar
                    </button>
                </div>
            </div>
        `;
        
        // Insertar el campo de búsqueda antes de la tabla
        tableContainer.parentNode.insertBefore(searchContainer, tableContainer);
        
        // Funcionalidad de búsqueda
        const searchInput = document.getElementById('searchInput');
        const clearSearch = document.getElementById('clearSearch');
        const table = tableContainer.querySelector('table');
        
        if (searchInput && table) {
            searchInput.addEventListener('keyup', function() {
                const searchText = this.value.toLowerCase();
                const rows = table.querySelectorAll('tbody tr');
                
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    if (text.includes(searchText)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
            
            // Limpiar búsqueda
            if (clearSearch) {
                clearSearch.addEventListener('click', function() {
                    searchInput.value = '';
                    const event = new Event('keyup');
                    searchInput.dispatchEvent(event);
                });
            }
        }
        
        // Funcionalidad para ordenar la tabla
        const headers = table.querySelectorAll('thead th');
        headers.forEach((header, index) => {
            // Añadir cursor pointer y un indicador de que se puede ordenar
            header.style.cursor = 'pointer';
            header.title = 'Haz clic para ordenar';
            
            // Añadir evento de clic
            header.addEventListener('click', function() {
                sortTable(table, index);
            });
        });
    }
});

// Función para ordenar la tabla
function sortTable(table, columnIndex) {
    const rows = Array.from(table.querySelectorAll('tbody tr'));
    const direction = table.getAttribute('data-sort-direction') === 'asc' ? 'desc' : 'asc';
    
    // Guardar la dirección actual
    table.setAttribute('data-sort-direction', direction);
    table.setAttribute('data-sort-column', columnIndex);
    
    // Ordenar las filas
    rows.sort((a, b) => {
        const cellA = a.cells[columnIndex].textContent.trim();
        const cellB = b.cells[columnIndex].textContent.trim();
        
        // Intentar convertir a números si es posible
        const numA = parseFloat(cellA);
        const numB = parseFloat(cellB);
        
        if (!isNaN(numA) && !isNaN(numB)) {
            return direction === 'asc' ? numA - numB : numB - numA;
        } else {
            return direction === 'asc' 
                ? cellA.localeCompare(cellB, 'es', {sensitivity: 'base'}) 
                : cellB.localeCompare(cellA, 'es', {sensitivity: 'base'});
        }
    });
    
    // Actualizar la tabla con las filas ordenadas
    const tbody = table.querySelector('tbody');
    rows.forEach(row => tbody.appendChild(row));
    
    // Actualizar los indicadores de ordenamiento en los encabezados
    const headers = table.querySelectorAll('thead th');
    headers.forEach((header, index) => {
        // Eliminar cualquier indicador existente
        header.classList.remove('sorted-asc', 'sorted-desc');
        
        // Añadir el indicador apropiado al encabezado actual
        if (index == columnIndex) {
            header.classList.add(direction === 'asc' ? 'sorted-asc' : 'sorted-desc');
        }
    });
}

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
    
    // Manejar clics en las filas de productos para mostrar el modal
    const productRows = document.querySelectorAll('.product-row');
    productRows.forEach(row => {
        row.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            const productName = this.getAttribute('data-product-name');
            const productCode = this.getAttribute('data-product-code');
            
            // Actualizar la información del producto en el modal
            document.getElementById('productName').textContent = productName;
            document.getElementById('productCode').textContent = `Código: ${productCode}`;
            
            // Limpiar la tabla de cajas
            const boxesTableBody = document.querySelector('#boxesTable tbody');
            boxesTableBody.innerHTML = '';
            
            // Mostrar indicador de carga
            boxesTableBody.innerHTML = '<tr><td colspan="3" class="text-center">Cargando información de cajas...</td></tr>';
            
            // Mostrar el modal
            $('#productDetailsModal').modal('show');
            
            // Obtener los datos de las cajas para este producto
            fetchBoxesData(productId);
        });
    });
});

// Función para obtener los datos de las cajas de un producto
function fetchBoxesData(productId) {
    // Usamos nuestro endpoint local que actúa como proxy para evitar problemas de CORS
    const apiUrl = `api/product-boxes.php?productId=${productId}`;
    
    fetch(apiUrl)
        .then(response => {
            if (!response.ok) {
                throw new Error('Error al obtener datos del producto');
            }
            return response.json();
        })
        .then(productDetails => {
            // Verificamos si el producto tiene cajas asociadas
            if (productDetails && productDetails.boxes && productDetails.boxes.length > 0) {
                // Transformamos los datos para que coincidan con nuestra estructura de tabla
                const boxesData = productDetails.boxes.map(box => ({
                    boxNumber: box.boxCode,
                    location: `Box ID: ${box.boxId}`, // No tenemos ubicación en la respuesta, usamos el ID
                    availableQuantity: box.quantity,
                    lastTransactionDate: box.lastTransactionDate
                }));
                
                updateBoxesTable(boxesData);
            } else {
                // Si no hay cajas, mostramos el mensaje de no hay datos
                const boxesTableBody = document.querySelector('#boxesTable tbody');
                boxesTableBody.innerHTML = '';
                
                const noBoxesMessage = document.getElementById('noBoxesMessage');
                noBoxesMessage.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showBoxesError();
        });
}

// Función para actualizar la tabla de cajas con los datos recibidos
function updateBoxesTable(boxesData) {
    const boxesTableBody = document.querySelector('#boxesTable tbody');
    boxesTableBody.innerHTML = '';
    
    // Si no hay datos, mostramos un mensaje
    if (!boxesData || boxesData.length === 0) {
        const noBoxesMessage = document.getElementById('noBoxesMessage');
        noBoxesMessage.style.display = 'block';
        return;
    }
    
    // Ocultamos el mensaje de error si estaba visible
    const noBoxesMessage = document.getElementById('noBoxesMessage');
    noBoxesMessage.style.display = 'none';
    
    // Agregamos los datos a la tabla
    boxesData.forEach(box => {
        const row = document.createElement('tr');
        
        // Formateamos la fecha si existe
        let formattedDate = 'N/A';
        if (box.lastTransactionDate) {
            const date = new Date(box.lastTransactionDate);
            formattedDate = date.toLocaleDateString('es-ES', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
        
        row.innerHTML = `
            <td>${box.boxNumber || box.code || 'N/A'}</td>
            <td>${formattedDate}</td>
            <td>${box.availableQuantity !== undefined ? box.availableQuantity : 'N/A'}</td>
        `;
        boxesTableBody.appendChild(row);
    });
}

// Función para mostrar un error en la tabla de cajas
function showBoxesError() {
    const boxesTableBody = document.querySelector('#boxesTable tbody');
    const noBoxesMessage = document.getElementById('noBoxesMessage');
    
    // Ocultar la tabla
    boxesTableBody.innerHTML = '';
    
    // Mostrar mensaje de error
    noBoxesMessage.innerHTML = '<p>Error al cargar la información de las cajas.</p>';
    noBoxesMessage.style.display = 'block';
    noBoxesMessage.className = 'alert alert-danger';
}

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

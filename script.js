document.addEventListener('DOMContentLoaded', () => {
    const clientTableBody = document.querySelector('#client-list tbody');
    const addClientForm = document.querySelector('#add-client-form');
    const clientListSection = document.querySelector('#client-list');
    const addClientSection = document.querySelector('#add-client');
    const clientDetailsSection = document.querySelector('#client-details');
    const calendarSection = document.querySelector('#calendar-section');
    const availabilitySection = document.querySelector('#availability-section'); // New
    const slotSearchSection = document.querySelector('#slot-search-section'); // New

    const detailNombre = document.querySelector('#detail-nombre');
    const detailApellidos = document.querySelector('#detail-apellidos');
    const detailTelefono = document.querySelector('#detail-telefono');
    const detailEmail = document.querySelector('#detail-email');
    const detailHistorialMedico = document.querySelector('#detail-historial-medico');
    const saveHistorialButton = document.querySelector('#save-historial-button');
    const closeDetailsButton = document.querySelector('#close-details-button');
    const showAddClientFormBtn = document.querySelector('#show-add-client-form-btn');

    const appointmentsTableBody = document.querySelector('#appointments-table-body');
    const addAppointmentForm = document.querySelector('#add-appointment-form');

    const documentsTableBody = document.querySelector('#documents-table-body');
    const uploadDocumentForm = document.querySelector('#upload-document-form');

    const searchInput = document.querySelector('#search-input');
    const searchButton = document.querySelector('#search-button');

    const navClientLink = document.querySelector('a[href="#client-list"]');
    const navCalendarLink = document.querySelector('a[href="#calendar-section"]');
    const navAvailabilityLink = document.querySelector('a[href="#availability-section"]'); // New

    // Modal elements
    const addAppointmentModal = document.getElementById('addAppointmentModal');
    const editAppointmentModal = document.getElementById('editAppointmentModal');
    const availabilityModal = document.getElementById('availabilityModal'); // New
    const closeButtons = document.querySelectorAll('.modal .close-button');

    const addCalendarAppointmentForm = document.getElementById('add-calendar-appointment-form');
    const editCalendarAppointmentForm = document.getElementById('edit-calendar-appointment-form');
    const deleteAppointmentButton = document.getElementById('delete-appointment-button');

    const addAppointmentClientSelect = document.getElementById('add-appointment-client');
    const editAppointmentClientSelect = document.getElementById('edit-appointment-client');

    // Availability elements
    const showAddAvailabilityModalBtn = document.getElementById('show-add-availability-modal-btn'); // New
    const availabilityTableBody = document.getElementById('availability-table-body'); // New
    const availabilityForm = document.getElementById('availability-form'); // New
    const deleteAvailabilityButton = document.getElementById('delete-availability-button'); // New

    // Slot Search elements
    const searchSlotsForm = document.getElementById('search-slots-form'); // New
    const searchResultsDiv = document.getElementById('search-results'); // New

    let currentClientId = null;
    let calendar = null; // FullCalendar instance

    // Helper function to open modals
    function openModal(modalElement) {
        modalElement.style.display = 'block';
    }

    // Helper function to close modals
    function closeModal(modalElement) {
        modalElement.style.display = 'none';
    }

    // Close modals when clicking on the close button or outside the modal
    closeButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            closeModal(e.target.closest('.modal'));
        });
    });

    window.addEventListener('click', (e) => {
        if (e.target === addAppointmentModal) {
            closeModal(addAppointmentModal);
        } else if (e.target === editAppointmentModal) {
            closeModal(editAppointmentModal);
        } else if (e.target === availabilityModal) { // New
            closeModal(availabilityModal);
        }
    });

    // Function to fetch clients and populate dropdowns
    async function fetchClientsForDropdown(selectElementId) {
        try {
            const response = await fetch('api/clients.php');
            const clients = await response.json();
            const selectElement = document.getElementById(selectElementId);
            selectElement.innerHTML = ''; // Clear existing options
            clients.forEach(client => {
                const option = document.createElement('option');
                option.value = client.id;
                option.textContent = `${client.nombre} ${client.apellidos}`;
                selectElement.appendChild(option);
            });
        } catch (error) {
            console.error('Error al cargar clientes para el dropdown:', error);
        }
    }

    // Función para obtener y mostrar clientes
    async function fetchClients(query = '') {
        try {
            let url = 'api/clients.php';
            if (query) {
                url += `?query=${encodeURIComponent(query)}`;
            }
            const response = await fetch(url);
            const clients = await response.json();
            
            clientTableBody.innerHTML = ''; // Limpiar tabla

            clients.forEach(client => {
                const row = clientTableBody.insertRow();
                row.insertCell().textContent = client.nombre;
                row.insertCell().textContent = client.apellidos;
                row.insertCell().textContent = client.telefono;
                row.insertCell().textContent = client.email;
                const actionsCell = row.insertCell();
                // Almacenar el objeto cliente completo en el botón para fácil acceso
                actionsCell.innerHTML = `<button class="view-details-btn" data-client='${JSON.stringify(client)}'>Ver Detalles</button>`;
            });
        } catch (error) {
            console.error('Error al obtener clientes:', error);
        }
    }

    // Función para obtener y mostrar citas/tratamientos de un cliente
    async function fetchAppointments(clientId) {
        try {
            const response = await fetch(`api/clients.php?action=get_appointments&client_id=${clientId}`);
            const appointments = await response.json();

            appointmentsTableBody.innerHTML = ''; // Limpiar tabla de citas

            appointments.forEach(appointment => {
                const row = appointmentsTableBody.insertRow();
                row.insertCell().textContent = appointment.fecha_cita;
                row.insertCell().textContent = appointment.tipo_tratamiento;
                row.insertCell().textContent = appointment.observaciones || '';
                row.insertCell().textContent = appointment.costo || '';
            });
        } catch (error) {
            console.error('Error al obtener citas:', error);
        }
    }

    // Función para obtener y mostrar documentos de un cliente
    async function fetchDocuments(clientId) {
        try {
            const response = await fetch(`api/clients.php?action=get_documents&client_id=${clientId}`);
            const documents = await response.json();

            documentsTableBody.innerHTML = ''; // Limpiar tabla de documentos

            documents.forEach(doc => {
                const row = documentsTableBody.insertRow();
                row.insertCell().textContent = doc.nombre_archivo;
                row.insertCell().textContent = doc.tipo_documento || '';
                row.insertCell().textContent = doc.descripcion || '';
                row.insertCell().textContent = doc.fecha_subida;
                const actionsCell = row.insertCell();
                actionsCell.innerHTML = `<a href="${doc.ruta_archivo}" target="_blank">Ver</a>`; // Link to view document
            });
        } catch (error) {
            console.error('Error al obtener documentos:', error);
        }
    }

    // Manejar el envío del formulario para añadir cliente
    addClientForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(addClientForm);
        const clientData = Object.fromEntries(formData.entries());

        try {
            const response = await fetch('api/clients.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(clientData)
            });
            const result = await response.json();
            if (response.ok) {
                alert(result.message);
                addClientForm.reset(); // Limpiar formulario
                fetchClients(); // Actualizar la lista de clientes
                addClientSection.style.display = 'none'; // Ocultar el formulario después de añadir
                clientListSection.style.display = 'block'; // Mostrar la lista de clientes
            } else {
                alert('Error: ' + result.message);
            }
        } catch (error) {
            console.error('Error al añadir cliente:', error);
            alert('Error de conexión al añadir cliente.');
        }
    });

    // Manejar clic en botones "Ver Detalles"
    clientTableBody.addEventListener('click', (e) => {
        if (e.target.classList.contains('view-details-btn')) {
            const client = JSON.parse(e.target.dataset.client);
            currentClientId = client.id; // Guardar el ID del cliente

            detailNombre.textContent = client.nombre;
            detailApellidos.textContent = client.apellidos;
            detailTelefono.textContent = client.telefono;
            detailEmail.textContent = client.email;
            detailHistorialMedico.value = client.historial_medico || ''; // Mostrar historial o vacío

            fetchAppointments(currentClientId); // Cargar citas del cliente
            fetchDocuments(currentClientId); // Cargar documentos del cliente

            clientListSection.style.display = 'none';
            addClientSection.style.display = 'none';
            calendarSection.style.display = 'none'; // Hide calendar
            availabilitySection.style.display = 'none'; // Hide availability
            slotSearchSection.style.display = 'none'; // Hide slot search
            clientDetailsSection.style.display = 'block';
        }
    });

    // Manejar clic en botón "Cerrar"
    closeDetailsButton.addEventListener('click', () => {
        clientDetailsSection.style.display = 'none';
        clientListSection.style.display = 'block';
        addClientSection.style.display = 'none'; // Asegurarse de que el formulario de añadir esté oculto
        calendarSection.style.display = 'none'; // Hide calendar
        availabilitySection.style.display = 'none'; // Hide availability
        slotSearchSection.style.display = 'none'; // Hide slot search
        currentClientId = null; // Limpiar el ID del cliente actual
    });

    // Manejar clic en botón "Guardar Historial"
    saveHistorialButton.addEventListener('click', async () => {
        if (!currentClientId) return;

        const updatedHistorial = detailHistorialMedico.value;

        try {
            const response = await fetch('api/clients.php', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id: currentClientId,
                    historial_medico: updatedHistorial
                })
            });
            const result = await response.json();
            if (response.ok) {
                alert(result.message);
                // Opcional: actualizar la lista de clientes si el historial médico se muestra en la tabla principal
                fetchClients(); 
            } else {
                alert('Error: ' + result.message);
            }
        } catch (error) {
            console.error('Error al guardar historial médico:', error);
            alert('Error de conexión al guardar historial médico.');
        }
    });

    // Manejar clic en botón "Registrar Nuevo Cliente"
    showAddClientFormBtn.addEventListener('click', () => {
        if (addClientSection.style.display === 'none' || addClientSection.style.display === '') {
            addClientSection.style.display = 'block';
            clientListSection.style.display = 'none'; // Ocultar la lista de clientes al mostrar el formulario
            clientDetailsSection.style.display = 'none'; // Ocultar detalles si están visibles
            calendarSection.style.display = 'none'; // Hide calendar
            availabilitySection.style.display = 'none'; // Hide availability
            slotSearchSection.style.display = 'none'; // Hide slot search
        } else {
            addClientSection.style.display = 'none';
            clientListSection.style.display = 'block'; // Mostrar la lista de clientes al ocultar el formulario
        }
    });

    // Manejar el envío del formulario para añadir cita/tratamiento
    addAppointmentForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(addAppointmentForm);
        const appointmentData = Object.fromEntries(formData.entries());
        appointmentData.client_id = currentClientId; // Añadir el ID del cliente actual

        try {
            const response = await fetch('api/clients.php?action=add_appointment', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(appointmentData)
            });
            const result = await response.json();
            if (response.ok) {
                alert(result.message);
                addAppointmentForm.reset(); // Limpiar formulario
                fetchAppointments(currentClientId); // Actualizar la lista de citas
            } else {
                alert('Error: ' + result.message);
            }
        } catch (error) {
            console.error('Error al añadir cita/tratamiento:', error);
            alert('Error de conexión al añadir cita/tratamiento.');
        }
    });

    // Manejar el envío del formulario para subir documento
    uploadDocumentForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(uploadDocumentForm);
        formData.append('client_id', currentClientId); // Add client_id to FormData

        try {
            const response = await fetch('api/clients.php?action=upload_document', {
                method: 'POST',
                body: formData // FormData is automatically set with correct Content-Type
            });
            const result = await response.json();
            if (response.ok) {
                alert(result.message);
                uploadDocumentForm.reset(); // Limpiar formulario
                fetchDocuments(currentClientId); // Actualizar la lista de documentos
            } else {
                alert('Error: ' + result.message);
            }
        } catch (error) {
            console.error('Error al subir documento:', error);
            alert('Error de conexión al subir documento.');
        }
    });

    // Manejar clic en botón "Buscar"
    searchButton.addEventListener('click', () => {
        const query = searchInput.value;
        fetchClients(query);
    });

    // Manejar "Enter" en el campo de búsqueda
    searchInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            const query = searchInput.value;
            fetchClients(query);
        }
    });

    // Navegación entre secciones
    navClientLink.addEventListener('click', (e) => {
        e.preventDefault();
        showSection('client-list');
        updateActiveNav(e.target);
        fetchClients(); // Refresh client list when navigating back
    });

    navCalendarLink.addEventListener('click', (e) => {
        e.preventDefault();
        showSection('calendar-section');
        updateActiveNav(e.target);
        calendar.render(); // Render calendar when navigating to it
    });

    navAvailabilityLink.addEventListener('click', (e) => { // New
        e.preventDefault();
        showSection('availability-section');
        updateActiveNav(e.target);
        fetchAvailability(); // Load availability when navigating to it
    });
    
    // Function to show/hide sections
    function showSection(sectionId) {
        // Hide all sections
        clientListSection.style.display = 'none';
        addClientSection.style.display = 'none';
        clientDetailsSection.style.display = 'none';
        calendarSection.style.display = 'none';
        availabilitySection.style.display = 'none';
        slotSearchSection.style.display = 'none';
        
        // Show the selected section
        const targetSection = document.getElementById(sectionId);
        if (targetSection) {
            targetSection.style.display = 'block';
            targetSection.classList.add('active');
        }
    }
    
    // Function to update active navigation state
    function updateActiveNav(clickedLink) {
        // Remove active class from all nav links
        document.querySelectorAll('nav a').forEach(link => {
            link.classList.remove('active');
        });
        // Add active class to clicked link
        clickedLink.classList.add('active');
    }
    
    // Initialize active navigation state
    updateActiveNav(navClientLink);
    
    // Mobile menu functionality
    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const mainNav = document.getElementById('main-nav');
    
    mobileMenuToggle.addEventListener('click', () => {
        mobileMenuToggle.classList.toggle('active');
        mainNav.classList.toggle('mobile-hidden');
        mainNav.classList.toggle('mobile-visible');
    });
    
    // Close mobile menu when clicking on a nav link
    document.querySelectorAll('nav a').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 768) {
                mobileMenuToggle.classList.remove('active');
                mainNav.classList.add('mobile-hidden');
                mainNav.classList.remove('mobile-visible');
            }
        });
    });
    
    // Close mobile menu when clicking outside
    document.addEventListener('click', (e) => {
        if (!mobileMenuToggle.contains(e.target) && !mainNav.contains(e.target)) {
            if (window.innerWidth <= 768) {
                mobileMenuToggle.classList.remove('active');
                mainNav.classList.add('mobile-hidden');
                mainNav.classList.remove('mobile-visible');
            }
        }
    });

    // Inicializar FullCalendar
    const calendarEl = document.getElementById('calendar');
    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: {
            url: 'api/clients.php?action=get_calendar_appointments',
            failure: function() {
                alert('Error al cargar las citas!');
            }
        },
        eventClick: function(info) {
            // Populate and open edit modal
            document.getElementById('edit-appointment-id').value = info.event.id;
            document.getElementById('edit-appointment-datetime').value = info.event.startStr.substring(0, 16); // Format for datetime-local
            document.getElementById('edit-appointment-type').value = info.event.title.split(' - ')[0]; // Extract type from title
            document.getElementById('edit-appointment-obs').value = info.event.extendedProps.observaciones || '';
            document.getElementById('edit-appointment-costo').value = info.event.extendedProps.costo || '';
            document.getElementById('edit-appointment-status').value = info.event.extendedProps.status || 'pendiente';
            document.getElementById('edit-appointment-notes').value = info.event.extendedProps.notas_internas || '';

            // Fetch clients for dropdown and select the correct one
            fetchClientsForDropdown('edit-appointment-client').then(() => {
                // Assuming client_id is available in extendedProps or can be derived
                // For now, we'll just display the client name in the title
                // A more robust solution would involve fetching client_id with the event
                // and then setting the selected option.
            });

            openModal(editAppointmentModal);
        },
        businessHours: {
            url: 'api/clients.php?action=get_availability',
            method: 'GET',
            failure: function() {
                alert('Error al cargar la disponibilidad!');
            }
        },
        selectable: true,
        selectMirror: true,
        select: function(info) {
            // Open add appointment modal and pre-fill date/time
            document.getElementById('add-appointment-datetime').value = info.startStr.substring(0, 16); // Format for datetime-local
            fetchClientsForDropdown('add-appointment-client');
            openModal(addAppointmentModal);
        }
    });

    // Handle Add Appointment Form Submission
    addCalendarAppointmentForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(addCalendarAppointmentForm);
        const appointmentData = Object.fromEntries(formData.entries());

        try {
            const response = await fetch('api/clients.php?action=add_calendar_appointment', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(appointmentData)
            });
            const result = await response.json();
            if (response.ok) {
                alert(result.message);
                addCalendarAppointmentForm.reset();
                closeModal(addAppointmentModal);
                calendar.refetchEvents(); // Refresh calendar events
            } else {
                alert('Error: ' + result.message);
            }
        } catch (error) {
            console.error('Error al añadir cita:', error);
            alert('Error de conexión al añadir cita.');
        }
    });

    // Handle Edit Appointment Form Submission
    editCalendarAppointmentForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(editCalendarAppointmentForm);
        const appointmentData = Object.fromEntries(formData.entries());

        try {
            const response = await fetch('api/clients.php?action=update_appointment', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(appointmentData)
            });
            const result = await response.json();
            if (response.ok) {
                alert(result.message);
                closeModal(editAppointmentModal);
                calendar.refetchEvents(); // Refresh calendar events
            } else {
                alert('Error: ' + result.message);
            }
        } catch (error) {
            console.error('Error al actualizar cita:', error);
            alert('Error de conexión al actualizar cita.');
        }
    });

    // Handle Delete Appointment Button Click
    deleteAppointmentButton.addEventListener('click', async () => {
        const appointmentId = document.getElementById('edit-appointment-id').value;
        if (confirm('¿Estás seguro de que quieres eliminar esta cita?')) {
            try {
                const response = await fetch('api/clients.php?action=delete_appointment', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id: appointmentId })
                });
                const result = await response.json();
                if (response.ok) {
                    alert(result.message);
                    closeModal(editAppointmentModal);
                    calendar.refetchEvents(); // Refresh calendar events
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error al eliminar cita:', error);
                alert('Error de conexión al eliminar cita.');
            }
        }
    });

    // --- Availability Management Logic ---

    // Function to fetch and display availability blocks
    async function fetchAvailability() {
        try {
            const response = await fetch('api/clients.php?action=get_availability');
            const availabilityBlocks = await response.json();

            availabilityTableBody.innerHTML = ''; // Clear existing rows

            availabilityBlocks.forEach(block => {
                const row = availabilityTableBody.insertRow();
                row.dataset.id = block.id; // Store ID for editing/deleting
                row.insertCell().textContent = block.display === 'background' ? (block.color === '#ff9f89' ? 'Bloqueado' : 'Disponible') : '';
                row.insertCell().textContent = block.daysOfWeek ? block.daysOfWeek[0] : '-'; // Assuming single day for now
                row.insertCell().textContent = block.startRecur ? block.startRecur : '-';
                row.insertCell().textContent = block.startTime;
                row.insertCell().textContent = block.endTime;
                row.insertCell().textContent = block.description || '';
                const actionsCell = row.insertCell();
                actionsCell.innerHTML = '<button class="edit-availability-btn">Editar</button>';
            });
        } catch (error) {
            console.error('Error al cargar la disponibilidad:', error);
        }
    }

    // Open Add Availability Modal
    showAddAvailabilityModalBtn.addEventListener('click', () => {
        availabilityForm.reset();
        document.getElementById('availability-id').value = ''; // Clear ID for new entry
        deleteAvailabilityButton.style.display = 'none'; // Hide delete button for new entry
        openModal(availabilityModal);
    });

    // Handle Edit Availability Button Click
    availabilityTableBody.addEventListener('click', (e) => {
        if (e.target.classList.contains('edit-availability-btn')) {
            const row = e.target.closest('tr');
            const id = row.dataset.id;

            // Fetch specific availability block data (or use data from row if available)
            // For simplicity, we'll refetch or assume data is in the row
            const type = row.cells[0].textContent === 'Bloqueado' ? 'bloqueado' : 'disponible';
            const dayOfWeek = row.cells[1].textContent !== '-' ? row.cells[1].textContent : '';
            const date = row.cells[2].textContent !== '-' ? row.cells[2].textContent : '';
            const startTime = row.cells[3].textContent;
            const endTime = row.cells[4].textContent;
            const description = row.cells[5].textContent;

            document.getElementById('availability-id').value = id;
            document.getElementById('availability-type').value = type;
            document.getElementById('availability-day-of-week').value = dayOfWeek;
            document.getElementById('availability-date').value = date;
            document.getElementById('availability-start-time').value = startTime;
            document.getElementById('availability-end-time').value = endTime;
            document.getElementById('availability-description').value = description;

            deleteAvailabilityButton.style.display = 'inline-block'; // Show delete button for existing entry
            openModal(availabilityModal);
        }
    });

    // Handle Availability Form Submission
    availabilityForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(availabilityForm);
        const availabilityData = Object.fromEntries(formData.entries());

        const id = document.getElementById('availability-id').value;
        const method = id ? 'PUT' : 'POST';
        const action = id ? 'update_availability' : 'add_availability';

        try {
            const response = await fetch(`api/clients.php?action=${action}`, {
                method: method,
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(availabilityData)
            });
            const result = await response.json();
            if (response.ok) {
                alert(result.message);
                closeModal(availabilityModal);
                fetchAvailability(); // Refresh availability list
                calendar.refetchEvents(); // Refresh calendar to show updated availability
            } else {
                alert('Error: ' + result.message);
            }
        } catch (error) {
            console.error(`Error al ${id ? 'actualizar' : 'añadir'} disponibilidad:`, error);
            alert(`Error de conexión al ${id ? 'actualizar' : 'añadir'} disponibilidad.`);
        }
    });

    // Handle Delete Availability Button Click
    deleteAvailabilityButton.addEventListener('click', async () => {
        const availabilityId = document.getElementById('availability-id').value;
        if (confirm('¿Estás seguro de que quieres eliminar este bloque de disponibilidad/bloqueo?')) {
            try {
                const response = await fetch('api/clients.php?action=delete_availability', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id: availabilityId })
                });
                const result = await response.json();
                if (response.ok) {
                    alert(result.message);
                    closeModal(availabilityModal);
                    fetchAvailability(); // Refresh availability list
                    calendar.refetchEvents(); // Refresh calendar
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error al eliminar disponibilidad:', error);
                alert('Error de conexión al eliminar disponibilidad.');
            }
        }
    });

    // --- Slot Search Logic ---

    searchSlotsForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const startDatetime = document.getElementById('search-slots-start').value;
        const endDatetime = document.getElementById('search-slots-end').value;
        const duration = document.getElementById('search-slots-duration').value;

        if (!startDatetime || !endDatetime || !duration) {
            alert('Por favor, rellena todos los campos para la búsqueda de huecos.');
            return;
        }

        try {
            const response = await fetch(`api/clients.php?action=search_available_slots&start_datetime=${encodeURIComponent(startDatetime)}&end_datetime=${encodeURIComponent(endDatetime)}&duration=${encodeURIComponent(duration)}`);
            const slots = await response.json();

            searchResultsDiv.innerHTML = ''; // Clear previous results
            if (slots.length > 0) {
                const ul = document.createElement('ul');
                slots.forEach(slot => {
                    const li = document.createElement('li');
                    li.textContent = `Desde: ${new Date(slot.start).toLocaleString()} - Hasta: ${new Date(slot.end).toLocaleString()}`;
                    ul.appendChild(li);
                });
                searchResultsDiv.appendChild(ul);
            } else {
                searchResultsDiv.textContent = 'No se encontraron huecos disponibles en el rango de fechas y duración especificados.';
            }
        } catch (error) {
            console.error('Error al buscar huecos disponibles:', error);
            searchResultsDiv.textContent = 'Error al buscar huecos disponibles.';
        }
    });

    // Cargar clientes al iniciar la página
    fetchClients();
});
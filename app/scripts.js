document.addEventListener('DOMContentLoaded', () => {

    const storedUser = JSON.parse(localStorage.getItem('vuelos_user') || 'null');
    const protectedPage = document.querySelector('[data-user-name]');

    if (protectedPage && !storedUser) {
        window.location.href = 'login.html';
        return;
    }

    document.querySelectorAll('[data-user-name]').forEach(element => {
        element.textContent = `Hola, ${storedUser.username}`;
    });

    document.querySelectorAll('[data-logout]').forEach(button => {
        button.addEventListener('click', () => {
            fetch('auth.php', { method: 'POST', body: new URLSearchParams({ action: 'logout' }) })
                .finally(() => {
                    localStorage.removeItem('vuelos_user');
                    localStorage.removeItem('user_email');
                    window.location.href = 'login.html';
                });
        });
    });

    function clearErrors(form) {
        form.querySelectorAll('.field-error').forEach(error => {
            error.textContent = '';
        });
    }

    function showErrors(form, errors) {
        Object.entries(errors || {}).forEach(([field, message]) => {
            const error = form.querySelector(`#${field}Error`);
            if (error) error.textContent = message;
        });
    }

    // 1. REGISTRO DE USUARIO
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            clearErrors(this);
            const formData = new FormData(this);
            formData.append('action', 'register');

            fetch('auth.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    window.location.href = 'login.html';
                } else {
                    showErrors(registerForm, data.errors);
                }
            })
            .catch(error => console.error('Error:', error));
        });
    }

    // 2. INICIO DE SESIÓN
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            clearErrors(this);
            const formData = new FormData(this);
            formData.append('action', 'login');

            fetch('auth.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    localStorage.setItem('vuelos_user', JSON.stringify(data.user));
                    window.location.href = 'search.html';
                } else {
                    showErrors(loginForm, data.errors);
                }
            })
            .catch(error => console.error('Error:', error));
        });
    }

    // 3. BÚSQUEDA DE VUELOS
    const searchForm = document.getElementById('searchForm');
    if (searchForm) {
        const loadFlights = () => {
            const origin = document.getElementById('origin').value.trim();
            const destination = document.getElementById('destination').value.trim();

            fetch(`search_flights.php?origin=${encodeURIComponent(origin)}&destination=${encodeURIComponent(destination)}`)
            .then(response => response.json())
            .then(data => {
                const resultsDiv = document.getElementById('results');
                resultsDiv.innerHTML = '';

                if (data.status === 'success' && data.flights.length > 0) {
                    data.flights.forEach(flight => {
                        resultsDiv.innerHTML += `
                            <div class="result-card">
                                <h3>${flight.origin} a ${flight.destination}</h3>
                                <p><strong>Salida:</strong> ${flight.departure_date} | <strong>Regreso:</strong> ${flight.return_date || 'Solo ida'}</p>
                                <p><strong>Precio:</strong> $${flight.price} MXN | <strong>Asientos:</strong> ${flight.available_seats}</p>
                                <label for="seats-${flight.flight_id}">Asientos a reservar:</label>
                                <input class="seat-input" type="number" id="seats-${flight.flight_id}" min="1" max="${flight.available_seats}" value="1">
                                <label for="payment-${flight.flight_id}">Método de pago:</label>
                                <select class="payment-select" id="payment-${flight.flight_id}">
                                    <option value="Tarjeta">Tarjeta</option>
                                    <option value="Transferencia">Transferencia</option>
                                </select>
                                <button type="button" onclick="reserveFlight(${flight.flight_id})">Reservar y pagar</button>
                            </div>`;
                    });
                } else {
                    resultsDiv.innerHTML = '<p class="empty-state">No hay vuelos disponibles.</p>';
                }
            })
            .catch(() => {
                document.getElementById('results').innerHTML = '<p class="field-error">No se pudieron cargar los vuelos.</p>';
            });
        };

        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            loadFlights();
        });
        document.getElementById('showAllFlights').addEventListener('click', () => {
            document.getElementById('origin').value = '';
            document.getElementById('destination').value = '';
            loadFlights();
        });
        loadFlights();
    }

    // 4. CONSULTAR RESERVAS
    const reservations = document.getElementById('reservations');
    if (reservations && storedUser) {
            fetch(`manage_reservations.php?user_id=${storedUser.id}`)
            .then(response => response.json())
            .then(data => {
                reservations.innerHTML = '';

                if (data.status === 'success' && data.reservations.length > 0) {
                    data.reservations.forEach(res => {
                        reservations.innerHTML += `
                            <div class="result-card">
                                <h4>Reserva #${res.reservation_id}</h4>
                                <p>${res.origin} a ${res.destination}</p>
                                <p><strong>Salida:</strong> ${res.departure_date} | <strong>Asientos:</strong> ${res.seats} | <strong>Total:</strong> $${res.total_price} MXN</p>
                                <p><strong>Pago:</strong> ${res.payment_method} | ${res.payment_status}</p>
                                <p><strong>Estado:</strong> <span class="status-${res.status.toLowerCase()}">${res.status}</span></p>
                                ${res.status === 'CONFIRMED' ? `<button type="button" onclick="cancelReservation(${res.reservation_id})">Cancelar</button>` : ''}
                            </div>
                        `;
                    });
                } else {
                    reservations.innerHTML = '<p class="empty-state">No tienes reservas registradas.</p>';
                }
            })
            .catch(() => { reservations.innerHTML = '<p class="field-error">No se pudieron cargar tus reservas.</p>'; });
    }
});

function reserveFlight(flightId) {
    const user = JSON.parse(localStorage.getItem('vuelos_user') || 'null');
    if (!user) {
        window.location.href = 'login.html';
        return;
    }

    const formData = new FormData();
    formData.append('user_id', user.id);
    formData.append('flight_id', flightId);
    formData.append('seats', document.getElementById(`seats-${flightId}`).value);
    formData.append('payment_method', document.getElementById(`payment-${flightId}`).value);

    fetch('reserve_flight.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            window.location.href = 'reservations.html';
        } else {
            alert(data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}

function cancelReservation(reservationId) {
    const user = JSON.parse(localStorage.getItem('vuelos_user') || 'null');
    const formData = new FormData();
    formData.append('action', 'cancel');
    formData.append('user_id', user.id);
    formData.append('reservation_id', reservationId);

    fetch('manage_reservations.php', { method: 'POST', body: formData })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') window.location.reload();
            else alert(data.message);
        });
}
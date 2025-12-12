// gestionCita.js
document.addEventListener('DOMContentLoaded', function () {


    function showSimpleAlert(message) {
        alert(message);
    }

    
    const reagendarModalEl      = document.getElementById('reagendarModal');
    const reagendarForm         = document.getElementById('reagendarForm');
    const nuevaFechaInput       = document.getElementById('nueva_fecha');
    const nuevaHoraInput        = document.getElementById('nueva_hora');
    const modalCitaIdInput      = document.getElementById('modalCitaId');
    const modalCitaInfo         = document.getElementById('modalCitaInfo');
    const doctorIdInput         = document.getElementById('doctorId');
    const availabilityStatusEl  = document.getElementById('availabilityStatusEdit');
    const timeSlotsContainerEl  = document.getElementById('timeSlotsContainerEdit');

    
    if (nuevaFechaInput) {
        flatpickr(nuevaFechaInput, {
            locale: "es",
            minDate: "today",
            dateFormat: "Y-m-d",
            disableMobile: true,
            disable: [
                function (date) {
                  
                    return date.getDay() === 0;
                }
            ],
            onChange: function (selectedDates, dateStr) {
                nuevaFechaInput.value = dateStr;
               
                nuevaFechaInput.dispatchEvent(new Event("change"));
            }
        });
    }

    
    if (reagendarModalEl && modalCitaIdInput && modalCitaInfo) {
        reagendarModalEl.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if (!button) return;

            const citaId     = button.getAttribute('data-cita-id');
            const citaFecha  = button.getAttribute('data-cita-fecha'); 
            const citaHora   = button.getAttribute('data-cita-hora');  
            const citaNombre = button.getAttribute('data-cita-nombre');
            const doctorId   = button.getAttribute('data-doctor-id');

            if (modalCitaIdInput) modalCitaIdInput.value = citaId || '';
            if (doctorIdInput)    doctorIdInput.value    = doctorId || '';

            if (nuevaFechaInput && citaFecha) nuevaFechaInput.value = citaFecha;
            if (nuevaHoraInput  && citaHora)  nuevaHoraInput.value  = citaHora;

            if (modalCitaInfo) {
                modalCitaInfo.innerHTML = `
                    <strong>${citaNombre || 'Cita'}</strong><br>
                    <span>Fecha actual: ${citaFecha || '--'} a las ${citaHora || '--'}</span>
                `;
            }

            
            if (doctorId && citaFecha) {
                cargarHorariosDia(doctorId, citaFecha);
            }
        });
    }

    
    function generateSlotsByDay(dateStr) {
        if (!dateStr) return [];

        const [y, m, d] = dateStr.split('-').map(Number);
        const date = new Date(y, m - 1, d);
        const day = date.getDay(); // 0 domingo, 6 sábado

        let ranges = [];

        switch (day) {
            case 0: // Domingo
                return [];

            case 6: // Sábado
                ranges = [
                    ["09:00", "15:00"]
                ];
                break;

            default: // Lunes a Viernes
                ranges = [
                    ["09:00", "12:00"],
                    ["14:00", "18:00"]
                ];
                break;
        }

        const slots = [];

        ranges.forEach(([start, end]) => {
            let current = start;
            while (current < end) {
                slots.push(current);

                let [h, m] = current.split(":").map(Number);
                m += 30;
                if (m >= 60) {
                    h++;
                    m -= 60;
                }
                current = `${String(h).padStart(2, "0")}:${String(m).padStart(2, "0")}`;
            }
        });

        return slots;
    }

   
  async function fetchBusyHours(doctorId, date) {
    const body = `action=get_busy_slots&doctor_id=${encodeURIComponent(doctorId)}&date=${encodeURIComponent(date)}`;

    try {
        const res = await fetch("", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body
        });

        const data = await res.json();
        let busy = data.busy ?? [];

        if (window.currentCitaHora && window.currentCitaFecha === date) {
            busy = busy.filter(h => h.trim() !== window.currentCitaHora.trim());
        }

        const now = new Date();

        const localToday =
            now.getFullYear() + "-" +
            String(now.getMonth() + 1).padStart(2, "0") + "-" +
            String(now.getDate()).padStart(2, "0");

        if (date === localToday) {
            const minutosActuales = now.getHours() * 60 + now.getMinutes();

            const slotsPasados = generateSlotsByDay(date).filter(time => {
                const [h, m] = time.split(":").map(Number);
                return (h * 60 + m) <= minutosActuales;
            });

            busy = busy.concat(slotsPasados);
        }

        
        return [...new Set(busy)];

    } catch (error) {
        console.error("Error al obtener horarios:", error);
        return [];
    }
}


    
    function renderTimeSlots(busy, dateStr) {
        if (!timeSlotsContainerEl || !availabilityStatusEl) return;

        const slots = generateSlotsByDay(dateStr);
        let availableCount = 0;

        timeSlotsContainerEl.innerHTML = "";

        if (slots.length === 0) {
            availabilityStatusEl.style.display = "block";
            availabilityStatusEl.textContent = "La óptica está cerrada este día.";
            availabilityStatusEl.className = "availability-status unavailable";
            return;
        }

        slots.forEach(time => {
            const isBusy = busy.includes(time);
            const div = document.createElement("div");

            div.className = `time-slot ${isBusy ? "unavailable" : "available"}`;
            div.textContent = time;

            if (!isBusy) {
                div.addEventListener("click", () => {
                    
                    timeSlotsContainerEl.querySelectorAll(".time-slot.selected")
                        .forEach(e => e.classList.remove("selected"));

                    div.classList.add("selected");
                    if (nuevaHoraInput) nuevaHoraInput.value = time;
                });

                availableCount++;
            }

            timeSlotsContainerEl.appendChild(div);
        });

        availabilityStatusEl.style.display = "block";

        if (availableCount > 0) {
            availabilityStatusEl.textContent = `${availableCount} horarios disponibles`;
            availabilityStatusEl.className = "availability-status available";
        } else {
            availabilityStatusEl.textContent = "No hay horarios disponibles";
            availabilityStatusEl.className = "availability-status unavailable";
        }
    }

    
    async function cargarHorariosDia(doctorId, date) {
        if (!doctorId || !date) return;

        if (availabilityStatusEl) {
            availabilityStatusEl.style.display = "block";
            availabilityStatusEl.textContent = "Cargando horarios...";
            availabilityStatusEl.className = "availability-status loading";
        }
        if (timeSlotsContainerEl) {
            timeSlotsContainerEl.innerHTML = "";
        }

        const busy = await fetchBusyHours(doctorId, date);
        renderTimeSlots(busy, date);
    }

    if (nuevaFechaInput) {
        nuevaFechaInput.addEventListener("change", () => {
            const date = nuevaFechaInput.value;
            const doctorId = doctorIdInput ? doctorIdInput.value : "";

            if (!date || !doctorId) return;
            cargarHorariosDia(doctorId, date);
        });
    }

    // Validación de fecha/hora futura en el submit del formulario de reagendar
    if (reagendarForm && nuevaFechaInput && nuevaHoraInput) {
        reagendarForm.addEventListener('submit', function (e) {
            const nuevaFecha = nuevaFechaInput.value;
            const nuevaHora  = nuevaHoraInput.value;

            if (!nuevaFecha || !nuevaHora) {
                e.preventDefault();
                showSimpleAlert('Debes seleccionar una nueva fecha y hora.');
                return;
            }

            // Fecha/hora completa
            const nuevaFechaHora = new Date(`${nuevaFecha}T${nuevaHora}:00`);
            const ahora = new Date();

            if (isNaN(nuevaFechaHora.getTime())) {
                e.preventDefault();
                showSimpleAlert('La fecha u hora seleccionada no es válida.');
                return;
            }

            if (nuevaFechaHora <= ahora) {
                e.preventDefault();
                showSimpleAlert('La nueva fecha y hora deben ser futuras.');
                return;
            }
        });
    }

    //cancelar cita
    const cancelarModalEl      = document.getElementById('cancelarModal');
    const cancelarCitaIdInput  = document.getElementById('cancelarCitaId');
    const cancelarCitaInfo     = document.getElementById('cancelarCitaInfo');
    const cancelarConfirmarBtn = document.getElementById('cancelarConfirmarBtn');
    const cancelarForm         = document.getElementById('cancelarForm');

   
    if (cancelarModalEl && cancelarCitaIdInput && cancelarCitaInfo) {
        cancelarModalEl.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if (!button) return;

            const citaId   = button.getAttribute("data-cita-id");
            const nombre   = button.getAttribute("data-cita-nombre");
            const fecha    = button.getAttribute("data-cita-fecha");
            const hora     = button.getAttribute("data-cita-hora");

            cancelarCitaIdInput.value = citaId || "";

            cancelarCitaInfo.innerHTML = `
                <p><strong>${nombre || 'Cita'}</strong></p>
                <p><i class="fas fa-calendar-alt me-1"></i> ${fecha || '--'}</p>
                <p><i class="fas fa-clock me-1"></i> ${hora || '--'}</p>
            `;
        });
    }

    // Confirmar cancelación → enviar el form una sola vez
    if (cancelarConfirmarBtn && cancelarForm) {
        cancelarConfirmarBtn.addEventListener("click", () => {
            cancelarForm.submit();
        });
    }

    // finaliza cita
    const finalizarModalEl     = document.getElementById('finalizarModal');
    const finalizarCitaIdInput = document.getElementById('finalizarCitaId');
    const finalizarCitaInfo    = document.getElementById('finalizarCitaInfo');

    if (finalizarModalEl && finalizarCitaIdInput && finalizarCitaInfo) {
        finalizarModalEl.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if (!button) return;

            const citaId = button.getAttribute("data-cita-id");
            const nombre = button.getAttribute("data-cita-nombre");
            const fecha  = button.getAttribute("data-cita-fecha");
            const hora   = button.getAttribute("data-cita-hora");

            finalizarCitaIdInput.value = citaId || "";

            finalizarCitaInfo.innerHTML = `
                <p><strong>${nombre || 'Cita'}</strong></p>
                <p><i class="fas fa-calendar-alt me-1"></i> ${fecha || '--'}</p>
                <p><i class="fas fa-clock me-1"></i> ${hora || '--'}</p>
            `;
        });
    }

   
    const modalButtons = document.querySelectorAll('.btn-modal');
    modalButtons.forEach(button => {
        button.addEventListener('mouseenter', function () {
            this.style.transform = 'translateY(-2px)';
            this.style.transition = 'transform 0.15s ease';
        });
        button.addEventListener('mouseleave', function () {
            this.style.transform = 'translateY(0)';
        });
    });

});

document.addEventListener("DOMContentLoaded", function () {

    const fromInput = document.getElementById("filterFrom");
    const toInput   = document.getElementById("filterTo");
    const clearBtn  = document.getElementById("clearFilters");
    const citas     = document.querySelectorAll(".cita-card");

    if (!fromInput || !toInput || citas.length === 0) return;

   
    flatpickr(fromInput, {
        locale: "es",
        dateFormat: "Y-m-d",
        disableMobile: true,
        onChange: filterCitas
    });

    flatpickr(toInput, {
        locale: "es",
        dateFormat: "Y-m-d",
        disableMobile: true,
        onChange: filterCitas
    });

   
    function filterCitas() {
        const fromDate = fromInput.value
            ? new Date(fromInput.value + "T00:00:00")
            : null;

        const toDate = toInput.value
            ? new Date(toInput.value + "T23:59:59")
            : null;

        citas.forEach(card => {
            const fechaAttr = card.getAttribute("data-fecha"); // Y-m-d
            if (!fechaAttr) return;

            const citaDate = new Date(fechaAttr + "T12:00:00");
            let visible = true;

            if (fromDate && citaDate < fromDate) visible = false;
            if (toDate && citaDate > toDate) visible = false;

            card.style.display = visible ? "" : "none";
        });
    }

    // Limpiar filtros
    if (clearBtn) {
        clearBtn.addEventListener("click", () => {
            fromInput.value = "";
            toInput.value = "";
            citas.forEach(card => card.style.display = "");
        });
    }

});

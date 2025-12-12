class AppointmentWizard {

    constructor() {
        this.currentStep = 1;
        this.selectedDoctor = null;
        this.selectedDate = null;
        this.selectedTime = null;
        this.availabilityCache = {};
        this.init();
    }

 
    init() {
        this.initDatePicker();
        this.initListeners();
        this.updateStepIndicator();
    }

   
    initDatePicker() {
        flatpickr("#datePicker", {
            locale: "default",
            minDate: "today",
            dateFormat: "Y-m-d",
            disableMobile: true,
            onChange: (selectedDates, dateStr) => this.handleDateSelection(dateStr)
        });
    }

    initListeners() {

        document.querySelectorAll('.doctor-card').forEach(card => {
            card.addEventListener('click', () => this.handleDoctorSelection(card));
        });

        const btnConfirmar = document.getElementById('btnConfirmarCita');
        if (btnConfirmar) {
            btnConfirmar.addEventListener('click', () => this.showConfirmationModal());
        }

        const modalConfirmButton = document.getElementById('modalConfirmButton');
        if (modalConfirmButton) {
            modalConfirmButton.addEventListener('click', () => {
                const form = document.getElementById('confirmAppointmentForm');
                if (form) form.submit();
            });
        }
    }

  
    handleDoctorSelection(card) {

        document.querySelectorAll('.doctor-card')
            .forEach(c => c.classList.remove('selected'));

        card.classList.add('selected');

        this.selectedDoctor = card.dataset.doctorId;

        const name = card.querySelector("h5").textContent;
        const infoDiv = document.getElementById("selectedDoctorInfo");

        if (infoDiv) {
            infoDiv.innerHTML = `
                <div class="alert alert-success mb-0">
                    <h6 class="fw-bold mb-1">${name}</h6>
                    <p class="mb-0">Doctor seleccionado</p>
                </div>
            `;
        }
    }

  
    async handleDateSelection(dateStr) {

        this.selectedDate = dateStr;
        if (!this.selectedDoctor) return;

        const status = document.getElementById("availabilityStatus");
        const container = document.getElementById("timeSlotsContainer");

        status.style.display = "block";
        status.textContent = "Cargando horarios...";
        status.className = "availability-status loading";
        container.innerHTML = "";

        const busy = await this.fetchBusyHours();
        this.renderTimeSlots(busy, dateStr);
    }

 
    async fetchBusyHours() {

        const body = `action=get_busy_slots&doctor_id=${this.selectedDoctor}&date=${this.selectedDate}`;

        try {
            const res = await fetch("", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body
            });

            const data = await res.json();
            return data.busy ?? [];

        } catch (err) {
            this.showErrorModal("Error consultando disponibilidad.");
            return [];
        }
    }

  
   generateSlotsByDay(dateStr) {
    if (!dateStr) return [];

    const [y, m, d] = dateStr.split('-').map(Number);
    const selectedDate = new Date(y, m - 1, d);
    const today = new Date();

    const isToday =
        selectedDate.getFullYear() === today.getFullYear() &&
        selectedDate.getMonth() === today.getMonth() &&
        selectedDate.getDate() === today.getDate();

    const currentMinutes = today.getHours() * 60 + today.getMinutes();

    let ranges = [];

    switch (selectedDate.getDay()) {
        case 0: // Domingo
            return [];

        case 6: // Sábado
            ranges = [["09:00", "15:00"]];
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
        let [h, m] = start.split(":").map(Number);

        while (true) {
            const slotMinutes = h * 60 + m;


            if (!isToday || slotMinutes > currentMinutes) {
                slots.push(`${String(h).padStart(2, "0")}:${String(m).padStart(2, "0")}`);
            }

            m += 30;
            if (m >= 60) {
                h++;
                m -= 60;
            }

            if (`${String(h).padStart(2, "0")}:${String(m).padStart(2, "0")}` >= end) {
                break;
            }
        }
    });

    return slots;
}

    renderTimeSlots(busy, dateStr) {

        const container = document.getElementById("timeSlotsContainer");
        const status = document.getElementById("availabilityStatus");

        const slots = this.generateSlotsByDay(dateStr);

        let countAvailable = 0;
        container.innerHTML = "";

        slots.forEach(time => {

            const isBusy = busy.includes(time);

            const div = document.createElement("div");
            div.className = `time-slot ${isBusy ? "unavailable" : "available"}`;
            div.textContent = time;

            if (!isBusy) {
                div.addEventListener("click", () => this.selectTime(div, time));
                countAvailable++;
            }

            container.appendChild(div);
        });

        if (countAvailable > 0) {
            status.textContent = `${countAvailable} horarios disponibles`;
            status.className = "availability-status available";
        } else {
            status.textContent = "No hay horarios disponibles para esta fecha.";
            status.className = "availability-status unavailable";
        }
    }


    selectTime(element, time) {

        document.querySelectorAll('.time-slot.selected')
            .forEach(x => x.classList.remove('selected'));

        element.classList.add("selected");
        this.selectedTime = time;

        document.getElementById("formDoctorId").value = this.selectedDoctor;
        document.getElementById("formFechaHora").value = `${this.selectedDate} ${time}:00`;
    }


    nextStep(step) {

        if (step === 2 && !this.selectedDoctor)
            return this.showErrorModal("Selecciona un doctor.");

        if (step === 3 && (!this.selectedDate || !this.selectedTime))
            return this.showErrorModal("Selecciona fecha y hora.");

        if (step === 3)
            this.updateAppointmentSummary();

        this.currentStep = step;
        this.updateStepIndicator();
        this.showStep();
    }

    previousStep(step) {
        this.currentStep = step;
        this.updateStepIndicator();
        this.showStep();
    }

    showStep() {
        document.querySelectorAll('.wizard-step')
            .forEach(s => s.classList.remove("active"));

        document.getElementById(`step${this.currentStep}`).classList.add("active");
    }

    updateStepIndicator() {
        document.querySelectorAll(".step").forEach(stepEl => {

            const num = parseInt(stepEl.dataset.step, 10);

            stepEl.classList.remove("active", "completed");

            if (num === this.currentStep) stepEl.classList.add("active");
            if (num < this.currentStep) stepEl.classList.add("completed");
        });
    }

   
updateAppointmentSummary() {

    const role = document.getElementById("userRole")?.value || "";
    let paciente = "";

    if (role === "Paciente") {
        paciente = document.getElementById("pacienteNombre")?.value.trim() || "";
    } else {
        const nombre = document.getElementById("extNombre")?.value.trim() || "";
        const apellido = document.getElementById("extApellido")?.value.trim() || "";

        paciente = (nombre || apellido)
            ? `${nombre} ${apellido}`.trim()
            : "(Paciente externo — complete los datos abajo)";
    }

    const card = document.querySelector(
        `.doctor-card[data-doctor-id="${this.selectedDoctor}"]`
    );

    const doctorName = card ? card.querySelector("h5").textContent : "No definido";

    const fecha = new Date(`${this.selectedDate}T${this.selectedTime}`);
    const formatted = fecha.toLocaleString("es-ES", {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });

    const motivo = document.getElementById("motivo")?.value.trim() ?? "";


    const html = `
        <div class="summary-item"><span>Paciente:</span><strong>${paciente}</strong></div>
        <div class="summary-item"><span>Doctor:</span><strong>${doctorName}</strong></div>
        <div class="summary-item"><span>Fecha y hora:</span><strong>${formatted}</strong></div>
        <div class="summary-item"><span>Duración:</span><strong>30 minutos</strong></div>
        <div class="summary-item"><span>Motivo:</span><strong>${motivo}</strong></div>
    `;

    document.getElementById("appointmentSummary").innerHTML = html;


    const modalDetails = document.getElementById("modalAppointmentDetails");
    if (modalDetails) {
        modalDetails.innerHTML = html;
    }
}


showConfirmationModal() {
    const motivoInput = document.getElementById("motivo");
    const motivo = motivoInput?.value.trim();

    // Validar si el motivo está vacío
    if (!motivo) {
        return this.showErrorModal("Debes indicar el motivo.");
    }

    const role = document.getElementById("userRole")?.value;

    if (role === "Empleado") {
        
        const campos = ["extCedula", "extNombre", "extApellido", "extTelefono", "extCorreo"];

        for (let c of campos) {
            const el = document.getElementById(c);
            if (!el || !el.value.trim()) {
                return this.showErrorModal("Debe completar todos los datos del paciente externo.");
            }
        }
    }

    this.updateAppointmentSummary(motivo);
    
    const modal = new bootstrap.Modal(document.getElementById("confirmModal"));
   
    modal.show();
}


    showErrorModal(message) {

        document.getElementById("errorModalMessage").textContent = message;

        const modal = new bootstrap.Modal(
            document.getElementById("errorModal")
        );

        modal.show();
    }
}


document.addEventListener("DOMContentLoaded", () => {
    window.wizard = new AppointmentWizard();
});

function consultarCedulaAPI() {
    let cedula = document.getElementById("extCedula")?.value.trim();

    if (!cedula || cedula.length < 9) return;


    document.getElementById("extNombre").value = "";
    document.getElementById("extApellido").value = "";
    
    console.log("Buscando datos de cédula:", cedula);

    fetch("https://apis.gometa.org/cedulas/" + cedula)
        .then(response => response.json())
        .then(data => {

            if (data && data.results && data.results.length > 0) {

                let persona = data.results[0];

                const nombre = persona.firstname || "";
                const apellido1 = persona.lastname1 || "";

                document.getElementById("extNombre").value = nombre.trim();
                document.getElementById("extApellido").value = apellido1.trim();

            } else {
                console.warn("⚠️ No se encontró información para esta cédula.");
            }
        })
        .catch(error => {
            console.error("⚠️ Error al conectar con la API:", error);
        });
}
class AppointmentWizard {

    constructor() {
        this.currentStep = 1;
        this.selectedDoctor = null;
        this.selectedDate = null;
        this.selectedTime = null;
        this.availabilityCache = {};
        this.init();
    }

    // ================================================================
    // 🔥 INICIALIZACIÓN
    // ================================================================
    init() {
        this.initDatePicker();
        this.initListeners();
        this.updateStepIndicator();
    }

    // ================================================================
    // 📅 FLATPICKR
    // ================================================================
 initDatePicker() {
    flatpickr("#datePicker", {
        locale: "default", // 🔹 asegura que esté en inglés
        minDate: "today",
        dateFormat: "Y-m-d",
        disableMobile: true,
        onChange: (d, dateStr) => this.handleDateSelection(dateStr)
    });
}
    // ================================================================
    // 📌 LISTENERS
    // ================================================================
    initListeners() {
        document.querySelectorAll('.cita-doctor-card').forEach(card => {
            card.addEventListener('click', () => this.handleDoctorSelection(card));
        });

        document.getElementById('btnConfirmarCita')
            .addEventListener('click', () => this.showConfirmationModal());

        document.getElementById('modalConfirmButton')
            .addEventListener('click', () => {
                document.getElementById('confirmAppointmentForm').submit();
            });
    }

    // ================================================================
    // 👨‍⚕️ SELECCIONAR DOCTOR
    // ================================================================
    handleDoctorSelection(card) {
        document.querySelectorAll('.cita-doctor-card')
            .forEach(c => c.classList.remove('selected'));

        card.classList.add('selected');

        this.selectedDoctor = card.dataset.doctorId;

        const name = card.querySelector(".cita-doctor-name").textContent;

        document.getElementById("selectedDoctorInfo").innerHTML = `
            <div class="alert alert-success shadow-sm">
                <i data-lucide="user-check"></i>
                Doctor seleccionado: <strong>${name}</strong>
            </div>
        `;

        lucide.createIcons();
    }

    // ================================================================
    // 📅 SELECCIÓN DE FECHA
    // ================================================================
    async handleDateSelection(dateStr) {
        this.selectedDate = dateStr;

        if (!this.selectedDoctor) return;

        const status = document.getElementById("availabilityStatus");
        status.textContent = "Cargando horarios...";
        status.className = "availability-message loading";

        const busy = await this.fetchBusyHours();
        this.renderTimeSlots(busy);
    }

    // ================================================================
    // 📡 AJAX – HORARIOS OCUPADOS
    // ================================================================
    async fetchBusyHours() {

        const body = `action=get_busy_slots&doctor_id=${this.selectedDoctor}&date=${this.selectedDate}`;

        const res = await fetch("", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body
        });

        const data = await res.json();

        return data.busy ?? [];
    }

    // ================================================================
    // 🧮 GENERAR HORARIOS
    // ================================================================
    generateAllSlots() {
        const slots = [];

        for (let h = 9; h < 18; h++) {
            slots.push(`${h.toString().padStart(2, "0")}:00`);
            slots.push(`${h.toString().padStart(2, "0")}:30`);
        }

        return slots;
    }

    // ================================================================
    // 🕒 RENDER HORARIOS
    // ================================================================
    renderTimeSlots(busy) {

        const container = document.getElementById("timeSlotsContainer");
        const status = document.getElementById("availabilityStatus");

        container.innerHTML = "";

        const allSlots = this.generateAllSlots();
        let count = 0;

        allSlots.forEach(time => {

            const isBusy = busy.includes(time);

            const div = document.createElement("div");
            div.className = `time-slot ${isBusy ? "unavailable" : "available"}`;
            div.textContent = time;

            if (!isBusy) {
                div.addEventListener("click", () => this.selectTime(div, time));
                count++;
            }

            container.appendChild(div);
        });

        if (count > 0) {
            status.textContent = `${count} horarios disponibles`;
            status.className = "availability-message available";
        } else {
            status.textContent = `No hay horarios disponibles`;
            status.className = "availability-message unavailable";
        }

        lucide.createIcons();
    }

    // ================================================================
    // 🕐 SELECCIONAR HORA
    // ================================================================
    selectTime(element, time) {
        document.querySelectorAll('.time-slot.selected')
            .forEach(x => x.classList.remove('selected'));

        element.classList.add("selected");

        this.selectedTime = time;

        document.getElementById("formDoctorId").value = this.selectedDoctor;
        document.getElementById("formFechaHora").value =
            `${this.selectedDate} ${time}:00`;
    }

    // ================================================================
    // 🔄 CAMBIAR PASO DEL WIZARD
    // ================================================================
    nextStep(step) {

        if (step === 2 && !this.selectedDoctor)
            return this.showErrorModal("Por favor selecciona un doctor antes de continuar.");

        if (step === 3 && (!this.selectedDate || !this.selectedTime))
            return this.showErrorModal("Debes seleccionar una fecha y una hora disponible.");

        if (step === 3) this.buildSummary();

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
        document.querySelectorAll('.cita-step-content')
            .forEach(s => s.classList.remove("active"));

        document.getElementById(`step${this.currentStep}`)
            .classList.add("active");
    }

    updateStepIndicator() {
        document.querySelectorAll(".cita-progress-step").forEach(step => {

            const num = parseInt(step.dataset.step);

            step.classList.remove("active", "completed");

            if (num === this.currentStep) step.classList.add("active");
            if (num < this.currentStep) step.classList.add("completed");
        });

        lucide.createIcons();
    }

    // ================================================================
    // 📄 RESUMEN FINAL DE LA CITA
    // ================================================================
    buildSummary() {

        const card = document.querySelector(
            `.cita-doctor-card[data-doctor-id="${this.selectedDoctor}"]`
        );

        const doctorName = card.querySelector(".cita-doctor-name").textContent;

        const fecha = new Date(`${this.selectedDate}T${this.selectedTime}`);

        const formatted = fecha.toLocaleString("es-ES", {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });

        const paciente = document.getElementById("pacienteNombre").value;

        document.getElementById("appointmentSummary").innerHTML = `
            <p><strong>Paciente:</strong> ${paciente}</p>
            <p><strong>Doctor:</strong> ${doctorName}</p>
            <p><strong>Fecha:</strong> ${formatted}</p>
            <p><strong>Hora:</strong> ${this.selectedTime}</p>
            <p><strong>Motivo:</strong> ${document.getElementById("motivo").value}</p>
        `;
    }

    // ================================================================
    // 📌 CONFIRMAR CITA (VALIDA MOTIVO)
    // ================================================================
    showConfirmationModal() {

        const motivo = document.getElementById("motivo").value.trim();

        if (!motivo)
            return this.showErrorModal("Debes indicar el motivo de la consulta.");

        const modal = new bootstrap.Modal(document.getElementById("confirmModal"));
        modal.show();
    }

    // ================================================================
    // 🚨 MODAL DE ERROR (REEMPLAZA alert())
    // ================================================================
    showErrorModal(message) {
        document.getElementById("errorModalMessage").textContent = message;

        const modal = new bootstrap.Modal(document.getElementById("errorModal"));
        modal.show();

        lucide.createIcons();
    }
}

// ================================================================
// 🚀 INICIALIZAR WIZARD
// ================================================================
document.addEventListener("DOMContentLoaded", () => {
    window.wizard = new AppointmentWizard();
});



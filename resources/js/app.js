import TomSelect from "tom-select";
import "tom-select/dist/css/tom-select.css";
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css";
import Swal from "sweetalert2";
window.Swal = Swal;
window.TomSelect = TomSelect;

// Inisialisasi jika diperlukan
document.addEventListener("DOMContentLoaded", function () {
    const selects = document.querySelectorAll(".tom-select");
    selects.forEach((select) => {
        new TomSelect(select, {});
    });
});

console.log("tom select");

import { Calendar } from "@fullcalendar/core";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin from "@fullcalendar/interaction";

document.addEventListener("DOMContentLoaded", function () {
    const calendarEl = document.getElementById("attendance-calendar");

    if (!calendarEl) return;

    const calendar = new Calendar(calendarEl, {
        plugins: [dayGridPlugin, interactionPlugin],
        initialView: "dayGridMonth",
        locale: "id",
        height: "auto",
        events: window.attendanceEvents,
        eventContent: function (arg) {
            const { event } = arg;
            const store = event.title;
            const jadwal = event.extendedProps.jadwal || "-";
            const real = event.extendedProps.real || "-";

            // Container toko dengan overflow hidden
            const storeWrapper = document.createElement("div");
            storeWrapper.className = "overflow-hidden relative w-full h-5";

            const storeEl = document.createElement("div");
            storeEl.className = "font-medium inline-block";
            storeEl.textContent = store;

            // Masukkan teks ke wrapper dulu
            storeWrapper.appendChild(storeEl);

            // Setelah dimasukkan, tunggu satu tick DOM, lalu cek panjang teks
            setTimeout(() => {
                if (storeEl.scrollWidth > storeWrapper.offsetWidth) {
                    storeEl.classList.add("marquee");
                }
            }, 0);

            const jadwalEl = document.createElement("div");
            jadwalEl.className = "text-xs text-gray-500 leading-tight truncate";
            jadwalEl.innerHTML = `<span class="block">${jadwal}</span>`;

            const realEl = document.createElement("div");
            realEl.className = "text-xs text-blue-500 leading-tight truncate";
            realEl.innerHTML = `<span class="block">${real}</span>`;

            return { domNodes: [storeWrapper, jadwalEl, realEl] };
        },

        dateClick: function (info) {
            const date = info.dateStr;
            const modal = document.getElementById("modal-" + date);
            if (modal) modal.classList.remove("hidden");
        },
    });

    calendar.render();
    function applyMarqueeIfNeeded() {
        document.querySelectorAll(".marquee-check").forEach((el) => {
            el.classList.remove("marquee");
            if (el.scrollWidth > el.offsetWidth) {
                el.classList.add("marquee");
            }
        });
    }

    setTimeout(applyMarqueeIfNeeded, 100); // cek setelah render
    window.addEventListener("resize", applyMarqueeIfNeeded); // cek jika layar diubah
});

// Global modal logic
const formatTime = (time) => {
    if (!time) return "-";
    const date = new Date(time);
    return date.toLocaleTimeString("id-ID", {
        hour: "2-digit",
        minute: "2-digit",
    });
};
window.showDetailModal = function (visit) {
    const modal = document.getElementById("global-detail-modal");
    const title = document.getElementById("detail-title");
    const body = document.getElementById("detail-body");

    const attendance = visit.attendance;

    const formatTime = (time) => {
        if (!time) return "-";
        const date = new Date(time);
        return date.toLocaleTimeString("id-ID", {
            hour: "2-digit",
            minute: "2-digit",
        });
    };

    const formatMoney = (amount) =>
        "Rp " + (amount ?? 0).toLocaleString("id-ID");

    title.textContent = `Detail Absensi - ${visit.store.name}`;

    const rows = `
        <div><dt class="font-medium">Sales</dt><dd>${
            visit.schedule.sales.name
        }</dd></div>
        <div><dt class="font-medium">Jadwal</dt><dd>${formatTime(
            visit.checkin_time
        )} - ${formatTime(visit.checkout_time)}</dd></div>
        <div><dt class="font-medium">Tanggal</dt><dd>${
            visit.schedule.visit_date
        }</dd></div>
        ${
            attendance
                ? `
            <div><dt class="font-medium">Check-in / Check-out</dt><dd>${formatTime(
                attendance.check_in_time
            )} - ${formatTime(attendance.check_out_time)}</dd></div>
            <div><dt class="font-medium">Tagihan</dt><dd>${formatMoney(
                attendance.actual_invoice_amount
            )}</dd></div>`
                : `<div><dt class="font-medium text-red-500">Status</dt><dd class="text-red-500">❌ Belum absen</dd></div>`
        }
    `;
    body.innerHTML = rows;
    modal.classList.remove("hidden");
};

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
            const idVisit = event.extendedProps.id_visit;
            const store = event.title;
            const jadwal = event.extendedProps.jadwal || "-";
            const real = event.extendedProps.real || "-";
            const attendance = event.extendedProps.attendance;
            const visitDate = event.start;

            const today = new Date();
            today.setHours(0, 0, 0, 0);
            let bgClass = "bg-gray-100 dark:bg-gray-700";

            if (attendance) {
                bgClass =
                    "bg-green-100 hover:bg-green-200 dark:bg-green-800 dark:hover:bg-green-700";
            } else if (!attendance && visitDate < today) {
                bgClass =
                    "bg-yellow-100 hover:bg-yellow-200 dark:bg-yellow-800 dark:hover:bg-yellow-700";
            } else {
                bgClass =
                    "bg-red-100 hover:bg-red-200 dark:bg-red-800 dark:hover:bg-red-700";
            }

            const container = document.createElement("div");
            container.className = `${bgClass} transition rounded px-1 py-0.5 cursor-pointer space-y-0.5`;
            container.title = "Klik untuk lihat detail";

            const storeWrapper = document.createElement("div");
            storeWrapper.className = "overflow-hidden relative w-full h-5";

            const storeEl = document.createElement("div");
            storeEl.className = "font-medium inline-block";
            storeEl.textContent = store;
            storeWrapper.appendChild(storeEl);
            container.appendChild(storeWrapper);

            const jadwalEl = document.createElement("div");
            jadwalEl.className = "text-xs text-gray-500 leading-tight truncate";
            jadwalEl.innerHTML = `<span class="block">${jadwal}</span>`;
            container.appendChild(jadwalEl);

            const realEl = document.createElement("div");
            realEl.className = "text-xs text-blue-500 leading-tight truncate";
            realEl.innerHTML = `<span class="block">${real}</span>`;
            container.appendChild(realEl);

            container.addEventListener("click", function () {
                const modal = document.getElementById("modal-visit-" + idVisit);
                if (modal) modal.classList.remove("hidden");
            });

            return { domNodes: [container] };
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

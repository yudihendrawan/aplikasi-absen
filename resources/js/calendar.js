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

        dateClick: function (info) {
            const date = info.dateStr;
            const modal = document.getElementById("modal-" + date);
            if (modal) {
                modal.classList.remove("hidden");
            }
        },
    });

    calendar.render();
});

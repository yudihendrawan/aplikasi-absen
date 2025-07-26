// resources/js/app.js
import "./bootstrap";
import "../css/app.css"; // pastikan ini tetap ada
import "./plugins";

import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css";

import TomSelect from "tom-select";
import "tom-select/dist/css/tom-select.css";

import Swal from "sweetalert2";
window.Swal = Swal;
window.TomSelect = TomSelect;
window.flatpickr = flatpickr; // <- ini penting agar bisa diakses di Blade

// Inisialisasi Tom Select jika diperlukan global
document.addEventListener("DOMContentLoaded", function () {
    const selects = document.querySelectorAll(".tom-select");
    selects.forEach((select) => {
        new TomSelect(select);
    });
});
function initControls(root = document) {
    // Init Flatpickr
    root.querySelectorAll("[data-date]:not([data-date-init])").forEach((el) => {
        flatpickr(el, { dateFormat: "Y-m-d" });
        el.setAttribute("data-date-init", "1");
    });

    // Init TomSelect
    root.querySelectorAll("[data-tom]:not([data-tom-init])").forEach((el) => {
        new TomSelect(el, {
            create: false,
            persist: false,
        });
        el.setAttribute("data-tom-init", "1");
    });
}

// document.addEventListener("DOMContentLoaded", () => {
//     window.initPlugins();

//     // Observer untuk detect elemen baru via fetch/ajax/modal
//     const observer = new MutationObserver((mutations) => {
//         mutations.forEach((mutation) => {
//             mutation.addedNodes.forEach((node) => {
//                 if (node.nodeType === 1) {
//                     window.initPlugins(node);
//                 }
//             });
//         });
//     });

//     observer.observe(document.body, {
//         childList: true,
//         subtree: true,
//     });
// });

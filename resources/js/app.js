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

import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css";
import TomSelect from "tom-select";
import "tom-select/dist/css/tom-select.css";

window.initPlugins = function (root = document) {
    // Init TomSelect
    root.querySelectorAll(".tom-select").forEach((el) => {
        if (!el.classList.contains("ts-initialized")) {
            new TomSelect(el);
            el.classList.add("ts-initialized");
        }
    });

    // Init Flatpickr (start_date & end_date)
    root.querySelectorAll("input[type=date], .flatpickr").forEach((el) => {
        if (!el.classList.contains("flatpickr-input")) {
            flatpickr(el, {
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "F j, Y",
                altInputClass:
                    "form-input block w-full rounded-lg border text-sm placeholder-gray-400",
            });
        }
    });
};

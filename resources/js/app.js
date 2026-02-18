import "toastify-js/src/toastify.css";
import Toastify from "toastify-js";
window.Toastify = Toastify
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css";
import * as echarts from 'echarts';
window.echarts = echarts

if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js', { scope: '/' }).then(function (registration) {
    }).catch(function (registrationError) {
    });
}

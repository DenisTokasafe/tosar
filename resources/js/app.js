import "toastify-js/src/toastify.css";
import Toastify from "toastify-js";
window.Toastify = Toastify
import flatpickr from "flatpickr";
import monthSelectPlugin from "flatpickr/dist/plugins/monthSelect/index";
import "flatpickr/dist/flatpickr.min.css";
import "flatpickr/dist/plugins/monthSelect/style.css";
import * as echarts from 'echarts';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
window.ClassicEditor = ClassicEditor;
window.echarts = echarts
window.flatpickr = flatpickr;
window.monthSelectPlugin = monthSelectPlugin.default || monthSelectPlugin;
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js', { scope: '/' }).then(function (registration) {
    }).catch(function (registrationError) {
    });
}

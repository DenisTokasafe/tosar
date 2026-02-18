import "toastify-js/src/toastify.css";
import Toastify from "toastify-js";
window.Toastify = Toastify
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css";
import * as echarts from 'echarts';
import { ClassicEditor, Essentials, Bold, Italic, Font, Paragraph, List } from 'ckeditor5';
import 'ckeditor5/ckeditor5.css';
window.echarts = echarts
window.ClassicEditor = ClassicEditor;
window.CKEditorPlugins = { Essentials, Bold, Italic, Font, Paragraph, List };
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js', { scope: '/' }).then(function (registration) {
    }).catch(function (registrationError) {
    });
}

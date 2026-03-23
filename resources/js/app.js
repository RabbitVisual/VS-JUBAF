import "./bootstrap";
import "./theme";
import "./auth-forms";
import "./masks";
import "./cep-helper";
import "./address-form";
import "./alerts";
import Alpine from "alpinejs";
import intersect from '@alpinejs/intersect';
import "flowbite";
import "./notifications";
import Sortable from "sortablejs";
import RichEditor from './components/rich-editor';

import mask from '@alpinejs/mask'
import collapse from '@alpinejs/collapse'

// Inicializa Alpine.js
window.Alpine = Alpine;
Alpine.plugin(intersect);
Alpine.plugin(collapse);
Alpine.plugin(mask);

Alpine.data('richEditor', RichEditor);

Alpine.start();


// Exporta Sortable para uso global
window.Sortable = Sortable;

import Chart from "chart.js/auto";
window.Chart = Chart;

import ApexCharts from "apexcharts";
window.ApexCharts = ApexCharts;

import { Html5Qrcode } from 'html5-qrcode';
window.Html5Qrcode = Html5Qrcode;

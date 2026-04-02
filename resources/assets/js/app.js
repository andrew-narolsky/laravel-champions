// Vendors
import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Bootstrap
import * as bootstrap from 'bootstrap'

// FormHelper
import FormHelper from './utils/form-helper';
window.FormHelper = FormHelper;

// SlimSelect
import SlimSelect from 'slim-select'
import 'slim-select/styles';
window.SlimSelect = SlimSelect;

// Notyf
import { Notyf } from 'notyf';
import 'notyf/notyf.min.css';
window.Notyf = new Notyf();

// Autoload modules
const modules = import.meta.glob('./modules/*.js', { eager: true });
Object.entries(modules).forEach(([path, module]) => {
    const name = path.split('/').pop().replace('.js', '').replace(/(^\w|-\w)/g, c => c.replace('-', '').toUpperCase());
    window[name] = module.default;
});

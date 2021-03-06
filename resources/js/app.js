/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('example-component', require('./components/ExampleComponent.vue').default);
Vue.component('frmtipoitem-component', require('./components/frmTipoItem.vue').default);
Vue.component('frmtipopaquete-component', require('./components/frmTipoPaquete').default);
Vue.component('frmsalon-component', require('./components/frmSalon').default);
Vue.component('frmbitacora-component', require('./components/frmBitacora').default);
Vue.component('frmpaquete-component', require('./components/frmPaquete').default);
Vue.component('frmcliente-component', require('./components/frmCliente').default);
Vue.component('frmempleado-component', require('./components/frmEmpleado').default);
Vue.component('frmitem-component', require('./components/frmItem').default);
Vue.component('FrmNewItem-component', require('./components/FrmNewItem').default);
Vue.component('frmnotaservicio-component', require('./components/FrmNotaServicio').default);
// conponente para que el cliente haga su reserva,se registre y haga login
Vue.component('frmclientereg-component', require('./components/frmClienteReg').default);
Vue.component('frmreserva-component', require('./components/frmReserva').default);
Vue.component('frmreservatodos-component', require('./components/frmReservaTodos').default);
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    data : {
        menu : 1
    }
});

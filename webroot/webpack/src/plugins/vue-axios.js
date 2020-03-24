import Vue from 'vue'
import axios from 'axios'
import VueAxios from 'vue-axios'

axios.defaults.baseURL = !PRODUCTION
    ? 'http://miller.edge.com'
    // : 'http://miller.edge.com'
    : 'http://intranet.edg3web.com'

Vue.use(VueAxios, axios)
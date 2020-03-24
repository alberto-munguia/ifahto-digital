import Vue from 'vue'
import FancyGridVue from 'fancy-grid-vue'
import Fancy from 'fancygrid'

import 'fancygrid/client/modules/paging.min'
import 'fancygrid/client/modules/sort.min'
import 'fancygrid/client/modules/filter.min'
import 'fancygrid/client/modules/grid.min'
import 'fancygrid/client/modules/dom.min'

import 'fancygrid/client/modules/i18n/es'

Vue.use(Fancy)
Vue.component('FancyGridVue', FancyGridVue)

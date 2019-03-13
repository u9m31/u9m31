import Vue from 'vue'
import Router from 'vue-router'
Vue.use(Router)

// 
import example_component from '../components/ExampleComponent.vue'
import admin_component   from '../components/AdminComponent.vue'
import r_link            from '../components/RouterLink.vue'

//
Vue.component('example-component', example_component)
Vue.component('admin-component', admin_component)
Vue.component('r-link', r_link)

// 
import home              from '../components/HomeComponent.vue'
//import admin_user        from '../components/Admin/UserComponent.vue'
//import admin_payslip     from '../components/Admin/PayslipComponent.vue'
//import admin_actlog      from '../components/Admin/ActlogComponent.vue'

export default new Router({
  mode: 'history',
  routes: [
    { path: '/home',          name: 'home',          component: home,          meta: {name: 'ホーム',   icon: 'home'}},

    { path: '/admin/actlog', name: 'admin_actlog',   meta: {name: '操作履歴', icon: 'list'},
      component: resolve => { require.ensure(['../components/Admin/ActlogComponent.vue'], () => {
                              resolve(require('../components/Admin/ActlogComponent.vue'))
                            }, 'js/admin_actlog') }},
    { path: '/admin/user',    name: 'admin_user',   meta: {name: '社員管理', icon: 'supervisor_account'},
      component: resolve => { require.ensure(['../components/Admin/UserComponent.vue'], () => {
                              resolve(require('../components/Admin/UserComponent.vue'))
                            }, 'js/admin_user') }},
    { path: '/admin/payslip', name: 'admin_payslip', meta: {name: '給与明細', icon: 'fa-file-invoice-dollar'},
      component: resolve => { require.ensure(['../components/Admin/PayslipComponent.vue'], () => {
                              resolve(require('../components/Admin/PayslipComponent.vue'))
                            }, 'js/admin_payslip') }},

    { path: '*',             redirect: '/home' },
  ],
})

import Vuex from 'vuex';

import layout from './modules/layout';
import notification from './modules/layout';
import todo from './modules/todo';

const store = new Vuex.Store({
  state: {
    permissions: []
},
  modules: {
    layout: layout, // Register the layout module
    notification, // Register the notifications module
    todo

    // Add more modules as needed
  },
  mutations: {
    setPermissions(state, permissions) {
        state.permissions = permissions;
    }
},
  actions: {
    fetchPermissions({ commit }) {
        return axios.get('/user/permissions')
            .then(response => {
                commit('setPermissions', response.data.data);
            })
            .catch(error => {
                console.error(error);
            });
    }
},
getters: {
  permissions: state => state.permissions
}
});

export default store;


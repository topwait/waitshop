import Vue from 'vue'
import Vuex from 'vuex'
import Storage from '../utils/storage.js'

Vue.use(Vuex)

const store = new Vuex.Store({
	state: {
		config: {
			defaultAvatar: '',
			diyBottomNav: {},
			diyBottomStyle: {}
		},
		token:   Storage.get('TOKEN') || null,
		isLogin: Storage.get('TOKEN') || null,
		historyLists: []
	},
	mutations: {
		LOGIN(state, loginRes) {
			state.isLogin = loginRes.token
			state.token = loginRes.token
			Storage.set('TOKEN', loginRes.token, 7200)
		},
		LOGOUT(state) {
			state.token = undefined;
			state.isLogin = undefined,
			Storage.remove('TOKEN')
		},
		SET_HISTORY_LISTS(state, history) {
			state.historyLists = history
		},
		SET_CONFIG(state, data) {
			state.config = Object.assign(state.config, data)
			Storage.set('CONFIG', data)
		}
	},
	actions: {
		set_history({commit, state}, history) {
			if (history instanceof Object) {
				let list = state.historyLists
				let newList = [history]
				list.forEach(function(item) {
					if (history.name !== item.name) {
						newList.push(item)
					}
				})
				commit('SET_HISTORY_LISTS', newList)
			} else {
				commit('SET_HISTORY_LISTS', [])
			}
		}
	}
})

export default store
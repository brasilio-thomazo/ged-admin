import { defineStore } from 'pinia'
import { ref } from 'vue'
import _ from 'lodash'

export const useStore = defineStore('main.store', () => {
  const user = ref<User>()
  const is_admin = ref(false)

  function setUser(payload: User) {
    user.value = payload
    is_admin.value = _.findIndex(payload.groups, { is_admin: true }) >= 0
  }

  return { setUser, user, is_admin }
})

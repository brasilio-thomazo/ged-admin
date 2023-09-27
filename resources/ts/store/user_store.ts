import { defineStore } from 'pinia'
import { ref } from 'vue'
import lodash from 'lodash'

const init: User = {
  id: '',
  name: '',
  document: '',
  role: '',
  phone: '',
  email: '',
  username: '',
  created_at: '',
  updated_at: '',
  groups: [],
}

export const useUserStore = defineStore('user.store', () => {
  const rows = ref<User[]>([])
  const user = ref<User>({ ...init })

  function create(payload: User) {
    rows.value.unshift(payload)
  }

  function edit(payload: User) {
    rows.value = rows.value.map((r) => (r.id === payload.id ? payload : r))
  }

  function destroy(payload: User) {
    const index = lodash.findIndex(rows.value, { id: payload.id })
    if (index >= 0) rows.value.splice(index, 1)
  }

  function setRows(payload: User[]) {
    rows.value = payload
  }

  return { rows, user, create, edit, destroy, setRows }
})

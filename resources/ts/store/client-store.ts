import { defineStore } from 'pinia'
import { ref } from 'vue'
import lodash from 'lodash'

export const useClientStore = defineStore('client.store', () => {
  const rows = ref<Client[]>([])

  function create(payload: Client) {
    rows.value.unshift(payload)
  }

  function edit(payload: Client) {
    rows.value = rows.value.map((r) => (r.id === payload.id ? payload : r))
  }

  function destroy(payload: Client) {
    const index = lodash.findIndex(rows.value, { id: payload.id })
    if (index >= 0) rows.value.splice(index, 1)
  }

  function setRows(payload: Client[]) {
    rows.value = payload
  }

  return { rows, create, edit, destroy, setRows }
})

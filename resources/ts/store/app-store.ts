import { defineStore } from 'pinia'
import { ref } from 'vue'
import lodash from 'lodash'

export const useAppStore = defineStore('app.store', () => {
  const rows = ref<App[]>([])

  function create(payload: App) {
    rows.value.unshift(payload)
  }

  function edit(payload: App) {
    rows.value = rows.value.map((r) => (r.id === payload.id ? payload : r))
  }

  function destroy(payload: App) {
    const index = lodash.findIndex(rows.value, { id: payload.id })
    if (index >= 0) rows.value.splice(index, 1)
  }

  function setRows(payload: App[]) {
    rows.value = payload
  }

  return { rows, create, edit, destroy, setRows }
})

import { defineStore } from 'pinia'
import { ref, shallowRef } from 'vue'
import lodash from 'lodash'

export const useTabStore = defineStore('tab.store', () => {
  const tabs = ref<Tab[]>([])
  const slots = ref<TabSlot>({
    list: { slot: null, icon: '' },
    create: { slot: null, icon: '' },
    edit: { slot: null, icon: '' },
    show: { slot: null, icon: '' },
    error: { slot: null, icon: 'error' },
  })
  const index = ref(0)
  const exlcude = ref('*')

  function setMain(title: string, icon: string, type: SlotType, slot: any) {
    tabs.value = []
    tabs.value.push({ title, icon, type, slot })
    index.value = 0
  }

  async function addTab(title: string, type: SlotType) {
    if (slots.value[type].slot) {
      const i = lodash.findIndex(tabs.value, { type })
      if (i < 0) {
        tabs.value.push({
          title,
          icon: slots.value[type].icon,
          type,
          slot: shallowRef(slots.value[type].slot),
        })
      } else {
        tabs.value[i].title = title
      }
      index.value = i < 0 ? tabs.value.length - 1 : i
    }
  }

  function setSlot(payload: TabSlot) {
    slots.value = payload
  }

  function setIndex(payload: number) {
    index.value = payload
  }

  function setExclude(payload: string) {
    exlcude.value = payload
  }

  function closeFromType(payload: SlotType) {
    const i = lodash.findIndex(tabs.value, { type: payload })
    if (i >= 0) closeFromIndex(i)
  }

  function closeFromIndex(payload: number) {
    if (payload - 1 >= 0) index.value = payload - 1
    tabs.value.splice(payload, 1)
  }

  function close() {
    closeFromIndex(index.value)
  }

  return {
    slots,
    tabs,
    addTab,
    setSlot,
    index,
    setMain,
    setIndex,
    exlcude,
    setExclude,
    close,
    closeFromIndex,
    closeFromType,
  }
})

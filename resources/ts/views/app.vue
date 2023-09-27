<template>
  <div id="user-view">
    <TabPane @edit="edit" @show="show">
      <div class="buttons">
        <button @click="create" type="button">
          <span class="material-icons">add</span>
          <span class="text">Nova aplicação</span>
        </button>
      </div>
    </TabPane>
  </div>
</template>

<script setup lang="ts">
import AppList from '@/components/AppList.vue'
import { useTabStore } from '@/store/tab-store'
import TabPane from '@/components/TabPane.vue'
import { inject, shallowRef } from 'vue'
import axios from 'axios'
import { useAppStore } from '@/store/app-store'
import AppCreate from '@/components/AppCreate.vue'
import AppEdit from '@/components/AppEdit.vue'
import AppShow from '@/components/AppShow.vue'

const tab = useTabStore()
const store = useAppStore()
const http = inject('http', axios)

tab.setSlot({
  list: { slot: AppList, icon: 'apps' },
  create: { slot: AppCreate, icon: 'library_add' },
  edit: { slot: AppEdit, icon: 'settings_applications' },
  show: { slot: AppShow, icon: 'preview' },
  error: {},
})

tab.setMain('Aplicações', 'apps', 'list', shallowRef(AppList))
tab.setExclude('AppCreate')

async function create() {
  tab.addTab('Cadastro', 'create')
}

async function edit(payload: App) {
  tab.closeFromType('show')
  tab.addTab(payload.id, 'edit')
}

async function show(payload: App) {
  tab.closeFromType('edit')
  tab.addTab(payload.id, 'show')
}

try {
  if (store.rows.length === 0) {
    const { data } = await http.get<App[]>('app')
    store.setRows(data)
  }
} catch ({ response }: any) {
  console.log('ERROR: ', response)
}
</script>

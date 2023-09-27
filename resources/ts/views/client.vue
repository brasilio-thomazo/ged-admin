<template>
  <div id="client-view">
    <TabPane @edit="edit" @show="show">
      <div class="buttons">
        <button @click="create" type="button">
          <span class="material-icons">add</span>
          <span class="text">Nova organização</span>
        </button>
      </div>
    </TabPane>
  </div>
</template>

<script setup lang="ts">
import { useClientStore } from '@/store/client-store'
import ClientList from '@/components/ClientList.vue'
import { useTabStore } from '@/store/tab-store'
import TabPane from '@/components/TabPane.vue'
import { inject, shallowRef } from 'vue'
import axios from 'axios'
import ClientCreate from '@/components/ClientCreate.vue'
import ClientEdit from '@/components/ClientEdit.vue'
import ClientShow from '@/components/ClientShow.vue'

const tab = useTabStore()
const store = useClientStore()
const http = inject('http', axios)

tab.setSlot({
  list: { slot: ClientList, icon: 'list' },
  create: { slot: ClientCreate, icon: 'box_add' },
  edit: { slot: ClientEdit, icon: 'folder_managed' },
  show: { slot: ClientShow, icon: 'preview' },
  error: {},
})

tab.setMain('Organizações', 'list', 'list', shallowRef(ClientList))
tab.setExclude('ClientCreate')

async function create() {
  tab.addTab('Cadastro', 'create')
}

async function edit(payload: Client) {
  tab.closeFromType('show')
  tab.addTab(payload.name, 'edit')
}

async function show(payload: Client) {
  tab.closeFromType('edit')
  tab.addTab(payload.name, 'show')
}

try {
  if (store.rows.length === 0) {
    const { data } = await http.get<Client[]>('client')
    store.setRows(data)
  }
} catch ({ response }: any) {
  console.log('ERROR: ', response)
}
</script>

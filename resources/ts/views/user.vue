<template>
  <div id="user-view">
    <TabPane @edit="edit" @show="show">
      <div class="buttons">
        <button @click="create" type="button">
          <span class="material-icons">person_add</span>
          <span class="text">Novo usuário</span>
        </button>
      </div>
    </TabPane>
  </div>
</template>

<script setup lang="ts">
import { useTabStore } from '@/store/tab-store'
import TabPane from '@/components/TabPane.vue'
import UserList from '@/components/UserList.vue'
import { inject, shallowRef } from 'vue'
import { useUserStore } from '@/store/user_store'
import axios from 'axios'
import UserCreate from '@/components/UserCreate.vue'
import UserEdit from '@/components/UserEdit.vue'
import UserShow from '@/components/UserShow.vue'

const tab = useTabStore()
const store = useUserStore()
const http = inject('http', axios)

tab.setSlot({
  list: { slot: UserList, icon: 'list' },
  create: { slot: UserCreate, icon: 'person_add' },
  edit: { slot: UserEdit, icon: 'manage_accounts' },
  show: { slot: UserShow, icon: 'preview' },
  error: {},
})

tab.setMain('Usuários', 'list', 'list', shallowRef(UserList))
tab.setExclude('UserCreate')

async function create() {
  tab.addTab('Cadastro', 'create')
}

async function edit(payload: User) {
  tab.closeFromType('show')
  tab.addTab(payload.username, 'edit')
}

async function show(payload: User) {
  tab.closeFromType('edit')
  tab.addTab(payload.username, 'show')
}

try {
  if (store.rows.length === 0) {
    const { data } = await http.get<User[]>('user')
    store.setRows(data)
  }
} catch ({ response }: any) {
  console.log('ERROR: ', response)
}
</script>

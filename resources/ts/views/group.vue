<template>
  <div id="group-view">
    <TabPane @edit="edit">
      <div class="buttons">
        <button @click="create" type="button">
          <span class="material-icons">person_add</span>
          <span class="text">Novo grupo</span>
        </button>
      </div>
    </TabPane>
  </div>
</template>

<script setup lang="ts">
import { useTabStore } from '@/store/tab-store'
import TabPane from '@/components/TabPane.vue'
import { useGroupStore } from '@/store/group_store'
import GroupList from '@components/GroupList.vue'
import { inject, shallowRef } from 'vue'
import axios from 'axios'
import GroupCreate from '@/components/GroupCreate.vue'
import GroupEdit from '@/components/GroupEdit.vue'

const tab = useTabStore()
const store = useGroupStore()
const http = inject('http', axios)

tab.setSlot({
  list: { slot: GroupList, icon: 'list' },
  create: { slot: GroupCreate, icon: 'group_add' },
  edit: { slot: GroupEdit, icon: 'edit_note' },
  show: {},
  error: {},
})

tab.setMain('Grupos', 'list', 'list', shallowRef(GroupList))
tab.setExclude('GroupCreate')

async function create() {
  tab.addTab('Cadastro', 'create')
}

async function edit(payload: Group) {
  tab.addTab(payload.name, 'edit')
}

try {
  if (store.rows.length === 0) {
    const { data } = await http.get<Group[]>('group')
    store.setRows(data)
  }
} catch ({ response }: any) {
  console.log('ERROR: ', response)
}
</script>

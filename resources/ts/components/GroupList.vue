<template>
  <div id="group-list">
    <div class="table">
      <table>
        <caption>
          <div class="data">
            <div class="title">Grupos cadastrados</div>
          </div>
        </caption>
        <thead>
          <tr>
            <th>Nome</th>
            <th>Grupos</th>
            <th>Usuários</th>
            <th>Clientes</th>
            <th>Aplicações</th>
            <th>Data</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="row in store.rows" :key="row.id">
            <td>{{ row.name }}</td>
            <td>
              <div class="permits">
                <div v-if="row.privilege?.group === 'rw'">
                  <span class="material-icons">check</span>
                  <span class="text">Escrita</span>
                </div>
                <div v-else-if="row.privilege?.group === 'r'">
                  <span class="material-icons">check</span>
                  <span class="text">Leitura</span>
                </div>
              </div>
            </td>
            <td>
              <div class="permits">
                <div v-if="row.privilege?.user === 'rw'">
                  <span class="material-icons">check</span>
                  <span class="text">Escrita</span>
                </div>
                <div v-else-if="row.privilege?.user === 'r'">
                  <span class="material-icons">check</span>
                  <span class="text">Leitura</span>
                </div>
              </div>
            </td>
            <td>
              <div class="permits">
                <div v-if="row.privilege?.client === 'rw'">
                  <span class="material-icons">check</span>
                  <span class="text">Escrita</span>
                </div>
                <div v-else-if="row.privilege?.client === 'r'">
                  <span class="material-icons">check</span>
                  <span class="text">Leitura</span>
                </div>
              </div>
            </td>
            <td>
              <div class="permits">
                <div v-if="row.privilege?.app === 'rw'">
                  <span class="material-icons">check</span>
                  <span class="text">Escrita</span>
                </div>
                <div v-else-if="row.privilege?.app === 'r'">
                  <span class="material-icons">check</span>
                  <span class="text">Leitura</span>
                </div>
              </div>
            </td>
            <td>{{ dateFormat(row.created_at) }}</td>
            <td>
              <div class="buttons">
                <button @click="emit('edit', row)" type="button" class="icon">
                  <span class="material-icons">edit</span>
                </button>
                <button @click="destroy(row)" type="button" class="icon">
                  <span class="material-icons">delete</span>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup lang="ts">
import axios from 'axios'
import { inject } from 'vue'
import { dateFormat } from '@/provider'
import { useGroupStore } from '@/store/group_store'
const store = useGroupStore()
const http = inject('http', axios)

const emit = defineEmits<{ (e: 'edit', payload: Group): void }>()

async function destroy(group: Group) {
  try {
    if (confirm(`Tem certeza que deseja remover o grupo ${group.name}`)) {
      await http.delete(`group/${group.id}`)
      store.destroy(group)
    }
  } catch ({ response }: any) {}
}
</script>

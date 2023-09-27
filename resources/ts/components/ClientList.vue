<template>
  <div class="table">
    <table>
      <caption>
        <div class="data">
          <div class="title">Organizações cadastradas</div>
        </div>
      </caption>
      <thead>
        <tr>
          <th>Nome</th>
          <th>Telefone</th>
          <th>E-mail</th>
          <th>Tipo</th>
          <th>Responsável</th>
          <th>Data</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="row in store.rows">
          <td>{{ row.name }}</td>
          <td>{{ mask(row.phone, true) }}</td>
          <td>{{ row.email }}</td>
          <td>{{ scope[row.scope] }}</td>
          <td>{{ row.manager }}</td>
          <td>{{ dateFormat(row.created_at) }}</td>
          <td>
            <div class="buttons">
              <button @click="emit('show', row)" type="button" class="icon">
                <span class="material-icons">pageview</span>
              </button>
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
</template>

<script setup lang="ts">
import axios from 'axios'
import { inject } from 'vue'
import { dateFormat, mask } from '@/provider'
import { useClientStore } from '@/store/client-store'
const store = useClientStore()
const http = inject('http', axios)
const emit = defineEmits<{
  (e: 'edit', payload: Client): void
  (e: 'show', payload: Client): void
}>()
const scope = { client: 'Cliente', agent: 'Distribuidor' }

async function destroy(client: Client) {
  try {
    const message = `Tem certeza que deseja remover a organização ${client.name}?`
    if (confirm(message)) {
      await http.delete(`client/${client.id}`)
      store.destroy(client)
    }
  } catch ({ response }: any) {}
}
</script>

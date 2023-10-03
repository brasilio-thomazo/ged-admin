<template>
  <div id="app-list">
    <div class="table">
      <table>
        <caption>
          <div class="data">
            <div class="title">Aplicações cadastradas</div>
          </div>
        </caption>
        <thead>
          <tr>
            <th>Organização</th>
            <th>Tipo</th>
            <th>Path</th>
            <th>URL</th>
            <th>Banco de dados</th>
            <th>Uptime/Status</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="row in store.rows" :key="row.id">
            <td>{{ row.client.name }}</td>
            <td class="upper">
              {{ row.application === 0 ? 'cliente' : 'distribuidor' }}
            </td>
            <td>{{ row.path }}</td>
            <td class="lower">http://{{ row.subdomain }}</td>
            <td class="lower">
              {{ row.db_name }}
            </td>
            <td>
              <div class="status">
                <span class="uptime">{{ interval(row.started_at) }}</span>
                <span v-if="row.started_at" class="material-icons online">
                  wifi
                </span>
                <span v-else="row.started_at" class="material-icons offline">
                  wifi_off
                </span>
              </div>
            </td>
            <td>
              <div class="buttons">
                <button @click="emit('show', row)" type="button" class="icon">
                  <span class="material-icons">pageview</span>
                </button>
                <button
                  v-if="me.is_admin"
                  @click="emit('edit', row)"
                  type="button"
                  class="icon"
                >
                  <span class="material-icons">edit</span>
                </button>
                <button
                  v-if="me.is_admin"
                  @click="destroy(row)"
                  type="button"
                  class="icon"
                >
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
import { useAppStore } from '@/store/app-store'
import { inject } from 'vue'
import axios from 'axios'
import { interval } from '@/provider'
import { useStore } from '@/store/store'
const store = useAppStore()
const http = inject('http', axios)
const me = useStore()

const emit = defineEmits<{
  (e: 'edit', payload: App): void
  (e: 'show', payload: App): void
}>()

async function destroy(app: App) {
  try {
    if (confirm(`Tem certeza que deseja remover a applicação ${app.id}?`)) {
      await http.delete(`app/${app.id}`)
      store.destroy(app)
    }
  } catch ({ response }: any) {}
}
</script>

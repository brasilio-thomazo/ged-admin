<template>
  <div id="app-show">
    <div class="show">
      <fieldset>
        <legend>Aplicação</legend>
        <div class="data">
          <div class="line">
            <div class="label">Cliente:</div>
            <div class="text">{{ app.client.name }}</div>
          </div>
          <div class="line">
            <div class="label">Aplicação:</div>
            <div class="text upper">{{ app.application }}</div>
          </div>
        </div>
      </fieldset>
      <fieldset>
        <legend>HTTP</legend>
        <div class="data">
          <div class="line">
            <div class="label">URL:</div>
            <div class="text">
              http://{{ `${app.domain}:${app.http_port}` }}
            </div>
          </div>
          <div class="line">
            <div class="label">Status:</div>
            <div class="text">
              <div class="status">
                <span class="date" v-if="app.started_at">
                  {{ dateFormat(app.started_at) }}
                </span>
                <span v-if="app.started_at" class="material-icons online">
                  wifi
                </span>
                <span v-else class="material-icons offline">wifi_off</span>
              </div>
            </div>
          </div>
          <div class="line">
            <div class="label">Uptime:</div>
            <div class="text">{{ uptime }}</div>
          </div>
          <div class="buttons">
            <button v-if="app.started_at" type="button" @click="reload">
              <span class="material-symbols-outlined">refresh</span>
              <span class="text">Reiniciar</span>
            </button>
            <button v-else type="button" @click="start">
              <span class="material-symbols-outlined">play_arrow</span>
              <span class="text">Iniciar</span>
            </button>
          </div>
        </div>
      </fieldset>
      <fieldset>
        <legend>Banco de dados</legend>
        <div class="data">
          <div class="line">
            <div class="label">Conexão:</div>
            <div class="text upper">{{ app.db_type }}</div>
          </div>
          <div class="line">
            <div class="label">Servidor:</div>
            <div class="text">{{ app.db_host }}:{{ app.db_port }}</div>
          </div>
          <div class="line">
            <div class="label">Banco de dados:</div>
            <div class="text">{{ app.db_name }}</div>
          </div>
          <div v-if="app.installed_at" class="line">
            <div class="label">Instalação:</div>
            <div class="text">{{ dateFormat(app.installed_at) }}</div>
          </div>
          <div class="buttons">
            <button
              v-if="app.started_at && !app.installed_at"
              type="button"
              @click="install"
            >
              <span class="material-symbols-outlined">database</span>
              <span class="text">Instalar</span>
            </button>
            <button v-else-if="app.started_at" type="button" @click="reinstall">
              <span class="material-symbols-outlined">source_notes</span>
              <span class="text">Reinstalar</span>
            </button>
          </div>
        </div>
      </fieldset>
      <fieldset>
        <legend>Cache / Session</legend>
        <div class="data">
          <div class="line">
            <div class="label">Cache Driver:</div>
            <div class="text upper">{{ app.cache_driver }}</div>
          </div>
          <div class="line">
            <div class="label">Session Driver:</div>
            <div class="text upper">{{ app.session_driver }}</div>
          </div>
          <div class="line">
            <div class="label">Redis:</div>
            <div class="text">{{ app.redis_host }}:{{ app.redis_port }}</div>
          </div>
          <div class="line">
            <div class="label">Memcached:</div>
            <div class="text">{{ app.memcached_host }}</div>
          </div>
        </div>
      </fieldset>
    </div>
  </div>
</template>

<script setup lang="ts">
import { dateFormat, interval } from '@/provider'
import { useAppStore } from '@/store/app-store'
import axios from 'axios'
import { computed, inject, ref } from 'vue'

interface Response {
  code: number
  command: string
  data: string
}

const props = defineProps<{ data: App }>()
const http = inject('http', axios)
const result = ref<Response[]>()
const store = useAppStore()

const app = ref<App>({ ...props.data })
const uptime = computed(() => interval(app.value.started_at))

function reload() {
  const api = axios.create({
    withCredentials: false,
    baseURL: 'http://127.0.0.1:8080/api',
  })
  api.post('/reload')
}

function waitRestart() {
  const api = axios.create({
    withCredentials: false,
    baseURL: `http://${app.value.domain}:${app.value.http_port}`,
  })

  const interval = setInterval(async () => {
    api.get('api/ping').then((res) => {
      if (res.data === 'pong') {
        http.put(`app/${app.value.id}/start`).then(({ data }) => {
          store.edit(data)
          app.value.started_at = data.started_at
        })
        clearInterval(interval)
      }
    })
  }, 1000)
}

async function start() {
  try {
    await http.post<Response[]>(`app/${app.value.id}/start`)
  } catch ({ response }: any) {
    result.value = response?.data
  }
  waitRestart()
}

async function install() {
  const api = axios.create({
    withCredentials: false,
    baseURL: `http://${app.value.domain}:${app.value.http_port}`,
  })
  try {
    await api.post('api/migrate')
    const { data } = await http.put<App>(`app/${app.value.id}/install`)
    store.edit(data)
    app.value.installed_at = data.installed_at
  } catch ({ response }: any) {
    if (response?.data) alert(response.data)
  }
}

async function reinstall() {
  if (!confirm('Isso irá remover o banco de dados e recriar, continuar?'))
    return

  const api = axios.create({
    withCredentials: false,
    baseURL: `http://${app.value.domain}:${app.value.http_port}`,
  })
  try {
    await api.put('api/migrate')
    const { data } = await http.put<App>(`app/${app.value.id}/install`)
    store.edit(data)
    app.value.installed_at = data.installed_at
  } catch ({ response }: any) {
    if (response?.data) alert(response.data.message)
  }
}
</script>

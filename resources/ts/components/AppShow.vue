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
            <div class="label">Tipo de aplicação:</div>
            <div class="text upper">
              {{ app.application === 0 ? 'cliente' : 'distribuidor' }}
            </div>
          </div>
          <div class="line">
            <div class="label">Instalação/Alteração</div>
            <div class="pre">kubectl apply -f {{ k8sURL }}</div>
          </div>
          <div class="line">
            <div class="label">Remoção</div>
            <div class="pre">kubectl delete -f {{ k8sURL }}</div>
          </div>
        </div>
      </fieldset>
      <fieldset>
        <legend>HTTP</legend>
        <div class="data">
          <div class="line">
            <div class="label">URL:</div>
            <div class="text">
              http://{{ app.use_domain ? app.domain : app.subdomain }}
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
        </div>
      </fieldset>
      <fieldset>
        <legend>Banco de dados</legend>
        <div class="data">
          <div v-if="app.use_custom" class="line">
            <div class="label">Conexão:</div>
            <div class="text upper">{{ app.db_type }}</div>
          </div>
          <div v-if="app.use_custom" class="line">
            <div class="label">Servidor:</div>
            <div class="text">{{ app.db_host }}:{{ app.db_port }}</div>
          </div>
          <div class="line">
            <div class="label">Banco de dados:</div>
            <div class="text">{{ app.db_name }}</div>
          </div>
        </div>
      </fieldset>
      <fieldset>
        <legend>Cache / Session</legend>
        <div class="data">
          <div v-if="app.use_custom" class="line">
            <div class="label">Cache Driver:</div>
            <div class="text upper">{{ app.cache_driver }}</div>
          </div>
          <div v-if="app.use_custom" class="line">
            <div class="label">Session Driver:</div>
            <div class="text upper">{{ app.session_driver }}</div>
          </div>
          <div v-if="app.use_custom" class="line">
            <div class="label">Redis:</div>
            <div class="text">{{ app.redis_host }}:{{ app.redis_port }}</div>
          </div>
          <div v-if="app.use_custom" class="line">
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
import axios from 'axios'
import { computed, inject, onMounted, ref } from 'vue'

const http = inject('http', axios)
const props = defineProps<{ data: App }>()
const app = ref<App>({ ...props.data })
const uptime = computed(() => interval(app.value.started_at))
const k8sURL = ref('')

onMounted(async () => {
  const { data } = await http.post(`k8s/${app.value.id}`, {})
  k8sURL.value = data.url
})
</script>

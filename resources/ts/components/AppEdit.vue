<template>
  <div id="app-edit">
    <form @submit.prevent="save">
      <div class="form">
        <div class="line">
          <label for="client_id">Organização:</label>
          <select id="client_id" v-model="form.client_id" required>
            <option v-for="c in client.rows" :value="c.id" :key="c.id">
              {{ c.name }}
            </option>
          </select>
          <span v-if="error?.errors?.client_id" class="error">
            {{ error.errors.client_id.join(',') }}
          </span>
        </div>
        <div class="line">
          <label for="use_custom">Domínio:</label>
          <div class="items">
            <div class="item">
              <label class="box" for="use_domain">
                <input
                  type="checkbox"
                  name="use_domain"
                  v-model="form.use_domain"
                  id="use_domain"
                />
                <span class="text">Usar domínio</span>
              </label>
            </div>
          </div>
        </div>
        <div v-if="form.use_domain" class="line">
          <label for="domain">Domínio:</label>
          <input type="text" id="domain" v-model="form.domain" required />
          <span v-if="error?.errors?.domain" class="error">
            {{ error.errors.domain.join(',') }}
          </span>
        </div>
        <div class="line">
          <label for="use_custom">Configurações:</label>
          <div class="items">
            <div class="item">
              <label class="box" for="use_custom">
                <input
                  type="checkbox"
                  name="use_custom"
                  v-model="form.use_custom"
                  id="use_custom"
                />
                <span class="text">Personalizar configurações</span>
              </label>
            </div>
          </div>
        </div>
        <div v-if="form.use_custom" class="line">
          <label for="redis_host">Redis servidor:</label>
          <input type="redis_host" id="redis_host" v-model="form.redis_host" />
          <span v-if="error?.errors?.redis_host" class="error">
            {{ error.errors.redis_host.join(',') }}
          </span>
        </div>
        <div v-if="form.use_custom" class="line">
          <label for="redis_port">Redis porta:</label>
          <input type="number" id="redis_port" v-model="form.redis_port" />
          <span v-if="error?.errors?.redis_port" class="error">
            {{ error.errors.redis_port.join(',') }}
          </span>
        </div>
        <div v-if="form.use_custom" class="line">
          <label for="redis_pass">Redis senha:</label>
          <input type="password" id="redis_pass" v-model="form.redis_pass" />
          <span v-if="error?.errors?.redis_pass" class="error">
            {{ error.errors.redis_pass.join(',') }}
          </span>
        </div>
        <div v-if="form.use_custom" class="line">
          <label for="memcached_host">Memcached servidor:</label>
          <input
            type="text"
            id="memcached_host"
            v-model="form.memcached_host"
          />
          <span v-if="error?.errors?.memcached_host" class="error">
            {{ error.errors.memcached_host.join(',') }}
          </span>
        </div>
        <div v-if="form.use_custom" class="line">
          <label for="group">Driver:</label>
          <div class="items">
            <div class="item">
              <label class="box" for="db_mysql">
                <input
                  type="radio"
                  name="db_type"
                  v-model="form.db_type"
                  value="mysql"
                  id="db_mysql"
                />
                <span class="text">MariaDB/MySQL</span>
              </label>
            </div>
            <div class="item">
              <label class="box" for="db_pgsql">
                <input
                  type="radio"
                  name="db_type"
                  v-model="form.db_type"
                  value="pgsql"
                  id="db_pgsql"
                />
                <span class="text">PostgreSQL/PGBounce</span>
              </label>
            </div>
            <div class="item">
              <label class="box" for="db_sqlite">
                <input
                  type="radio"
                  name="db_type"
                  v-model="form.db_type"
                  value="sqlite"
                  id="db_sqlite"
                />
                <span class="text">Sqlite3</span>
              </label>
            </div>
          </div>
          <span v-if="error?.errors?.db_type" class="error">
            {{ error.errors.db_type.join(',') }}
          </span>
        </div>
        <div v-if="form.use_custom" class="line">
          <label for="db_host">Servidor:</label>
          <input type="text" id="db_host" v-model="form.db_host" />
          <span v-if="error?.errors?.db_host" class="error">
            {{ error.errors.db_host.join(',') }}
          </span>
        </div>
        <div v-if="form.use_custom" class="line">
          <label for="db_port">Porta:</label>
          <input type="number" id="db_port" v-model="form.db_port" />
          <span v-if="error?.errors?.db_port" class="error">
            {{ error.errors.db_port.join(',') }}
          </span>
        </div>
        <div v-if="form.use_custom" class="line">
          <label for="super_user">Usuário (SUPER):</label>
          <input type="text" id="super_user" v-model="form.super_user" />
          <span v-if="error?.errors?.super_user" class="error">
            {{ error.errors.super_user.join(',') }}
          </span>
        </div>
        <div v-if="form.use_custom" class="line">
          <label for="super_pass">Senha (SUPER):</label>
          <input type="password" id="super_pass" v-model="form.super_pass" />
          <span v-if="error?.errors?.super_pass" class="error">
            {{ error.errors.super_pass.join(',') }}
          </span>
        </div>
        <div v-if="form.use_custom" class="line">
          <label for="db_user">Usuário:</label>
          <input type="text" id="db_user" v-model="form.db_user" />
          <span v-if="error?.errors?.db_user" class="error">
            {{ error.errors.db_user.join(',') }}
          </span>
        </div>
        <div v-if="form.use_custom" class="line">
          <label for="db_pass">Senha:</label>
          <input type="password" id="db_pass" v-model="form.db_pass" />
          <span v-if="error?.errors?.db_pass" class="error">
            {{ error.errors.db_pass.join(',') }}
          </span>
        </div>
        <div class="line">
          <label for="db_name">Banco de dados (nome):</label>
          <input type="text" id="db_name" v-model="form.db_name" />
          <span v-if="error?.errors?.db_name" class="error">
            {{ error.errors.db_name.join(',') }}
          </span>
        </div>
        <div v-if="form.use_custom" class="line">
          <label for="group">Cache driver:</label>
          <div class="items">
            <div class="item">
              <label class="box" for="redis">
                <input
                  type="radio"
                  name="cache_driver"
                  v-model="form.cache_driver"
                  value="redis"
                  id="redis"
                />
                <span class="text">Redis</span>
              </label>
            </div>
            <div class="item">
              <label class="box" for="memcached">
                <input
                  type="radio"
                  name="cache_driver"
                  v-model="form.cache_driver"
                  value="memcached"
                  id="memcached"
                />
                <span class="text">Memcached</span>
              </label>
            </div>
            <div class="item">
              <label class="box" for="file">
                <input
                  type="radio"
                  name="cache_driver"
                  v-model="form.cache_driver"
                  value="file"
                  id="file"
                />
                <span class="text">File</span>
              </label>
            </div>
          </div>
          <span v-if="error?.errors?.cache_driver" class="error">
            {{ error.errors.cache_driver.join(',') }}
          </span>
        </div>
        <div v-if="form.use_custom" class="line">
          <label for="group">Session driver:</label>
          <div class="items">
            <div class="item">
              <label class="box" for="session_redis">
                <input
                  type="radio"
                  name="session_driver"
                  v-model="form.session_driver"
                  value="redis"
                  id="session_redis"
                />
                <span class="text">Redis</span>
              </label>
            </div>
            <div class="item">
              <label class="box" for="session_memcached">
                <input
                  type="radio"
                  name="session_driver"
                  v-model="form.session_driver"
                  value="memcached"
                  id="session_memcached"
                />
                <span class="text">Memcached</span>
              </label>
            </div>
            <div class="item">
              <label class="box" for="session_file">
                <input
                  type="radio"
                  name="session_driver"
                  v-model="form.session_driver"
                  value="file"
                  id="session_file"
                />
                <span class="text">File</span>
              </label>
            </div>
          </div>
          <span v-if="error?.errors?.session_driver" class="error">
            {{ error.errors.session_driver.join(',') }}
          </span>
        </div>
        <div class="buttons">
          <button type="submit">
            <span class="material-icons">save</span>
            <span class="text">Salvar</span>
          </button>
        </div>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { useClientStore } from '@/store/client-store'
import { computed, inject, ref } from 'vue'
import axios from 'axios'
import { useAppStore } from '@/store/app-store'

const emit = defineEmits<{ (e: 'close'): void }>()
const props = defineProps<{ data: App }>()
const init = computed(
  (): AppRequest => ({
    application: props.data.application,
    client_id: props.data.client_id,
    path: props.data.path,
    domain: props.data.domain,
    redis_host: props.data.redis_host,
    redis_port: props.data.redis_port,
    memcached_host: props.data.memcached_host,
    db_type: props.data.db_type,
    db_host: props.data.db_host,
    db_port: props.data.db_port,
    db_name: props.data.db_name,
    cache_driver: props.data.cache_driver,
    session_driver: props.data.session_driver,
    use_custom: props.data.use_custom,
    use_domain: props.data.use_domain,
  })
)

const http = inject('http', axios)

const client = useClientStore()
const store = useAppStore()

const error = ref<AppError>()
const form = ref<AppRequest>({ ...init.value })

async function save() {
  try {
    const request = { ...form.value }
    const path = `app/${props.data.id}`
    const { data } = await http.put<App>(path, request)
    store.edit(data)
    emit('close')
  } catch ({ response }: any) {
    console.log(response.data)
    error.value = response?.data
  }
}

try {
  if (client.rows.length == 0) {
    const { data } = await http.get<Client[]>('client')
    client.setRows(data)
  }
} catch ({ response }: any) {}
</script>

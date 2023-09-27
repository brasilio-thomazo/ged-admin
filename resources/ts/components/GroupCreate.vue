<template>
  <div id="group-create">
    <form @submit.prevent="save" @reset.prevent="reset">
      <div class="form">
        <div class="line">
          <label for="name">Nome:</label>
          <input type="text" id="name" v-model="form.name" />
          <span v-if="error?.errors?.name" class="error">
            {{ error.errors.name.join(',') }}
          </span>
        </div>
        <div class="line">
          <p>Privilégios:</p>
          <div class="groups">
            <fieldset class="group">
              <legend>Grupos:</legend>
              <label class="box" for="group_read">
                <input
                  type="radio"
                  name="group"
                  value="r"
                  v-model="form.privilege.group"
                  id="group_read"
                />
                <span class="icon"></span>
                <span class="text">Leitura</span>
              </label>
              <label class="box" for="group_write">
                <input
                  type="radio"
                  name="group"
                  value="rw"
                  v-model="form.privilege.group"
                  id="group_write"
                />
                <span class="icon"></span>
                <span class="text">Escrita</span>
              </label>
            </fieldset>
            <fieldset class="group">
              <legend>Usuários:</legend>
              <label class="box" for="user_read">
                <input
                  type="radio"
                  name="user"
                  value="r"
                  v-model="form.privilege.user"
                  id="user_read"
                />
                <span class="text">Leitura</span>
              </label>
              <label class="box" for="user_write">
                <input
                  type="radio"
                  name="user"
                  value="rw"
                  v-model="form.privilege.user"
                  id="user_write"
                />
                <span class="text">Escrita</span>
              </label>
            </fieldset>
            <fieldset class="group">
              <legend>Organizações:</legend>
              <label class="box" for="client_read">
                <input
                  type="radio"
                  name="client"
                  value="r"
                  v-model="form.privilege.client"
                  id="client_read"
                />
                <span class="text">Leitura</span>
              </label>
              <label class="box" for="client_write">
                <input
                  type="radio"
                  name="client"
                  value="rw"
                  v-model="form.privilege.client"
                  id="client_write"
                />
                <span class="text">Escrita</span>
              </label>
            </fieldset>
            <fieldset class="group">
              <legend>Aplicações:</legend>
              <label class="box" for="app_read">
                <input
                  type="radio"
                  name="app"
                  value="r"
                  v-model="form.privilege.app"
                  id="app_read"
                />
                <span class="text">Leitura</span>
              </label>
              <label class="box" for="app_write">
                <input
                  type="radio"
                  name="app"
                  value="rw"
                  v-model="form.privilege.app"
                  id="app_write"
                />
                <span class="text">Escrita</span>
              </label>
            </fieldset>
          </div>
          <span v-if="error?.errors?.privilege" class="error">
            {{ error.errors.privilege.join(',') }}
          </span>
        </div>
        <div class="buttons">
          <button @click="reset" type="button">
            <span class="material-icons">clear_all</span>
            <span class="text">Limpar</span>
          </button>
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
import { useGroupStore } from '@/store/group_store'
import { inject, ref, watchEffect } from 'vue'
import axios from 'axios'

const emit = defineEmits<{ (e: 'close'): void }>()
const init = { name: '', privilege: {} }

const http = inject('http', axios)

const store = useGroupStore()

const error = ref<GroupError>()
const form = ref<GroupRequest>({ ...init })

async function save() {
  try {
    const request = { ...form.value }
    const { data } = await http.post<Group>('group', request)
    store.create(data)
    emit('close')
  } catch ({ response }: any) {
    console.log(response.data)
    error.value = response?.data
  }
}

function reset() {
  form.value.privilege = {}
}

function setReadPermission() {
  const user = form.value.privilege.user
  const group = form.value.privilege.group
  const client = form.value.privilege.client
  const app = form.value.privilege.app
  if (user === 'rw' && group === undefined) {
    form.value.privilege.group = 'r'
  }
  if (client !== undefined && /r|rw/.test(client) && app === undefined) {
    form.value.privilege.app = 'r'
  }
  if (app !== undefined && /r|rw/.test(app) && client === undefined) {
    form.value.privilege.client = 'r'
  }
}

watchEffect(setReadPermission)
</script>

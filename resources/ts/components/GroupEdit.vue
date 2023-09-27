<template>
  <div id="group-edit">
    <form @submit.prevent="save">
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
                <input type="radio" name="group" value="r" v-model="form.privilege.group" id="group_read" />
                <span class="icon"></span>
                <span class="text">Leitura</span>
              </label>
              <label class="box" for="group_write">
                <input type="radio" name="group" value="rw" v-model="form.privilege.group" id="group_write" />
                <span class="icon"></span>
                <span class="text">Escrita</span>
              </label>
            </fieldset>
            <fieldset class="group">
              <legend>Usuários:</legend>
              <label class="box" for="user_read">
                <input type="radio" name="user" value="r" v-model="form.privilege.user" id="user_read" />
                <span class="text">Leitura</span>
              </label>
              <label class="box" for="user_write">
                <input type="radio" name="user" value="rw" v-model="form.privilege.user" id="user_write" />
                <span class="text">Escrita</span>
              </label>
            </fieldset>
            <fieldset class="group">
              <legend>Organizações:</legend>
              <label class="box" for="client_read">
                <input type="radio" name="client" value="r" v-model="form.privilege.client" id="client_read" />
                <span class="text">Leitura</span>
              </label>
              <label class="box" for="client_write">
                <input type="radio" name="client" value="rw" v-model="form.privilege.client" id="client_write" />
                <span class="text">Escrita</span>
              </label>
            </fieldset>
            <fieldset class="group">
              <legend>Aplicações:</legend>
              <label class="box" for="app_read">
                <input type="radio" name="app" value="r" v-model="form.privilege.app" id="app_read" />
                <span class="text">Leitura</span>
              </label>
              <label class="box" for="app_write">
                <input type="radio" name="app" value="rw" v-model="form.privilege.app" id="app_write" />
                <span class="text">Escrita</span>
              </label>
            </fieldset>
          </div>
          <span v-if="error?.errors?.privilege" class="error">
            {{ error.errors.privilege.join(',') }}
          </span>
        </div>
        <div v-if="error?.message" class="error">{{ error.message }}</div>
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
import { computed, inject, ref } from 'vue'
import axios from 'axios'

const props = defineProps<{ data: Group }>()
const emit = defineEmits<{ (e: 'close'): void }>()
const init = computed(() => ({
  name: props.data.name,
  privilege: {
    group: props.data.privilege.group,
    user: props.data.privilege.user,
    client: props.data.privilege.client,
    app: props.data.privilege.app
  },
}))

const http = inject('http', axios)

const store = useGroupStore()

const error = ref<GroupError>()
const form = ref<GroupRequest>({ ...init.value })

async function save() {
  try {
    const request = { ...form.value }
    const { data } = await http.put<Group>(`group/${props.data.id}`, request)
    store.edit(data)
    emit('close')
  } catch ({ response }: any) {
    console.log(response.data)
    error.value = response?.data
  }
}

function reset() {
  form.value.privilege = {}
}
</script>

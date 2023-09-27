<template>
  <div id="user-edit">
    <form @submit.prevent="save">
      <div class="form">
        <div class="line">
          <label for="name">Nome:</label>
          <input type="text" id="name" v-model="form.name" required />
          <span v-if="error?.errors?.name" class="error">
            {{ error.errors.name.join(',') }}
          </span>
        </div>
        <div class="line">
          <label for="document">CPF/CNPJ:</label>
          <input
            type="tel"
            id="document"
            v-model="form.document"
            v-maska="document"
            data-maska="['###.###.###-##', '##.###.###/####-##']"
            pattern="^[0-9]{2,3}\.[0-9]{3}\.[0-9]{3}((\/[0-9]{4})?)-[0-9]{2}$"
            required
          />
          <span v-if="error?.errors?.document" class="error">
            {{ error.errors.document.join(',') }}
          </span>
        </div>
        <div class="line">
          <label for="role">Cargo:</label>
          <input type="text" id="role" v-model="form.role" required />
          <span v-if="error?.errors?.role" class="error">
            {{ error.errors.role.join(',') }}
          </span>
        </div>
        <div class="line">
          <label for="phone">Telefone:</label>
          <input
            type="tel"
            id="phone"
            v-model="form.phone"
            v-maska="phone"
            data-maska="['(##) #####-####', '(##) ####-####']"
            pattern="^\([0-9]{2}\) (9?)[0-9]{4}-[0-9]{4}$"
            required
          />
          <span v-if="error?.errors?.phone" class="error">
            {{ error.errors.phone.join(',') }}
          </span>
        </div>
        <div class="line">
          <label for="email">E-mail:</label>
          <input type="email" id="email" v-model="form.email" />
          <span v-if="error?.errors?.email" class="error">
            {{ error.errors.email.join(',') }}
          </span>
        </div>
        <div class="line">
          <label for="username">Usuário:</label>
          <input type="text" id="username" v-model="form.username" />
          <span v-if="error?.errors?.username" class="error">
            {{ error.errors.username.join(',') }}
          </span>
        </div>
        <div class="line">
          <label for="password">Senha:</label>
          <input type="password" id="password" v-model="form.password" />
          <span v-if="error?.errors?.password" class="error">
            {{ error.errors.password.join(',') }}
          </span>
        </div>
        <div class="line">
          <label for="password_confirmation">Confirme a senha:</label>
          <input
            type="password"
            id="password_confirmation"
            v-model="form.password_confirmation"
          />
        </div>
        <div class="line">
          <label for="group">Grupos:</label>
          <div class="items">
            <div class="item" v-for="group in gStore.rows">
              <label class="box" :for="`group_${group.id}`">
                <input
                  type="checkbox"
                  v-model="form.groups"
                  :value="group.id"
                  :id="`group_${group.id}`"
                />
                <span class="icon"></span>
                <span class="text">{{ group.name }}</span>
              </label>
            </div>
          </div>
          <span v-if="error?.errors?.groups" class="error">
            {{ error.errors.groups.join(',') }}
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
import { useGroupStore } from '@/store/group_store'
import { useUserStore } from '@/store/user_store'
import { computed, inject, ref } from 'vue'
import { vMaska, MaskaDetail } from 'maska'
import axios from 'axios'

const props = defineProps<{ data: User }>()
const emit = defineEmits<{ (e: 'close'): void }>()
const init = computed(() => ({
  name: props.data.name,
  document: props.data.document,
  role: props.data.role,
  phone: props.data.phone,
  email: props.data.email,
  username: props.data.username,
  groups: props.data.groups.map((g) => g.id),
  password: '',
  password_confirmation: '',
}))

const maskDetail = {
  masked: '',
  unmasked: '',
  completed: false,
}

const http = inject('http', axios)

const gStore = useGroupStore()
const store = useUserStore()

const error = ref<UserError>()
const form = ref<UserRequest>({ ...init.value })

const document = ref<MaskaDetail>({ ...maskDetail })
const phone = ref<MaskaDetail>({ ...maskDetail })

async function save() {
  try {
    const request = {
      ...form.value,
      document: document.value.unmasked,
      phone: phone.value.unmasked,
    }
    const { data } = await http.put<User>(`user/${props.data.id}`, request)
    store.edit(data)
    emit('close')
  } catch ({ response }: any) {
    console.log(response.data)
    error.value = response?.data
  }
}

if (gStore.rows.length === 0) {
  try {
    const { data } = await http.get<Group[]>('group')
    gStore.rows = data
  } catch ({ response }: any) {}
}
</script>

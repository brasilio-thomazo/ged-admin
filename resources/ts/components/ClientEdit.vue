<template>
  <div id="client-edit">
    <form @submit.prevent="save">
      <div class="form">
        <div class="line">
          <label for="name">Razão social:</label>
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
            v-model="form.identity"
            v-maska="document"
            data-maska="['###.###.###-##', '##.###.###/####-##']"
            pattern="^[0-9]{2,3}\.[0-9]{3}\.[0-9]{3}((\/[0-9]{4})?)-[0-9]{2}$"
            required
          />
          <span v-if="error?.errors?.identity" class="error">
            {{ error.errors.identity.join(',') }}
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
          <label for="group">Tipo:</label>
          <div class="items">
            <div class="item">
              <label class="box" for="scope_client">
                <input
                  type="radio"
                  name="scope"
                  v-model="form.scope"
                  value="client"
                  id="scope_client"
                />
                <span class="text">Cliente</span>
              </label>
            </div>
            <div class="item">
              <label class="box" for="scope_agent">
                <input
                  type="radio"
                  name="scope"
                  v-model="form.scope"
                  value="agent"
                  id="scope_agent"
                />
                <span class="text">Distribuidor</span>
              </label>
            </div>
          </div>
          <span v-if="error?.errors?.scope" class="error">
            {{ error.errors.scope.join(',') }}
          </span>
        </div>
        <div class="line">
          <label for="manager">Responsável:</label>
          <input type="text" id="manager" v-model="form.manager" />
          <span v-if="error?.errors?.manager" class="error">
            {{ error.errors.manager.join(',') }}
          </span>
        </div>
        <div class="line">
          <label for="role">Cargo:</label>
          <input type="text" id="role" v-model="form.role" required />
          <span v-if="error?.errors?.role" class="error">
            {{ error.errors.role.join(',') }}
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
import { vMaska, MaskaDetail } from 'maska'
import axios from 'axios'

const props = defineProps<{ data: Client }>()
const emit = defineEmits<{ (e: 'close'): void }>()
const init = computed(
  (): ClientRequest => ({
    name: props.data.name,
    identity: props.data.identity,
    phone: props.data.phone,
    email: props.data.email,
    scope: props.data.scope,
    manager: props.data.manager,
    role: props.data.role,
  })
)

const maskDetail = {
  masked: '',
  unmasked: '',
  completed: false,
}

const http = inject('http', axios)

const store = useClientStore()

const error = ref<ClientError>()
const form = ref<ClientRequest>({ ...init.value })

const document = ref<MaskaDetail>({ ...maskDetail })
const phone = ref<MaskaDetail>({ ...maskDetail })

async function save() {
  try {
    const request = {
      ...form.value,
      identity: document.value.unmasked,
      phone: phone.value.unmasked,
    }
    const { data } = await http.put<Client>(`client/${props.data.id}`, request)
    store.edit(data)
    emit('close')
  } catch ({ response }: any) {
    console.log(response.data)
    error.value = response?.data
  }
}
</script>

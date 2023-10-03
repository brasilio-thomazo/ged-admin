<template>
  <div id="client-show">
    <div class="show">
      <fieldset>
        <legend>Organizacional</legend>
        <div class="data">
          <div class="line">
            <div class="label">Razão social:</div>
            <div class="text upper">{{ props.data.name }}</div>
          </div>
          <div class="line">
            <div class="label">CPF/CNPJ:</div>
            <div class="text">{{ mask(props.data.identity) }}</div>
          </div>
          <div class="line">
            <div class="label">Tipo:</div>
            <div class="text">{{ scope[props.data.scope] }}</div>
          </div>
          <div class="line">
            <div class="label">Registro:</div>
            <div class="text">{{ dateFormat(props.data.created_at) }}</div>
          </div>
        </div>
      </fieldset>
      <fieldset>
        <legend>Contato</legend>
        <div class="data">
          <div class="line">
            <div class="label">Telefone:</div>
            <div class="text">{{ mask(props.data.phone, true) }}</div>
          </div>
          <div class="line">
            <div class="label">E-mail:</div>
            <div class="text">{{ props.data.email }}</div>
          </div>
        </div>
      </fieldset>
      <fieldset>
        <legend>Responsável</legend>
        <div class="data">
          <div class="line">
            <div class="label">Nome:</div>
            <div class="text upper">{{ props.data.manager }}</div>
          </div>
          <div class="line">
            <div class="label">Cargo:</div>
            <div class="text upper">{{ props.data.role }}</div>
          </div>
        </div>
      </fieldset>
    </div>
  </div>
</template>

<script setup lang="ts">
import { dateFormat, mask } from '@/provider'

const props = defineProps<{ data: Client }>()
const scope = { client: 'Cliente', agent: 'Distribuidor' }
</script>

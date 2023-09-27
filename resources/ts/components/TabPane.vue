<template>
  <div id="groups">
    <div class="tabpane">
      <slot name="header" />
      <div class="header">
        <div
          v-for="(tab, i) in store.tabs"
          :class="['title', { active: store.index === i }]"
        >
          <button @click="store.setIndex(i)" type="button">
            <span v-if="tab.icon" class="material-symbols-outlined">
              {{ tab.icon }}
            </span>
            <span class="text">{{ tab.title }}</span>
          </button>
          <button
            v-if="i !== 0"
            @click="store.closeFromIndex(i)"
            type="button"
            class="material-symbols-outlined"
          >
            close
          </button>
        </div>
      </div>
      <div class="content">
        <div class="tab" v-if="store.tabs[store.index]">
          <KeepAlive :exclude="store.exlcude">
            <component
              :is="store.tabs[store.index].slot"
              @edit="edit"
              @close="store.close"
              @show="show"
              :data="data"
            />
          </KeepAlive>
        </div>
      </div>
      <slot />
    </div>
  </div>
</template>

<script setup lang="ts">
import { useTabStore } from '@/store/tab-store'
import { ref } from 'vue'
const store = useTabStore()
const emit = defineEmits<{
  (e: 'edit', payload: any): void
  (e: 'show', payload: any): void
}>()
const data = ref()

function edit(payload: any) {
  data.value = payload
  emit('edit', payload)
}

function show(payload: any) {
  data.value = payload
  emit('show', payload)
}
</script>

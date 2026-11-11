<script setup>
import { RouterView, useRoute, useRouter } from 'vue-router'
import { computed, ref } from 'vue';
import { storeToRefs } from 'pinia';
import { useAuthStore } from '@/stores/auth';
import { useNotificacaoStore } from '@/stores/notificacao';

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();
const notificacao = useNotificacaoStore();
const { usuario } = storeToRefs(authStore);

const drawer = ref(true);
const menu = computed(() => !route.meta.publica)

const iniciais = computed(() => (usuario.value?.name ?? '?')
  .split(' ').map(p => p[0]).slice(0, 2).join('').toUpperCase());

const sair = async () => {
  await authStore.logout();
  router.push('/');
}
</script>

<template>
  <v-app>
    <template v-if="menu">
      <v-app-bar flat color="background" border="b">
        <v-app-bar-nav-icon @click="drawer=!drawer" />
        <v-app-bar-title class="font-weight-bold">Agenda de Tarefas</v-app-bar-title>
        <v-menu>
          <template #activator="{ props }">
            <v-btn v-bind="props" variant="text" class="mr-2">
              <v-avatar color="secondary" size="32" class="mr-2 text-caption font-weight-bold">
                {{ iniciais }}
              </v-avatar>
              <span class="d-none d-sm-inline">{{ usuario?.name }}</span>
            </v-btn>
          </template>
          <v-list density="compact">
            <v-list-item :subtitle="usuario?.email" :title="usuario?.name" />
            <v-divider />
            <v-list-item prepend-icon="mdi-logout" title="Sair" @click="sair" />
          </v-list>
        </v-menu>
      </v-app-bar>
      <v-navigation-drawer v-model="drawer" color="primary">
        <v-list nav class="mt-2">
          <v-list-item prepend-icon="mdi-format-list-checks" title="Tarefas" to="/tarefa" rounded="lg" />
          <v-list-item prepend-icon="mdi-tag-multiple-outline" title="Categorias" to="/categoria" rounded="lg" />
        </v-list>
      </v-navigation-drawer>
    </template>
    <v-main>
      <v-container>
        <RouterView />
      </v-container>
    </v-main>
    <v-snackbar v-model="notificacao.visivel" :color="notificacao.cor" location="top right" timeout="3000">
      {{ notificacao.texto }}
    </v-snackbar>
  </v-app>
</template>

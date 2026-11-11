<script setup>
import { RouterLink, RouterView, useRoute } from 'vue-router'
import { computed, ref } from 'vue';

const route = useRoute();
const drawer = ref(true);
const menu = computed(() => {
  return route.path !== '/' 
})
</script>

<template>
  <v-app>
    <v-app-bar title="Gerenciador de tarefas" color="primary">
      <v-app-bar-nav-icon @click="drawer=!drawer" />
    </v-app-bar>
    <v-navigation-drawer v-model="drawer" color="#ccc" 
    v-if="menu">
      <v-list-item title="" subtitle=""></v-list-item>
      <v-divider></v-divider>
      <v-list-item link density="compact"
      title="Tarefas" href="/tarefa"></v-list-item>
      <v-list-item link density="compact"
      title="Categorias" href="/categoria"></v-list-item>
    </v-navigation-drawer>
    <v-main>
      <v-container>
        <RouterView />
      </v-container>
    </v-main>
  </v-app>
</template>

<style scoped>
</style>

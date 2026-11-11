<script setup>
    import { ref } from 'vue';
    import { useRouter } from 'vue-router';
    import { useAuthStore } from '@/stores/auth';
    import { mensagemErro } from '@/utils/erros';
    import AuthCard from './AuthCard.vue';

    const authStore = useAuthStore();
    const router = useRouter();

    const item = ref({
        email:"",
        password:""
    });
    const carregando = ref(false);
    const erro = ref('');
    const mostrarSenha = ref(false);

    async function login(){
        erro.value = '';
        carregando.value = true;
        try{
            await authStore.login(item.value.email, item.value.password);
            router.push('/tarefa');
        }catch(error){
            erro.value = mensagemErro(error, 'Erro ao realizar o login');
        }finally{
            carregando.value = false;
        }
    }
</script>

<template>
    <AuthCard titulo="Bem-vindo de volta" subtitulo="Entre para ver suas tarefas">
        <v-alert v-if="erro" type="error" variant="tonal" density="compact" class="mb-4">
            {{ erro }}
        </v-alert>
        <v-form @submit.prevent="login">
            <v-text-field type="email" v-model="item.email" label="E-mail"
                prepend-inner-icon="mdi-email-outline" autocomplete="email" required />
            <v-text-field :type="mostrarSenha ? 'text' : 'password'" v-model="item.password"
                label="Senha" prepend-inner-icon="mdi-lock-outline" autocomplete="current-password"
                :append-inner-icon="mostrarSenha ? 'mdi-eye-off' : 'mdi-eye'"
                @click:append-inner="mostrarSenha = !mostrarSenha" required />
            <v-btn type="submit" color="primary" size="large" block rounded="lg"
                :loading="carregando">Entrar</v-btn>
        </v-form>
        <template #rodape>
            Ainda não tem conta?
            <RouterLink to="/cadastro" class="link">Criar conta</RouterLink>
        </template>
    </AuthCard>
</template>

<style scoped>
    .link { color: rgb(var(--v-theme-secondary)); font-weight: 600; text-decoration: none; }
</style>

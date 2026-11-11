<script setup>
    import { ref } from 'vue';
    import { useRouter } from 'vue-router';
    import { useAuthStore } from '@/stores/auth';
    import { useNotificacaoStore } from '@/stores/notificacao';
    import AuthCard from './AuthCard.vue';

    const authStore = useAuthStore();
    const notificacao = useNotificacaoStore();
    const router = useRouter();

    const item = ref({
        name: '',
        email: '',
        password: '',
        password_confirmation: ''
    });
    const carregando = ref(false);
    const erros = ref({});
    const erroGeral = ref('');

    const regras = {
        obrigatorio: v => !!v || 'Campo obrigatório',
        email: v => /.+@.+\..+/.test(v) || 'Informe um e-mail válido',
        minimo: v => (v?.length ?? 0) >= 6 || 'Mínimo de 6 caracteres',
        confere: v => v === item.value.password || 'As senhas não conferem'
    };

    async function cadastrar(event){
        const { valid } = await event;
        if(!valid) return;

        erros.value = {};
        erroGeral.value = '';
        carregando.value = true;
        try{
            await authStore.register(item.value);
            notificacao.sucesso(`Conta criada! Bem-vindo, ${authStore.usuario.name}`);
            router.push('/tarefa');
        }catch(error){
            if(error.response?.status === 422){
                erros.value = error.response.data.errors;
            }else{
                erroGeral.value = 'Não foi possível criar a conta. Tente novamente.';
            }
        }finally{
            carregando.value = false;
        }
    }
</script>

<template>
    <AuthCard titulo="Criar conta" subtitulo="Organize sua rotina em poucos cliques">
        <v-alert v-if="erroGeral" type="error" variant="tonal" density="compact" class="mb-4">
            {{ erroGeral }}
        </v-alert>
        <v-form @submit.prevent="cadastrar">
            <v-text-field v-model="item.name" label="Nome" prepend-inner-icon="mdi-account-outline"
                :rules="[regras.obrigatorio]" :error-messages="erros.name" autocomplete="name" />
            <v-text-field v-model="item.email" type="email" label="E-mail"
                prepend-inner-icon="mdi-email-outline" autocomplete="email"
                :rules="[regras.obrigatorio, regras.email]" :error-messages="erros.email" />
            <v-text-field v-model="item.password" type="password" label="Senha"
                prepend-inner-icon="mdi-lock-outline" autocomplete="new-password"
                :rules="[regras.obrigatorio, regras.minimo]" :error-messages="erros.password" />
            <v-text-field v-model="item.password_confirmation" type="password" label="Confirmar senha"
                prepend-inner-icon="mdi-lock-check-outline" autocomplete="new-password"
                :rules="[regras.obrigatorio, regras.confere]" />
            <v-btn type="submit" color="primary" size="large" block rounded="lg"
                :loading="carregando">Criar conta</v-btn>
        </v-form>
        <template #rodape>
            Já tem conta?
            <RouterLink to="/" class="link">Entrar</RouterLink>
        </template>
    </AuthCard>
</template>

<style scoped>
    .link { color: rgb(var(--v-theme-secondary)); font-weight: 600; text-decoration: none; }
</style>

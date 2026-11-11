<script setup>
    import { ref } from 'vue';
    import { useAuthStore } from '@/stores/auth';
import router from '@/router';

    const authStore = useAuthStore();

    const item = ref({
        email:"",
        password:""
    });

    async function login(){
        try{
            await authStore.login(item.value.email, item.value.password);
            router.push('/tarefa');
        }catch(error){
            console.log(error);
            alert("Erro ao realizar o login");
        }
    }
</script>

<template>
    <v-row>
        <v-col cols="12" lg="6" md="8" offset-lg="3" offset-md="2">
            <v-card elevation="6" class="mx-auto" max-width="344">
                <v-card-title>
                    Login
                </v-card-title>
                <v-card-text>
                    <v-form @submit.prevent="login">
                        <v-text-field type="email" id="email" 
                        v-model="item.email" label="Email" 
                        variant="outlined" density="compact"></v-text-field>
                        <v-text-field type="password" id="senha"
                        v-model="item.password" label="Senha"
                        variant="outlined"></v-text-field>
                        <input type="submit" value="Entrar">
                    </v-form>
                </v-card-text>
            </v-card>
        </v-col>
    </v-row>
    <div>
        
    </div>
</template>
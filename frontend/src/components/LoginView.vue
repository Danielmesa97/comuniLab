//Inicio de Sesion
<script setup>
import {ref} from Vue
const isLogin=ref(true)</script>
<template>
    <div class="main-container">
        <div class="form-card">
            <div class="logo container">
                <div class="logo-circle">
                    <span class="logo-placeholder">LOGO</span>
                </div>
            </div>
        </div>
        //Iniciar Sesion
        <div class="tab-selector">
            <button @click="isLogin=true"
            :class="['tab-button',isLogin ? 'active':'inactive']">
        </button>
        </div>
    </div>
</template>

<style scoped>
    .main-container{
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #333; /* color gris parecido al wireframe*/
    }
    .form-card{
        width: 300 px;
        min-height: 500 px;
        border-radius: 30 px;
        padding: 40 px 30 px;
        display: flex;
        flex-direction: column;
    }
    .tab-selector{
        display: flex;
        border-radius: 10 px;
        padding: 5 px;
        margin-bottom: 20 px;
        background-color: #f2f2f2;
    }
    .tab-button{
        flex: 1;
        border: none;
        font-size: 12 px;
        padding: 10 px;
    }
</style>
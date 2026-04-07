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
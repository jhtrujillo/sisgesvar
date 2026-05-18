<!-- Elemento que indica que la pagina está cargando -->
<template>
    <div class="loading-content fixed m-auto flex justify-center items-center left-0 right-0 top-96 text-center font-bold text-lime-800 text-xl">
    <div class="loading">
      <div></div>
      <div></div>
      <div></div>
      <div></div>
    </div>
    <div>{{ text }}</div>
  </div>
</template>
<script setup lang="ts">
import {defineProps} from "vue";


const props = defineProps ({
        text: {
          require:false,
          type: String,
          default: 'Cargando...'
        }
 
});

// const state = mapState() {
//   ret

// }
</script>
<style>
.loading-content{
  width: 25%;
  z-index: 999;
}
.loading {
    display: inline-block;
    position: relative;
    width: 80px;
    height: 80px;
  }
  .loading div {
    box-sizing: border-box;
    display: block;
    position: absolute;
    width: 64px;
    height: 64px;
    margin: 8px;
    border: 8px solid rgb(63 98 18);
    border-radius: 50%;
    animation: loading 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
    border-color: rgb(63 98 18) transparent transparent transparent;
  }
  .loading div:nth-child(1) {
    animation-delay: -0.45s;
  }
  .loading div:nth-child(2) {
    animation-delay: -0.3s;
  }
  .loading div:nth-child(3) {
    animation-delay: -0.15s;
  }
  @keyframes loading {
    0% {
      transform: rotate(0deg);
    }
    100% {
      transform: rotate(360deg);
    }
  }
</style>
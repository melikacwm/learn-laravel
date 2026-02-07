<template>
  <!-- Latihan Melika -->
    <div style="padding:16px">
        <h1>
        Hello Page
        </h1>
        <p>{{message}}</p>
    </div>
</template>

<script setup>
// latihan Melika
defineProps({
    message: String,
})
</script>
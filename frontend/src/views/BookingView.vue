<template>
  <div>
    <h1>Đặt chỗ: {{ code }}</h1>
    <p>Form đặt chỗ sẽ hiển thị ở đây.</p>
  </div>
</template>

<script setup>
defineProps(['code'])
</script>
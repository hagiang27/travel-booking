<template>
  <div>
    <h1>Chi tiết sản phẩm: {{ slug }}</h1>
    <p>Thông tin chi tiết, lịch trình, đánh giá sẽ hiển thị ở đây.</p>
  </div>
</template>

<script setup>
defineProps(['slug'])
</script>
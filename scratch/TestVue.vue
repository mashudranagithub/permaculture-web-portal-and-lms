<template>
  <div>
    Test URL: {{ route('payments.initiate', { enrollment_id: 2 }) }}
  </div>
</template>
<script setup>
import { route } from 'ziggy-js'; // Or whatever global route
</script>

<template>
  <div class="home">
    
    <Scan />
  </div>
</template>

<script>
// @ is an alias to /src
import Scan from '@/components/Scan.vue'

export default {
  name: 'Home',
  components: {
    Scan
  }
}
</script>

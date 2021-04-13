<template>
  <div class="singleFood">
    
    <InventoryItem :inventoryId="inventoryId"/>
  </div>
</template>

<script>
// @ is an alias to /src
import InventoryItem from '@/components/InventoryItem.vue'

export default {
  name: 'SingleFood',
  props:['inventoryId'],
  components: {
    InventoryItem
  }
}
</script>

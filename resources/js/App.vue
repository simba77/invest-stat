<template>
  <router-view></router-view>
  <base-modal></base-modal>
</template>

<script lang="ts">
import {defineComponent} from 'vue';
import BaseModal from "@/components/Modals/BaseModal.vue";

export default defineComponent({
  name: "App",
  components: {BaseModal}
});
</script>

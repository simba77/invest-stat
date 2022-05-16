<template>
  <page-component title="Add Category">
    <form class="mt-8 space-y-6 w-1/3" action="#" method="POST" @submit.prevent="submitForm">
      <div class="bg-red-500 text-white rounded px-4 py-2" v-if="error">{{ error }}</div>
      <div class="rounded-md shadow-sm">
        <div class="">
          <label for="name" class="sr-only">Name</label>
          <input
            id="name"
            name="name"
            type="text"
            class="form-input"
            placeholder="Name"
            required
            v-model="form.name"
          >
        </div>
      </div>
      <button type="submit" class="btn-primary" :disabled="loading">Save</button>
    </form>
  </page-component>
</template>

<script lang="ts">
import PageComponent from "@/components/PageComponent.vue";

export default {
  name: "CategoryForm",
  components: {PageComponent},
  data() {
    return {
      form: {
        name: '',
      },
      loading: false,
      error: null,
    }
  },
  methods: {
    submitForm() {
      alert('Submit');
    }
  }
}
</script>

<style scoped>

</style>

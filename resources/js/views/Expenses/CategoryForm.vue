<template>
  <page-component title="Add Category">
    <div class="card">
      <form class="space-y-6 w-2/3 mx-auto" action="#" method="POST" @submit.prevent="submitForm">
        <div>
          <h3 class="text-lg font-medium text-gray-900">Category</h3>
          <p class="mt-1 text-sm text-gray-600">Enter the name of the category to group expenses</p>
        </div>
        <div class="bg-red-500 inline-block text-white rounded px-4 py-2" v-if="error">{{ error }}</div>
        <div class="w-2/4">
          <input-text
            v-model="form.name"
            name="name"
            label="Category Name"
            placeholder="Enter a category name"
          />
        </div>
        <div class="border-b"></div>
        <button type="submit" class="btn-primary" :disabled="loading">Save</button>
        <router-link :to="{name: 'Expenses'}" class="btn btn-cancel ml-3">Back</router-link>
      </form>
    </div>
  </page-component>
</template>

<script lang="ts">
import PageComponent from "@/components/PageComponent.vue";
import InputText from "@/components/Forms/InputText.vue";

export default {
  name: "CategoryForm",
  components: {InputText, PageComponent},
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

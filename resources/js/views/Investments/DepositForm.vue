<template>
  <page-component title="Add Deposit">
    <div class="card">
      <form class="space-y-6 w-2/3 mx-auto" action="#" method="POST" @submit.prevent="submitForm">
        <div>
          <h3 class="text-lg font-medium text-gray-900">Deposit</h3>
          <p class="mt-1 text-sm text-gray-600">Enter the date and amount of expense</p>
        </div>
        <div class="bg-red-500 inline-block text-white rounded px-4 py-2" v-if="errors && errors.message">{{ errors.message }}</div>
        <div class="w-2/4">
          <input-text
            v-model="form.date"
            :key="componentKey"
            :error="errors?.date"
            type="date"
            name="date"
            label="Date"
            placeholder="Date"
          />
          <input-text
            type="number"
            class="mt-3"
            v-model="form.sum"
            :key="componentKey"
            :error="errors?.sum"
            name="sum"
            label="Amount of Deposit"
            placeholder="Amount of Deposit"
          />
        </div>
        <div class="border-b"></div>
        <button type="submit" class="btn btn-primary" :disabled="loading">Save</button>
        <router-link :to="{name: 'Investments'}" class="btn btn-secondary ml-3">Back</router-link>
      </form>
    </div>
  </page-component>
</template>

<script lang="ts">
import PageComponent from "@/components/PageComponent.vue";
import InputText from "@/components/Forms/InputText.vue";
import axios from "axios";

export default {
  name: "ExpenseForm",
  components: {InputText, PageComponent},
  data() {
    return {
      form: {
        date: '',
        sum: '',
      },
      loading: false,
      errors: null,
      componentKey: 0,
    }
  },
  mounted() {
    if (this.$route.params.id) {
      this.getForm(this.$route.params.id);
    }
  },
  methods: {
    submitForm() {
      this.loading = true;
      axios.post('/api/investments/deposits/store', this.form)
        .then(() => {
          this.$router.push({name: 'Investments'});
        })
        .catch((error) => {
          if (error.response.data.errors) {
            this.errors = error.response.data.errors;
            this.componentKey += 1;
          } else {
            alert('An error has occurred');
          }
        })
        .finally(() => {
          this.loading = false;
        })
    }
  }
}
</script>

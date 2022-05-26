<template>
  <page-component title="Add Account">
    <div class="card">
      <form class="space-y-6 w-2/3 mx-auto" action="#" method="POST" @submit.prevent="submitForm">
        <div>
          <h3 class="text-lg font-medium text-gray-900">Account</h3>
          <p class="mt-1 text-sm text-gray-600">Enter the name of the account to group your assets</p>
        </div>
        <div class="bg-red-500 inline-block text-white rounded px-4 py-2" v-if="errors && errors.message">{{ errors.message }}</div>
        <div class="w-2/4">
          <input-text
            v-model="form.name"
            :key="componentKey"
            :error="errors?.name"
            class="mb-3"
            name="name"
            label="Account Name"
            placeholder="Enter an account name"
          />
          <input-text
            v-model="form.balance"
            :key="componentKey"
            :error="errors?.balance"
            class="mb-3"
            type="number"
            name="balance"
            label="Balance"
            placeholder="Enter balance"
          />
          <input-select
            v-model="form.currency"
            :key="componentKey"
            :error="errors?.balance"
            :options="[{value: 'RUB', name: 'RUB'}, {value: 'USD', name: 'USD'}]"
            id="currency"
            name="currency"
            label="Currency"
          />
        </div>
        <div class="border-b"></div>
        <button type="submit" class="btn btn-primary" :disabled="loading">Save</button>
        <router-link :to="{name: 'Accounts'}" class="btn btn-cancel ml-3">Back</router-link>
      </form>
    </div>
  </page-component>
</template>

<script lang="ts">
import PageComponent from "@/components/PageComponent.vue";
import InputText from "@/components/Forms/InputText.vue";
import axios from "axios";
import InputSelect from "@/components/Forms/InputSelect.vue";

export default {
  name: "CategoryForm",
  components: {InputSelect, InputText, PageComponent},
  data() {
    return {
      form: {
        name: '',
        balance: '',
        currency: 'RUB',
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
      axios.post('/api/accounts/store', this.form)
        .then(() => {
          this.$router.push({name: 'Accounts'});
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
    },
    getForm(id: number) {
      this.loading = true;
      axios.get('/api/accounts/edit/' + id)
        .then((response) => {
          this.form = response.data.form;
          this.componentKey += 1;
        })
        .catch(() => {
          alert('An error has occurred');
        })
        .finally(() => {
          this.loading = false;
        })
    }
  }
}
</script>
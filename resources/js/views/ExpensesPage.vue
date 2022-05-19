<template>
  <page-component title="Expenses">
    <div class="mb-4">
      <router-link :to="{name: 'CreateCategory'}" class="btn btn-primary">Create Category</router-link>
    </div>
    <table class="simple-table white-header">
      <thead>
      <tr>
        <th>Name</th>
        <th>Sum</th>
      </tr>
      </thead>
      <tbody>
      <template v-for="(cat, index) in expenses.data" :key="index">
        <template v-if="! cat.isTotal">
          <tr class="table-subtitle">
            <td colspan="2">{{ cat.name }}</td>
          </tr>
          <template v-if="cat.expenses.length > 0">
            <tr v-for="(expense, i) in cat.expenses" :class="[expense.isTotal ? 'font-bold' : '']" :key="i">
              <td :class="[expense.isSubTotal || expense.isTotal ? 'text-right' : '']">{{ expense.name }}</td>
              <td>{{ new Intl.NumberFormat('ru-RU').format(expense.sum) }} {{ expense.currency }}</td>
            </tr>
          </template>
        </template>
        <tr class="font-bold" v-else>
          <td class="text-right">{{ cat.name }}</td>
          <td>{{ new Intl.NumberFormat('ru-RU').format(cat.sum) }} {{ cat.currency }}</td>
        </tr>
      </template>
      </tbody>
    </table>
  </page-component>
</template>

<script lang="ts">
import PageComponent from "../components/PageComponent.vue";
import axios from "axios";

export default {
  name: "ExpensesPage",
  components: {PageComponent},
  mounted() {
    this.getItems();
  },
  data() {
    return {
      loading: true,
      expenses: {},
    }
  },
  methods: {
    getItems() {
      this.loading = true;
      axios.get('/api/expenses/list')
        .then((response) => {
          this.expenses = response.data;
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

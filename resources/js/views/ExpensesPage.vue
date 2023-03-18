<template>
  <page-component title="Expenses">
    <preloader-component class="mb-3" v-if="loadingSummary"/>
    <template v-else>
      <div class="text-xl mb-3">Summary</div>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2 md:gap-4 mb-5">
        <stat-card
          v-for="(card, i) in summary"
          :key="i"
          :name="card.name"
          :help-text="card.helpText ?? null"
          :percent="card.percent ?? null"
          :total="card.total"
        ></stat-card>
      </div>
    </template>

    <div class="mb-4">
      <router-link :to="{name: 'CreateCategory'}" class="btn btn-primary">Create Category</router-link>
    </div>

    <preloader-component v-if="loadingExpenses"/>
    <table v-else class="simple-table white-header">
      <thead>
      <tr>
        <th>Name</th>
        <th>Sum</th>
        <th class="flex justify-end">Actions</th>
      </tr>
      </thead>
      <tbody>
      <template v-for="(cat, index) in expenses.data" :key="index">
        <template v-if="! cat.isTotal">
          <tr class="table-subtitle">
            <td colspan="2">{{ cat.name }}</td>
            <td class="flex justify-end items-center">
              <template v-if="cat.id">
                <router-link :to="{name: 'AddExpense', params: {category: cat.id}}" class="text-gray-300 hover:text-gray-600 mr-2">
                  <plus-circle-icon class="h-5 w-5"></plus-circle-icon>
                </router-link>
                <router-link :to="{name: 'EditCategory', params: {id: cat.id}}" class="text-gray-300 hover:text-gray-600 mr-2">
                  <pencil-icon class="h-5 w-5"></pencil-icon>
                </router-link>
                <button
                  type="button"
                  class="text-gray-300 hover:text-red-500"
                  @click="confirmCategoryDeletion(cat, () => getExpenses())"
                >
                  <x-circle-icon class="h-5 w-5"></x-circle-icon>
                </button>
              </template>
            </td>
          </tr>
          <template v-if="cat.expenses.length > 0">
            <tr v-for="(expense, i) in cat.expenses" :class="[expense.isTotal ? 'font-bold' : '']" :key="i">
              <td :class="[expense.isSubTotal || expense.isTotal ? 'text-right' : '']">{{ expense.name }}</td>
              <td>{{ new Intl.NumberFormat('ru-RU').format(expense.sum) }} {{ expense.currency }}</td>
              <td class="table-actions">
                <template v-if="expense.id">
                  <div class="flex justify-end items-center show-on-row-hover">
                    <router-link :to="{name: 'EditExpense', params: {id: expense.id, category: cat.id}}" class="text-gray-300 hover:text-gray-600 mr-2">
                      <pencil-icon class="h-5 w-5"></pencil-icon>
                    </router-link>
                    <button
                      type="button"
                      class="text-gray-300 hover:text-red-500"
                      @click="confirmDeletion(expense, () => getExpenses())"
                    >
                      <x-circle-icon class="h-5 w-5"></x-circle-icon>
                    </button>
                  </div>
                </template>
              </td>
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

<script setup lang="ts">
import PageComponent from "../components/PageComponent.vue";
import {PencilIcon, XCircleIcon, PlusCircleIcon} from "@heroicons/vue/outline";
import StatCard from "@/components/Cards/StatCard.vue";
import {useExpenses} from "@/composable/useExpenses";
import PreloaderComponent from "@/components/Common/PreloaderComponent.vue";
import {useExpensesCategory} from "@/composable/useExpensesCategory";

const {confirmDeletion: confirmCategoryDeletion} = useExpensesCategory()
const {
  loadingExpenses,
  loadingSummary,
  getSummary,
  getExpenses,
  expenses,
  summary,
  confirmDeletion
} = useExpenses();

getExpenses()
getSummary()
</script>

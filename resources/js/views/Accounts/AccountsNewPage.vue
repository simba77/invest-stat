<script setup lang="ts">
import PageComponent from "@/components/PageComponent.vue";
import PreloaderComponent from "@/components/Common/PreloaderComponent.vue";
import {provide} from "vue";
import AccountComponent from "@/components/Account/AccountComponent.vue";
import AssetsTableComponent from "@/components/Account/AssetsTableComponent.vue";
import useAccounts from "@/composable/useAccounts";

const {
  getAccounts,
  accounts,
  loading
} = useAccounts();

provide('accounts', {getAccounts})

getAccounts();
</script>
<template>
  <page-component title="Accounts" ref="accounts">
    <div class="mb-4">
      <router-link :to="{name: 'CreateAccount'}" class="btn btn-primary">Create Account</router-link>
    </div>
    <preloader-component v-if="loading"></preloader-component>
    <template v-if="!loading && accounts">
      <div class="mb-6">
        <div v-for="(account, index) in accounts" :key="index">
          <account-component :account="account"></account-component>

          <template v-for="(blocTypeGroup, blockTypeGroupIndex) in account.blockGroups" :key="blockTypeGroupIndex">
            <!-- Если групп блокировки больше одной, выводим название -->
            <template v-if="Object.keys(account.blockGroups).length > 1">
              <div class="font-extrabold text-base mb-4">{{ blocTypeGroup.name }}</div>
            </template>

            <!-- Выводим группы активов по валютам -->
            <template v-for="(currencyGroup, currencyGroupIndex) in blocTypeGroup.items" :key="currencyGroupIndex">
              <div class="flex items-center">
                <div class="font-bold text-sm mb-4">{{ currencyGroup.name }}</div>
                <div class="flex-grow mb-4 ml-3 border-b"></div>
              </div>

              <div class="w-full overflow-x-auto mb-4">
                <template v-if="Object.keys(currencyGroup.items).length < 1">
                  <div class="text-gray-500 text-sm">The List is Empty</div>
                </template>
                <template v-else>
                  <!-- Выводим таблицу с активами -->
                  <assets-table-component v-model="currencyGroup.items"></assets-table-component>
                </template>
              </div>
            </template>
          </template>
        </div>
      </div>
    </template>
  </page-component>
</template>

<template>
  <page-component title="Accounts">
    <template v-if="stat">
      <div class="text-xl mb-3">Summary</div>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2 md:gap-4 mb-5">
        <stat-card
          v-for="(card, i) in stat.summary"
          :key="i"
          :name="card.name"
          :help-text="card.helpText ?? null"
          :percent="card.percent ?? null"
          :total="card.total"
        ></stat-card>
      </div>
    </template>

    <div class="mb-4">
      <router-link :to="{name: 'CreateAccount'}" class="btn btn-primary">Create Account</router-link>
      <button class="btn btn-secondary ml-3" :disabled="loading" @click="updateData">Update Data</button>
    </div>

    <div class="mb-6">
      <div v-for="(account, index) in assets.data" :key="index">

        <!-- Account block -->
        <div class="flex justify-between mb-2 mt-5 py-3 rounded">
          <div class="">
            <div class="font-extrabold text-lg">{{ account.name }}</div>
            <div class="text-sm"><span class="font-light">Balance:</span> <span>{{ helpers.formatPrice(account.balance) }} {{ account.currency }}</span></div>
          </div>
          <div class="flex items-center">
            <router-link
              :to="{name: 'AddAsset', params: {account: account.id}}"
              class="text-gray-300 hover:text-gray-600 mr-2"
              title="Add Asset">
              <plus-circle-icon class="h-5 w-5"></plus-circle-icon>
            </router-link>
            <router-link
              :to="{name: 'EditAccount', params: {id: account.id}}"
              class="text-gray-300 hover:text-gray-600 mr-2"
              title="Edit Account"
            >
              <pencil-icon class="h-5 w-5"></pencil-icon>
            </router-link>
            <button
              type="button"
              class="text-gray-300 hover:text-red-500"
              @click="openConfirmModal(account, 'category')"
              title="Delete Account"
            >
              <x-circle-icon class="h-5 w-5"></x-circle-icon>
            </button>
          </div>
        </div>
        <!-- // Account block -->

        <div class="w-full overflow-x-auto">
          <template v-if="account.assets.length > 0">
            <table class="simple-table sub-table white-header">
              <thead>
              <tr>
                <th>Ticker</th>
                <th>Name</th>
                <th>Quantity</th>
                <th>Buy Price</th>
                <th>Price</th>
                <th>Full Buy Price</th>
                <th>Full Price</th>
                <th>Profit</th>
                <th>Percent</th>
                <th class="flex justify-end" style="min-width: 115px;">Actions</th>
              </tr>
              </thead>
              <tbody>

              <template v-for="(asset, i) in account.assets" :key="i">

                <!-- Account block -->
                <tr v-if="asset.isSubTotal" class="font-bold">
                  <td
                    :class="[asset.isSubTotal || asset.isTotal ? 'text-right' : '']"
                    v-tooltip="'Last Update:' + asset.updated"
                  >{{ asset.ticker }}
                  </td>
                  <td>{{ asset.name }}</td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td>{{ helpers.formatPrice(asset.fullBuyPrice) }} {{ asset.currency }}</td>
                  <td>{{ helpers.formatPrice(asset.fullPrice) }} {{ asset.currency }}</td>
                  <td :class="[asset.profit > 0 ? 'text-green-600' : 'text-red-700']">{{ helpers.formatPrice(asset.profit) }} {{ asset.currency }} ({{ asset.profitPercent }}%)</td>
                  <td></td>
                  <td class="table-actions"></td>
                </tr>
                <!-- Asset block -->
                <template v-else>
                  <!-- Group of assets -->
                  <template v-if="asset.items.length > 0">
                    <tr class="tr-clickable" @click="asset.showItems = !asset.showItems">
                      <td
                        :class="[asset.isSubTotal || asset.isTotal ? 'text-right' : '']"
                        v-tooltip="'Last Update: ' + asset.updated"
                      >{{ asset.ticker }}
                      </td>
                      <td>{{ asset.name }}</td>
                      <td>{{ asset.quantity }}</td>
                      <td>{{ helpers.formatPrice(asset.buyPrice) }} {{ asset.currency }}</td>
                      <td>{{ helpers.formatPrice(asset.price) }} {{ asset.currency }}</td>
                      <td>{{ helpers.formatPrice(asset.fullBuyPrice) }} {{ asset.currency }}</td>
                      <td>{{ helpers.formatPrice(asset.fullPrice) }} {{ asset.currency }}</td>
                      <td :class="[asset.profit > 0 ? 'text-green-600' : 'text-red-700']">
                        {{ asset.profit > 0 ? '+' : '-' }} {{ helpers.formatPrice(Math.abs(asset.profit)) }} {{ asset.currency }} ({{ asset.profitPercent }}%)
                      </td>
                      <td>{{ asset.accountPercent }}%</td>
                      <td class="table-actions"></td>
                    </tr>
                    <tr v-if="asset.showItems">
                      <td colspan="111" class="!p-2 !bg-white">
                        <table class="simple-table sub-table white-header">
                          <thead>
                          <tr>
                            <th>Ticker</th>
                            <th>Name</th>
                            <th>Quantity</th>
                            <th>Buy Price</th>
                            <th>Price</th>
                            <th>Full Buy Price</th>
                            <th>Full Price</th>
                            <th>Profit</th>
                            <th>Percent</th>
                            <th class="flex justify-end" style="min-width: 115px;">Actions</th>
                          </tr>
                          </thead>
                          <tbody>
                          <tr v-for="(subItem, subIndex) in asset.items" :key="'sub' + subIndex">
                            <td :class="[subItem.isSubTotal || subItem.isTotal ? 'text-right' : '']">{{ subItem.ticker }}</td>
                            <td>{{ subItem.name }}</td>
                            <td>{{ subItem.quantity }}</td>
                            <td>{{ helpers.formatPrice(subItem.buyPrice) }} {{ subItem.currency }}</td>
                            <td>{{ helpers.formatPrice(subItem.price) }} {{ subItem.currency }}</td>
                            <td>{{ helpers.formatPrice(subItem.fullBuyPrice) }} {{ subItem.currency }}</td>
                            <td>{{ helpers.formatPrice(subItem.fullPrice) }} {{ subItem.currency }}</td>
                            <td :class="[subItem.profit > 0 ? 'text-green-600' : 'text-red-700']">
                              {{ subItem.profit > 0 ? '+' : '-' }} {{ helpers.formatPrice(Math.abs(subItem.profit)) }} {{ subItem.currency }} ({{ subItem.profitPercent }}%)
                            </td>
                            <td>{{ subItem.accountPercent }}%</td>
                            <td class="table-actions">
                              <div class="flex justify-end items-center show-on-row-hover">
                                <router-link
                                  :to="{name: 'EditAsset', params: {id: subItem.id, account: account.id}}"
                                  class="text-gray-300 hover:text-gray-600 mr-2"
                                  title="Edit"
                                >
                                  <pencil-icon class="h-5 w-5"></pencil-icon>
                                </router-link>
                                <div
                                  @click="openSellModal(subItem)"
                                  class="text-gray-300 hover:text-gray-600 mr-2 cursor-pointer"
                                  title="Sell"
                                >
                                  <cash-icon class="h-5 w-5"></cash-icon>
                                </div>
                                <button
                                  type="button"
                                  class="text-gray-300 hover:text-red-500"
                                  @click="openConfirmModal(subItem, 'asset')"
                                  title="Delete"
                                >
                                  <x-circle-icon class="h-5 w-5"></x-circle-icon>
                                </button>
                              </div>
                            </td>
                          </tr>
                          </tbody>
                        </table>
                      </td>
                    </tr>

                  </template>
                  <!-- Asset without group -->
                  <tr v-else>
                    <td
                      :class="[asset.isSubTotal || asset.isTotal ? 'text-right' : '']"
                      v-tooltip="'Last Update: ' + asset.updated"
                    >{{ asset.ticker }}
                    </td>
                    <td>{{ asset.name }}</td>
                    <td>{{ asset.quantity }}</td>
                    <td>{{ helpers.formatPrice(asset.buyPrice) }} {{ asset.currency }}</td>
                    <td>{{ helpers.formatPrice(asset.price) }} {{ asset.currency }}</td>
                    <td>{{ helpers.formatPrice(asset.fullBuyPrice) }} {{ asset.currency }}</td>
                    <td>{{ helpers.formatPrice(asset.fullPrice) }} {{ asset.currency }}</td>
                    <td :class="[asset.profit > 0 ? 'text-green-600' : 'text-red-700']">
                      {{ asset.profit > 0 ? '+' : '-' }} {{ helpers.formatPrice(Math.abs(asset.profit)) }} {{ asset.currency }} ({{ asset.profitPercent }}%)
                    </td>
                    <td>{{ asset.accountPercent }}%</td>
                    <td class="table-actions">
                      <template v-if="asset.id">
                        <div class="flex justify-end items-center show-on-row-hover">
                          <router-link
                            :to="{name: 'EditAsset', params: {id: asset.id, account: account.id}}"
                            class="text-gray-300 hover:text-gray-600 mr-2"
                            title="Edit"
                          >
                            <pencil-icon class="h-5 w-5"></pencil-icon>
                          </router-link>
                          <div
                            @click="openSellModal(asset)"
                            class="text-gray-300 hover:text-gray-600 mr-2 cursor-pointer"
                            title="Sell"
                          >
                            <cash-icon class="h-5 w-5"></cash-icon>
                          </div>
                          <button
                            type="button"
                            class="text-gray-300 hover:text-red-500"
                            @click="openConfirmModal(asset, 'asset')"
                            title="Delete"
                          >
                            <x-circle-icon class="h-5 w-5"></x-circle-icon>
                          </button>
                        </div>
                      </template>
                    </td>
                  </tr>
                </template>
              </template>
              </tbody>
            </table>
          </template>
        </div>
      </div>
    </div>
  </page-component>

  <base-modal ref="deleteConfirmationModal">
    <confirm-modal
      :close="closeModal"
      :confirm="confirmDeletion"
      title="Deletion confirmation"
      :text="'Are you sure you want to delete &quot;<b>'+ deleteItem.data.name +'</b>&quot;?'"
    ></confirm-modal>
  </base-modal>
  <base-modal ref="sellModal">
    <sell-modal :close="closeSellModal" :confirm="getItems" :sellAsset="sell"></sell-modal>
  </base-modal>
</template>

<script lang="ts">
import PageComponent from "../../components/PageComponent.vue";
import axios from "axios";
import {PencilIcon, XCircleIcon, PlusCircleIcon, CashIcon} from "@heroicons/vue/outline";
import BaseModal from "@/components/Modals/BaseModal.vue";
import ConfirmModal from "@/components/Modals/ConfirmModal.vue";
import StatCard from "@/components/Cards/StatCard.vue";
import helpers from "@/helpers";
import SellModal from "@/components/Modals/SellModal.vue";

export default {
  name: "AccountsPage",
  components: {SellModal, StatCard, ConfirmModal, BaseModal, PageComponent, PencilIcon, XCircleIcon, PlusCircleIcon, CashIcon},
  mounted() {
    this.getItems();
    this.getStat();
  },
  data() {
    return {
      loading: true,
      deleting: false,
      stat: {},
      deleteItem: {
        type: '',
        data: {},
      },
      sell: {},
      assetsGroup: {},
      assets: {},
      helpers,
    }
  },
  methods: {
    openConfirmModal(item: any, type = 'expense') {
      this.deleteItem.type = type;
      this.deleteItem.data = item;
      setTimeout(() => {
        this.$refs.deleteConfirmationModal.openModal();
      });
    },
    openSellModal(item: any) {
      this.sell = item;
      setTimeout(() => {
        this.$refs.sellModal.openModal();
      });
    },
    closeModal() {
      this.$refs.deleteConfirmationModal.closeModal();
    },
    closeSellModal() {
      this.$refs.sellModal.closeModal();
    },
    confirmDeletion() {
      if (this.deleteItem.type === 'category') {
        this.deleteCategory(this.deleteItem.data.id)
          .finally(() => {
            this.closeModal();
          });
      } else {
        this.deleteAsset(this.deleteItem.data.id)
          .finally(() => {
            this.closeModal();
          });
      }
    },
    getItems() {
      this.loading = true;
      axios.get('/api/accounts/list')
        .then((response) => {
          this.assets = response.data;
        })
        .catch(() => {
          alert('An error has occurred');
        })
        .finally(() => {
          this.loading = false;
        })
    },
    getStat() {
      this.loading = true;
      axios.get('/api/accounts/summary')
        .then((response) => {
          this.stat = response.data;
        })
        .catch(() => {
          alert('An error has occurred');
        })
        .finally(() => {
          this.loading = false;
        })
    },
    deleteCategory(id: number) {
      this.deleting = true;
      return new Promise((resolve, reject) => {
        axios.post('/api/accounts/delete/' + id)
          .then(() => {
            this.getItems();
            resolve({deleted: true});
          })
          .catch(() => {
            alert('An error has occurred');
            reject('An error has occurred');
          })
          .finally(() => {
            this.deleting = false;
          })
      });
    },

    deleteAsset(id: number) {
      this.deleting = true;
      return new Promise((resolve, reject) => {
        axios.post('/api/assets/delete/' + id)
          .then(() => {
            this.getItems();
            resolve({deleted: true});
          })
          .catch(() => {
            alert('An error has occurred');
            reject('An error has occurred');
          })
          .finally(() => {
            this.deleting = false;
          })
      });
    },

    updateData() {
      this.loading = true;
      axios.get('/api/accounts/update-data')
        .then(() => {
          this.getItems();
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

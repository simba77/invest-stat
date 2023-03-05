import {ref} from "vue";
import {Account} from "@/models/account";
import axios from "axios";
import useAsync from "@/utils/use-async";

const accounts = ref<Account[]>([])

async function getAccounts() {
  accounts.value = await axios.get('/api/accounts/new-list').then((response) => response.data);
}

const {loading, run: asyncGetAccount} = useAsync(getAccounts)

export default function () {
  return {
    accounts,
    loading,
    getAccounts: asyncGetAccount,
  }
}

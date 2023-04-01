import {ref} from "vue";
import axios from "axios";
import {config} from "@/config";
import useAsync from "@/utils/use-async";


const currentUser = ref({})

async function getCurrentUser() {
  console.log('Get current user');
  currentUser.value = await axios.get(config.API_HOST + '/api/checkAuth').then((response) => response.data);
}

const {loading, run: asyncGetCurrentUser} = useAsync(getCurrentUser)

export default function () {
  return {
    loading,
    currentUser,
    getCurrentUser: asyncGetCurrentUser
  }
}

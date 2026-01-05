<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, defineProps, computed } from 'vue';
import NavLink from "@/Components/NavLink.vue";
import {
  HomeIcon,
  ArrowsUpDownIcon,
  UsersIcon,
  EyeIcon,
  UserPlusIcon,
  ShieldCheckIcon,
  ShieldExclamationIcon,
  CheckBadgeIcon,
  XCircleIcon,
  ClockIcon,
  CogIcon,
} from "@heroicons/vue/24/solid";

//  Instantiating the props
const props = defineProps({
  allUsers: {
    type: Array,
    default: () => []
  },
  users: {
    type: Array,
    default: () => []
  },
  pagination: {
    type: Object,
    default: () => ({ current_page: 1, last_page: 1, total: 0, per_page: 10, from: 0, to: 0 })
  },
  filters: {
    type: Object,
    default: () => ({ search: '', sort: 'created_at', direction: 'desc' })
  }
});

// Instantiating the reactive variables:

// Check if we have paginated data or initial load
const isInitialLoad = computed(() => !props.users || props.users.length === 0);

// Use either paginated users or all users for initial load
const currentUsers = computed(() => {
  return isInitialLoad.value ? props.allUsers.slice(0, 10) : props.users;
});

// Instantiating the pagination variables
const currentPage = ref(isInitialLoad.value ? 1 : (props.pagination?.current_page || 1));
const usersPerPage = 10;

// For initial load, calculate pagination from allUsers
const totalPages = computed(() => {
  if (isInitialLoad.value) {
    return Math.ceil(props.allUsers.length / usersPerPage);
  }
  return props.pagination?.last_page || 1;
});

const totalCount = computed(() => {
  return isInitialLoad.value ? props.allUsers.length : (props.pagination?.total || 0);
});

const fromCount = computed(() => {
  if (isInitialLoad.value) {
    return ((currentPage.value - 1) * usersPerPage) + 1;
  }
  return props.pagination?.from || 0;
});

const toCount = computed(() => {
  if (isInitialLoad.value) {
    return Math.min(currentPage.value * usersPerPage, props.allUsers.length);
  }
  return props.pagination?.to || 0;
});

// Instantiating the sorting variables
const sortColumn = ref(props.filters?.sort || 'created_at');
const sortDirection = ref(props.filters?.direction || 'desc');

// Instantiating the search variables
const searchQuery = ref(props.filters?.search || '');
const isLoading = ref(false);

// Function to make server requests without changing URL
const fetchUsers = (params = {}) => {
  isLoading.value = true;

  const requestData = {
    page: currentPage.value,
    search: searchQuery.value,
    sort: sortColumn.value,
    direction: sortDirection.value,
    ...params
  };

  router.get(route('users.index'), requestData, {
    preserveState: true,
    preserveScroll: true,
    replace: true,
    only: ['users', 'pagination', 'filters'],
    onFinish: () => {
      isLoading.value = false;
    }
  });
};

// Function to navigate to a different page
const goToPage = (page) => {
  if (page >= 1 && page <= totalPages.value && page !== currentPage.value) {
    currentPage.value = page;
    if (!isInitialLoad.value || page > 1) {
      fetchUsers({ page });
    }
  }
};

// Function to perform search with debouncing
let searchTimeout = null;
const performSearch = () => {
  if (searchTimeout) {
    clearTimeout(searchTimeout);
  }

  searchTimeout = setTimeout(() => {
    currentPage.value = 1; // Reset to first page on search
    fetchUsers({ page: 1, search: searchQuery.value });
  }, 500); // 500ms debounce
};

const sortBy = (column) => {
  if (sortColumn.value === column) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortColumn.value = column;
    sortDirection.value = 'asc';
  }

  currentPage.value = 1; // Reset to first page on sort
  fetchUsers({ page: 1, sort: sortColumn.value, direction: sortDirection.value });
};

// Format date function
const formatDate = (dateString) => {
  const date = new Date(dateString);
  return `${date.getDate()}-${date.getMonth() + 1
    }-${date.getFullYear()} ${date.getHours()}:${date.getMinutes()}:${date.getSeconds()}`;
};

// Get role badge class
const getRoleBadgeClass = (role) => {
  const roleClasses = {
    'admin': 'badge-error',
    'customer': 'badge-success',
    'No Role': 'badge-neutral'
  };
  return roleClasses[role] || 'badge-neutral';
};

// Get role icon
const getRoleIcon = (role) => {
  const icons = {
    'admin': ShieldCheckIcon,
    'customer': UserPlusIcon,
    'No Role': ShieldExclamationIcon
  };
  return icons[role] || ShieldExclamationIcon;
};

// Check if user is verified
const isUserVerified = (emailVerifiedAt) => {
  return emailVerifiedAt !== null;
};

// View user function (placeholder)
const viewUser = (id) => {
  console.log('View user:', id);
};

// Toggle user status function (placeholder)
const toggleUserStatus = (id, currentStatus) => {
  console.log('Toggle user status:', id, currentStatus);
};

// Manage user roles function (placeholder)
const manageUserRoles = (id) => {
  console.log('Manage user roles:', id);
};
</script>

<template>
  <Head title="Users" />

  <AuthenticatedLayout>
    <div class="breadcrumbs text-sm mb-2 text-black">
      <ul class="flex items-center space-x-2">
        <li>
          <NavLink :href="route('dashboard')" class="flex items-center">
            <HomeIcon class="w-4 h-4" />
            <span class="ml-4">Dashboard</span>
          </NavLink>
        </li>
        <li class="text-gray-400 mx-2 select-none">&gt;</li>
        <li>
          <a class="flex items-center">
            <UsersIcon class="w-4 h-4" />
            <span class="ml-4">Users</span>
          </a>
        </li>
      </ul>
    </div>

    <div class="w-full text-black">
      <!-- Header Pagination -->
      <nav class="flex items-center justify-between mb-4">
        <div>
          <span class="text-sm">
            Showing
            <span class="font-medium">{{ fromCount }}</span>
            to
            <span class="font-medium">{{ toCount }}</span>
            of
            <span class="font-medium">{{ totalCount }}</span>
            users
            <span v-if="isLoading" class="ml-2 text-gray-500">(Loading...)</span>
          </span>
        </div>
        <div class="join text-black">
          <button class="join-item btn btn-sm btn-neutral btn-outline" :disabled="currentPage === 1 || isLoading"
            @click="goToPage(currentPage - 1)">
            «
          </button>
          <button class="join-item btn btn-sm btn-neutral btn-outline">Page {{ currentPage }}</button>
          <button class="join-item btn btn-sm btn-neutral btn-outline"
            :disabled="currentPage === totalPages || isLoading" @click="goToPage(currentPage + 1)">
            »
          </button>
        </div>
        <div class="flex items-center text-black">
          <div class="form-control">
            <input type="text" placeholder="Search users..." class="input border-b w-24 md:w-auto input-sm text-black"
              v-model="searchQuery" @input="performSearch" />
          </div>
        </div>
      </nav>

      <div>
        <div class="inline-block min-w-full">
          <div>
            <table class="table w-full table-sm">
              <thead>
                <tr>
                  <th class="w-1/12 py-2 px-4 border-b">
                    <button @click="sortBy('id')" class="flex items-center justify-between w-full text-black">
                      ID
                      <ArrowsUpDownIcon class="w-4 h-4" :class="{ 'text-blue-500': sortColumn === 'id' }" />
                    </button>
                  </th>
                  <th class="w-3/12 py-2 px-4 border-b">
                    <button @click="sortBy('name')" class="flex items-center justify-between w-full text-black">
                      Name
                      <ArrowsUpDownIcon class="w-4 h-4" :class="{
                        'text-blue-500': sortColumn === 'name',
                      }" />
                    </button>
                  </th>
                  <th class="w-3/12 py-2 px-4 border-b">
                    <button @click="sortBy('email')" class="flex items-center justify-between w-full text-black">
                      Email
                      <ArrowsUpDownIcon class="w-4 h-4" :class="{
                        'text-blue-500': sortColumn === 'email',
                      }" />
                    </button>
                  </th>
                  <th class="w-2/12 py-2 px-4 border-b">
                    <button @click="sortBy('roles')" class="flex items-center justify-between w-full text-black">
                      Role
                      <ArrowsUpDownIcon class="w-4 h-4" :class="{
                        'text-blue-500': sortColumn === 'roles',
                      }" />
                    </button>
                  </th>
                  <th class="w-1/12 py-2 px-4 border-b">
                    <button @click="sortBy('email_verified_at')" class="flex items-center justify-between w-full text-black">
                      Status
                      <ArrowsUpDownIcon class="w-4 h-4" :class="{
                        'text-blue-500': sortColumn === 'email_verified_at',
                      }" />
                    </button>
                  </th>
                  <th class="w-2/12 py-2 px-4 border-b">
                    <button @click="sortBy('created_at')" class="flex items-center justify-between w-full text-black">
                      Joined
                      <ArrowsUpDownIcon class="w-4 h-4" :class="{
                        'text-blue-500': sortColumn === 'created_at',
                      }" />
                    </button>
                  </th>
                  <th class="w-1/12 py-2 px-4 border-b text-black">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="user in currentUsers" :key="user.id">
                  <td class="py-2 px-4 border-b">#{{ user.id }}</td>
                  <td class="py-2 px-4 border-b">
                    <div class="flex items-center space-x-3">
                      <div>
                        <div class="font-bold">{{ user.name }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="py-2 px-4 border-b">
                    {{ user.email }}
                  </td>
                  <td class="py-2 px-4 border-b">
                    <div class="flex items-center space-x-2">
                      <component :is="getRoleIcon(user.primary_role)" class="w-4 h-4" />
                      <span class="badge badge-sm" :class="getRoleBadgeClass(user.primary_role)">
                        {{ user.primary_role }}
                      </span>
                      <span v-if="user.roles.length > 1" class="text-xs text-gray-500">
                        +{{ user.roles.length - 1 }}
                      </span>
                    </div>
                  </td>
                  <td class="py-2 px-4 border-b">
                    <div class="flex items-center space-x-2">
                      <CheckBadgeIcon v-if="isUserVerified(user.email_verified_at)" class="w-4 h-4 text-green-500" />
                      <ClockIcon v-else class="w-4 h-4 text-yellow-500" />
                      <span class="badge badge-sm" :class="isUserVerified(user.email_verified_at) ? 'badge-success' : 'badge-warning'">
                        {{ isUserVerified(user.email_verified_at) ? 'Verified' : 'Pending' }}
                      </span>
                    </div>
                  </td>
                  <td class="py-2 px-4 border-b text-sm">
                    {{ formatDate(user.created_at) }}
                  </td>
                  <td class="py-2 px-4 border-b">
                    <div class="dropdown dropdown-left">
                      <div tabindex="0" role="button" class="btn btn-info m-1 btn-sm text-white">
                        Actions
                      </div>
                      <ul tabindex="0"
                        class="dropdown-content menu bg-base-100 rounded-box z-[1] w-52 p-2 shadow-lg dropdown-top">
                        <li>
                          <a href="#" @click.prevent="viewUser(user.id)">
                            <EyeIcon class="w-4 h-4" />
                            <span class="ml-2">View Details</span>
                          </a>
                        </li>
                        <li>
                          <a href="#" @click.prevent="manageUserRoles(user.id)">
                            <CogIcon class="w-4 h-4" />
                            <span class="ml-2">Manage Roles</span>
                          </a>
                        </li>
                        <li>
                          <a href="#" @click.prevent="toggleUserStatus(user.id, isUserVerified(user.email_verified_at))" 
                             :class="isUserVerified(user.email_verified_at) ? 'text-red-600' : 'text-green-600'">
                            <XCircleIcon v-if="isUserVerified(user.email_verified_at)" class="w-4 h-4" />
                            <CheckBadgeIcon v-else class="w-4 h-4" />
                            <span class="ml-2">{{ isUserVerified(user.email_verified_at) ? 'Deactivate' : 'Activate' }}</span>
                          </a>
                        </li>
                      </ul>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      
      <!-- Show message if no users -->
      <div v-if="currentUsers.length === 0" class="text-center py-12">
        <UsersIcon class="w-16 h-16 mx-auto text-gray-400 mb-4" />
        <h3 class="text-lg font-medium text-gray-900 mb-2">No users found</h3>
        <p class="text-gray-500">No users match your search criteria.</p>
      </div>
      
      <nav class="flex items-center justify-center mt-4" v-if="currentUsers.length > 0">
        <div class="join">
          <button class="join-item btn btn-sm" :disabled="currentPage === 1 || isLoading"
            @click="goToPage(currentPage - 1)">
            «
          </button>
          <button class="join-item btn btn-sm">Page {{ currentPage }}</button>
          <button class="join-item btn btn-sm" :disabled="currentPage === totalPages || isLoading"
            @click="goToPage(currentPage + 1)">
            »
          </button>
        </div>
      </nav>
    </div>
  </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, defineProps, computed, watch } from 'vue';
import NavLink from "@/Components/NavLink.vue";
import {
  ArrowUpCircleIcon,
  HomeIcon,
  ArrowsUpDownIcon,
  WindowIcon,
  DocumentMagnifyingGlassIcon,
  CubeIcon,
  EyeIcon,
  PencilIcon,

} from "@heroicons/vue/24/solid";

//  Instantiating the props
const props = defineProps({
  allProducts: {
    type: Array,
    default: () => []
  },
  products: {
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
const isInitialLoad = computed(() => !props.products || props.products.length === 0);

// Use either paginated products or all products for initial load
const currentProducts = computed(() => {
  return isInitialLoad.value ? props.allProducts.slice(0, 10) : props.products;
});

// Instantiating the pagination variables
const currentPage = ref(isInitialLoad.value ? 1 : (props.pagination?.current_page || 1));
const productsPerPage = 10;

// For initial load, calculate pagination from allProducts
const totalPages = computed(() => {
  if (isInitialLoad.value) {
    return Math.ceil(props.allProducts.length / productsPerPage);
  }
  return props.pagination?.last_page || 1;
});

const totalCount = computed(() => {
  return isInitialLoad.value ? props.allProducts.length : (props.pagination?.total || 0);
});

const fromCount = computed(() => {
  if (isInitialLoad.value) {
    return ((currentPage.value - 1) * productsPerPage) + 1;
  }
  return props.pagination?.from || 0;
});

const toCount = computed(() => {
  if (isInitialLoad.value) {
    return Math.min(currentPage.value * productsPerPage, props.allProducts.length);
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
const fetchProducts = (params = {}) => {
  isLoading.value = true;

  const requestData = {
    page: currentPage.value,
    search: searchQuery.value,
    sort: sortColumn.value,
    direction: sortDirection.value,
    ...params
  };

  router.get(route('products.index'), requestData, {
    preserveState: true,
    preserveScroll: true,
    replace: true,
    only: ['products', 'pagination', 'filters'],
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
      fetchProducts({ page });
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
    fetchProducts({ page: 1, search: searchQuery.value });
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
  fetchProducts({ page: 1, sort: sortColumn.value, direction: sortDirection.value });
};

// Format date function
const formatDate = (dateString) => {
  const order_date = new Date(dateString);
  return `${order_date.getDate()}-${order_date.getMonth() + 1
    }-${order_date.getFullYear()} ${order_date.getHours()}:${order_date.getMinutes()}:${order_date.getSeconds()}`;
};

// View product function (placeholder)
const viewProduct = (id) => {
  // Navigate to product show page when implemented
  console.log('View product:', id);
  // router.get(route('products.show', id));
};
</script>

<template>
  <Head title="Products" />

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
            <CubeIcon class="w-4 h-4" />
            <span class="ml-4">Products</span>
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
            products
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
            <input type="text" placeholder="Search products..." class="input border-b w-24 md:w-auto input-sm text-black"
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
                  <th class="w-2/12 py-2 px-4 border-b">
                    <button @click="sortBy('price')" class="flex items-center justify-between w-full text-black">
                      Price
                      <ArrowsUpDownIcon class="w-4 h-4" :class="{
                        'text-blue-500': sortColumn === 'price',
                      }" />
                    </button>
                  </th>
                  <th class="w-1/12 py-2 px-4 border-b">
                    <button @click="sortBy('stock')" class="flex items-center justify-between w-full text-black">
                      Stock
                      <ArrowsUpDownIcon class="w-4 h-4" :class="{
                        'text-blue-500': sortColumn === 'stock',
                      }" />
                    </button>
                  </th>
                  <th class="w-2/12 py-2 px-4 border-b">
                    <button @click="sortBy('created_at')" class="flex items-center justify-between w-full text-black">
                      Date
                      <ArrowsUpDownIcon class="w-4 h-4" :class="{
                        'text-blue-500': sortColumn === 'created_at',
                      }" />
                    </button>
                  </th>
                  <th class="w-2/12 py-2 px-4 border-b text-black">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in currentProducts" :key="item.id">
                  <td class="py-2 px-4 border-b">#{{ item.id }}</td>
                  <td class="py-2 px-4 border-b font-medium">
                    {{ item.name }}
                  </td>
                  <td class="py-2 px-4 border-b font-semibold">
                    ${{ parseFloat(item.price).toFixed(2) }}
                  </td>
                  <td class="py-2 px-4 border-b">
                    <span class="badge badge-sm" :class="item.stock > 10 ? 'badge-success' : (item.stock > 0 ? 'badge-warning' : 'badge-error')">
                      {{ item.stock }}
                    </span>
                  </td>
                  <td class="py-2 px-4 border-b text-sm">
                    {{ formatDate(item.created_at) }}
                  </td>
                  <td class="py-2 px-4 border-b">
                    <div class="dropdown dropdown-left">
                      <div tabindex="0" role="button" class="btn btn-info m-1 btn-sm text-white">
                        Actions
                      </div>
                      <ul tabindex="0"
                        class="dropdown-content menu bg-base-100 rounded-box z-[1] w-52 p-2 shadow-lg dropdown-top">
                        <li>
                          <NavLink :href="route('products.edit', item.id)">
                            <PencilIcon class="w-4 h-4" />
                            <span class="ml-2">Edit</span>
                          </NavLink>
                        </li>
                        <li>
                          <a href="#" @click.prevent="viewProduct(item.id)">
                            <EyeIcon class="w-4 h-4" />
                            <span class="ml-2">View</span>
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
      
      <!-- Show message if no products -->
      <div v-if="currentProducts.length === 0" class="text-center py-12">
        <CubeIcon class="w-16 h-16 mx-auto text-gray-400 mb-4" />
        <h3 class="text-lg font-medium text-gray-900 mb-2">No products found</h3>
        <p class="text-gray-500">No products available in the inventory.</p>
      </div>
      
      <nav class="flex items-center justify-center mt-4" v-if="currentProducts.length > 0">
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
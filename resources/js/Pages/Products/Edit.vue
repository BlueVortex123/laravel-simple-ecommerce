<template>
  <Head title="Edit Product" />

  <AuthenticatedLayout>
    <div class="breadcrumbs text-sm mb-4 text-black">
      <ul class="flex items-center space-x-4">
        <li>
          <NavLink :href="route('dashboard')" class="flex items-center">
            <HomeIcon class="w-4 h-4" />
            <span class="ml-4">Dashboard</span>
          </NavLink>
        </li>
        <li class="text-gray-400 mx-2 select-none">&gt;</li>
        <li>
          <NavLink :href="route('products.index')" class="flex items-center">
            <CubeIcon class="w-4 h-4" />
            <span class="ml-4">Products</span>
          </NavLink>
        </li>
        <li class="text-gray-400 mx-2 select-none">&gt;</li>
        <li>
          <a class="flex items-center">
            <PencilIcon class="w-4 h-4" />
            <span class="ml-4">Edit Product</span>
          </a>
        </li>
      </ul>
    </div>

    <div class="w-full max-w-4xl mx-auto text-black">
      <div class="card bg-white shadow-xl">
        <div class="card-body">
          <div class="flex items-center justify-between mb-6">
            <h2 class="card-title text-2xl">Edit Product</h2>
            <NavLink :href="route('products.index')" class="btn btn-outline btn-sm">
              <ArrowLeftIcon class="w-4 h-4 mr-2" />
              Back to Products
            </NavLink>
          </div>

          <!-- Display Success/Error Messages -->
          <div v-if="$page.props.flash?.success" class="alert alert-success mb-4">
            <CheckCircleIcon class="w-6 h-6" />
            <span>{{ $page.props.flash.success }}</span>
          </div>
          
          <div v-if="$page.props.flash?.error" class="alert alert-error mb-4">
            <XCircleIcon class="w-6 h-6" />
            <span>{{ $page.props.flash.error }}</span>
          </div>

          <form @submit.prevent="updateProduct" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Product Name -->
              <div class="form-control">
                <label class="label">
                  <span class="label-text font-medium">Product Name <span class="text-red-500">*</span></span>
                </label>
                <input
                  type="text"
                  v-model="form.name"
                  class="input input-bordered w-full"
                  :class="{ 'input-error': form.errors.name }"
                  placeholder="Enter product name"
                  required
                />
                <div v-if="form.errors.name" class="label">
                  <span class="label-text-alt text-red-500">{{ form.errors.name }}</span>
                </div>
              </div>

              <!-- Product Price -->
              <div class="form-control">
                <label class="label">
                  <span class="label-text font-medium">Price <span class="text-red-500">*</span></span>
                </label>
                <input
                  type="number"
                  step="0.01"
                  min="0"
                  v-model="form.price"
                  class="input input-bordered w-full"
                  :class="{ 'input-error': form.errors.price }"
                  placeholder="0.00"
                  required
                />
                <div v-if="form.errors.price" class="label">
                  <span class="label-text-alt text-red-500">{{ form.errors.price }}</span>
                </div>
              </div>

              <!-- Stock Quantity -->
              <div class="form-control">
                <label class="label">
                  <span class="label-text font-medium">Stock Quantity</span>
                </label>
                <input
                  type="number"
                  min="0"
                  v-model="form.stock"
                  class="input input-bordered w-full"
                  :class="{ 'input-error': form.errors.stock }"
                  placeholder="0"
                />
                <div v-if="form.errors.stock" class="label">
                  <span class="label-text-alt text-red-500">{{ form.errors.stock }}</span>
                </div>
              </div>
            </div>

            <!-- Product Description -->
            <div class="form-control">
              <label class="label">
                <span class="label-text font-medium">Description</span>
              </label>
              <textarea
                v-model="form.description"
                class="textarea textarea-bordered w-full h-32"
                :class="{ 'textarea-error': form.errors.description }"
                placeholder="Enter product description..."
              ></textarea>
              <div v-if="form.errors.description" class="label">
                <span class="label-text-alt text-red-500">{{ form.errors.description }}</span>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-4 pt-4">
              <NavLink :href="route('products.index')" class="btn btn-outline">
                Cancel
              </NavLink>
              <button 
                type="submit" 
                class="btn btn-primary"
                :class="{ 'loading': form.processing }"
                :disabled="form.processing"
              >
                <span v-if="!form.processing">Update Product</span>
                <span v-else>Updating...</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { watch } from 'vue';
import NavLink from "@/Components/NavLink.vue";
import {
  HomeIcon,
  CubeIcon,
  PencilIcon,
  ArrowLeftIcon,
  CheckCircleIcon,
  XCircleIcon,
} from "@heroicons/vue/24/solid";

// Define props
const props = defineProps({
  product: {
    type: Object,
    required: true
  }
});

// Initialize form with existing product data (useForm as a single reactive object)
const form = useForm({
  name: props.product?.name ?? '',
  description: props.product?.description ?? '',
  price: props.product?.price ?? '',
  stock: props.product?.stock ?? 0,
  image: null,
});

// If product arrives/changes asynchronously, keep the form in sync
watch(
  () => props.product,
  (p) => {
    if (p) {
      form.name = p.name ?? '';
      form.description = p.description ?? '';
      form.price = p.price ?? '';
      form.stock = p.stock ?? 0;
    }
  },
  { immediate: true }
);

// Handle image file upload
const handleImageUpload = (event) => {
  const file = event.target.files[0];
  form.image = file;
};

// Submit form to update product
const updateProduct = () => {
  // Use POST with _method=PUT so file uploads are handled correctly by the backend
  form.post(route('products.update', props.product.id), {
    data: { _method: 'PUT' },
    forceFormData: true,
    onSuccess: () => {
      // Success handling (Inertia will follow backend redirects/flash)
    },
    onError: () => {
      // Errors will be displayed automatically
    }
  });
};
</script>
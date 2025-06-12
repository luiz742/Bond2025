<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, router, usePage } from '@inertiajs/vue3'

const page = usePage()
const user = page.props.auth.user

// Menu definitions
const adminMenus = [
  { name: 'Clients', icon: '👥', href: '/admin/clients' },
  { name: 'Services', icon: '🌐', href: '/admin/services' },
  { name: 'Users', icon: '🧑‍💼', href: '/admin/users' },
  { name: 'Settings', icon: '⚙️', href: '/user/profile' },
  { name: 'Logout', icon: '🚪', action: 'logout' }
]

const userMenus = [
  { name: 'Clients', icon: '👥', href: '/clients' },
  { name: 'Settings', icon: '⚙️', href: '/user/profile' },
  { name: 'Logout', icon: '🚪', action: 'logout' }
]

// Choose menu and layout based on user role
const isAdmin = ['super_admin', 'admin'].includes(user.role)
const menus = isAdmin ? adminMenus : userMenus
const layoutComponent = isAdmin ? AdminLayout : AppLayout

// Logout handler
const handleAction = (menu) => {
  if (menu.action === 'logout') {
    router.post(route('logout'))
  }
}
</script>

<template>
  <component :is="layoutComponent" title="Dashboard">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Dashboard
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="menu in menus"
            :key="menu.name"
            @click="menu.action === 'logout' ? handleAction(menu) : null"
          >
            <Link
              v-if="!menu.action"
              :href="menu.href"
              class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow hover:shadow-lg transition flex flex-col items-start"
            >
              <div class="text-4xl mb-4">{{ menu.icon }}</div>
              <div class="text-lg font-semibold text-gray-800 dark:text-white">
                {{ menu.name }}
              </div>
            </Link>
            <button
              v-else
              class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow hover:shadow-lg transition flex flex-col items-start w-full text-left"
            >
              <div class="text-4xl mb-4">{{ menu.icon }}</div>
              <div class="text-lg font-semibold text-gray-800 dark:text-white">
                {{ menu.name }}
              </div>
            </button>
          </div>
        </div>
      </div>
    </div>
  </component>
</template>

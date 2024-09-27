<script setup>
import {reactive, ref} from 'vue';
import {useRootStore} from "../stores/root";

import Menu from 'primevue/menu';
import {useRoute} from 'vue-router';
const route = useRoute();
const store = useRootStore();

const inputs = {
}
const data = reactive(inputs);
const height = ref(window.innerHeight)

const menu = ref();

const menu_pt = ref({
  menuitem: ({ props }) => ({
    class: route.matched && route.matched[1] &&
    route.matched[1].path === props.item.route ? 'p-focus' : ''
  })
});

const items = ref([
    {
        label: 'Appointments',
        items: [
            {
                label: 'Dashboard',
                icon: 'fa-regular fa-chart-bar',
                route: "/",
                permissions: [ "appointments-can-create-patients" , "appointments-can-create-doctors" ]
            },
            {
                label: 'Doctors',
                icon: 'fa-regular fa-chart-bar',
                route: "/doctors",
                permissions: ["can-login-in-backend", "appointments-can-create-doctors" ]
            },
            {
                label: 'Patients',
                icon: 'fa-regular fa-chart-bar',
                route: "/patients",
                permissions: ["can-login-in-backend", "appointments-can-create-patients"]
            },
            {
                label: 'Appointments',
                icon: 'fa-regular fa-chart-bar',
                route: "/appointments",
                permission: [ "can-login-in-backend", "appointments-can-create-patients" , "appointments-can-create-doctors" ]
            }
        ]
    },
]);
</script>
<template>
    <div v-if="height && store.assets">
      <Menu :model="items"  class="w-full"
            :pt="menu_pt">
        <template #item="{ item, props }">
          <router-link v-if="item.route && store.assets.permission.every(perm => item.permission.includes(perm))" v-slot="{ href, navigate }" :to="item.route" custom>
            <a v-ripple :href="href" v-bind="props.action" @click="navigate">
              <span :class="item.icon" />
              <span class="ml-2">{{ item.label }}</span>
            </a>
          </router-link>
        </template>
      </Menu>
    </div>

</template>



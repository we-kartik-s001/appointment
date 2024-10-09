<script  setup>

import { useDoctorStore } from '../../../stores/store-doctors'
import VhFieldVertical from './../../../vaahvue/vue-three/primeflex/VhFieldVertical.vue'
import { useToast } from "primevue/usetoast";
import { useConfirm } from "primevue/useconfirm";
import {onBeforeMount} from "vue";

const confirm = useConfirm();
const toast = useToast();
const time_range = ['09:00-12:00','12:00-15:00','15:00-18:00','18:00-21:00'];
const price_range = ['0-20','20-40','40-60','60-80','80-100'];
const store = useDoctorStore();

onBeforeMount(() => {
    store.getSpecializationList();
});
</script>

<template>
    <div class="col-3" v-if="store.show_field_filters">

        <Panel class="is-small">

            <template class="p-1" #header>

                <div class="flex flex-row">
                    <div >
                        <b class="mr-1">Apply field based filters</b>
                    </div>

                </div>

            </template>

            <template #icons>

                <div class="p-inputgroup">

                    <Button data-testid="doctors-hide-filter"
                            class="p-button-sm"
                            @click="store.show_field_filters = false">
                        <i class="pi pi-times"></i>
                    </Button>

                </div>

            </template>

            <VhFieldVertical >
                <template #label>
                    <b>Specialization:</b>
                </template>

                <div v-for="(specialization,index) in store.specializations" :key="index" class="field-radiobutton">
                    <RadioButton name="active-all"
                                 :inputId="specialization"
                                 :value="specialization"
                                 v-model="store.query.field_filter.specialization" />
                    <label for="active-all" class="cursor-pointer">{{specialization}}</label>
                </div>

            </VhFieldVertical>

            <Divider/>

            <VhFieldVertical >
                <template #label>
                    <b>Price:</b>
                </template>

                <div v-for="(price,index) in price_range" :key="index" class="field-radiobutton">
                    <RadioButton name="active-all"
                                 :inputId="price"
                                 :value="price"
                                 data-testid="doctors-filters-active-all"
                                 v-model="store.query.field_filter.price" />
                    <label for="active-all" class="cursor-pointer">{{price}}</label>
                </div>
            </VhFieldVertical>

            <Divider/>

            <VhFieldVertical >
                <template #label>
                    <b>Timings:</b>
                </template>

                <div v-for="(timing,index) in time_range" :key="index" class="field-radiobutton">
                    <RadioButton name="active-all"
                                 :inputId="timing"
                                 :value="timing"
                                 data-testid="doctors-filters-active-all"
                                 v-model="store.query.field_filter.timings" />
                    <label for="active-all" class="cursor-pointer">{{timing}}</label>
                </div>

            </VhFieldVertical>


        </Panel>

    </div>
</template>

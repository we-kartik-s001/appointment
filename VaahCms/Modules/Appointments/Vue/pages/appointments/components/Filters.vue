<script  setup>

import { useAppointmentStore } from '../../../stores/store-appointments'
import VhFieldVertical from './../../../vaahvue/vue-three/primeflex/VhFieldVertical.vue'
import useMobileView from "../../../mixins/MobileMixin.js";

const { isMobile } = useMobileView();
const store = useAppointmentStore();

</script>

<template>
    <div class="col-3" v-if="store.show_filters && !isMobile">
        <Panel class="is-small">

                <template class="p-1" #header>

                    <div class="flex flex-row">
                        <div >
                            <b class="mr-1">Filters</b>
                        </div>

                    </div>

                </template>

                <template #icons>

                    <div class="p-inputgroup">

                        <Button data-testid="appointments-hide-filter"
                                class="p-button-sm"
                                @click="store.show_filters = false">
                            <i class="pi pi-times"></i>
                        </Button>

                    </div>

                </template>

            <VhFieldVertical >
                <template #label>
                    <b>Sort By:</b>
                </template>
                <div class="field-radiobutton">
                    <RadioButton name="sort-none"
                                 inputId="sort-none"
                                 data-testid="appointments-filters-sort-none"
                                 value=""
                                 v-model="store.query.filter.sort" />
                    <label for="sort-none" class="cursor-pointer">None</label>
                </div>
                <div class="field-radiobutton">
                    <RadioButton name="sort-ascending"
                                 inputId="sort-ascending"
                                 data-testid="appointments-filters-sort-ascending"
                                 value="created_at"
                                 v-model="store.query.filter.sort" />
                    <label for="sort-ascending" class="cursor-pointer">Date of booking (Ascending)</label>
                </div>
                <div class="field-radiobutton">
                    <RadioButton name="sort-descending"
                                 inputId="sort-descending"
                                 data-testid="appointments-filters-sort-descending"
                                 value="created_at:desc"
                                 v-model="store.query.filter.sort" />
                    <label for="sort-descending" class="cursor-pointer">Date of booking (Descending)</label>
                </div>

            </VhFieldVertical>

            <Divider/>

            <VhFieldVertical >
                <template #label>
                    <b>Booking Status:</b>
                </template>

                <div class="field-radiobutton">
                    <RadioButton name="active-all"
                                 inputId="active-all"
                                 value="null"
                                 data-testid="appointments-filters-active-all"
                                 v-model="store.query.filter.is_active" />
                    <label for="active-all" class="cursor-pointer">All</label>
                </div>
                <div class="field-radiobutton">
                    <RadioButton name="active-true"
                                 inputId="active-true"
                                 data-testid="appointments-filters-active-true"
                                 value=1
                                 v-model="store.query.filter.is_active" />
                    <label for="active-true" class="cursor-pointer">Only Booked</label>
                </div>
                <div class="field-radiobutton">
                    <RadioButton name="active-false"
                                 inputId="active-false"
                                 data-testid="appointments-filters-active-false"
                                 value=0
                                 v-model="store.query.filter.is_active" />
                    <label for="active-false" class="cursor-pointer">Only Cancelled</label>
                </div>

            </VhFieldVertical>

             <Divider/>

            <VhFieldVertical >
                <template #label>
                    <b>Show Appointments:</b>
                </template>

                <div class="field-radiobutton">
                    <RadioButton name="trashed-exclude"
                                 inputId="trashed-exclude"
                                 data-testid="appointments-filters-trashed-exclude"
                                 value=""
                                 v-model="store.query.filter.trashed" />
                    <label for="trashed-exclude" class="cursor-pointer">Exclude Trashed</label>
                </div>
                <div class="field-radiobutton">
                    <RadioButton name="trashed-include"
                                 inputId="trashed-include"
                                 data-testid="appointments-filters-trashed-include"
                                 value="include"
                                 v-model="store.query.filter.trashed" />
                    <label for="trashed-include" class="cursor-pointer">Include Trashed</label>
                </div>
                <div class="field-radiobutton">
                    <RadioButton name="trashed-only"
                                 inputId="trashed-only"
                                 data-testid="appointments-filters-trashed-only"
                                 value="only"
                                 v-model="store.query.filter.trashed" />
                    <label for="trashed-only" class="cursor-pointer">Only Trashed</label>
                </div>

            </VhFieldVertical>


        </Panel>
    </div>

    <div class="col-3" v-else-if="store.show_filters && isMobile">
        <div class="sort-by-filter-container">
            <div class="bg-white p-4 rounded-lg shadow-md">
                <h3 class="text-lg font-semibold mb-2">Sort By:</h3>
                <div class="flex flex-col space-y-2">
                    <div class="field-radiobutton flex items-center mr-1">
                        <RadioButton
                            name="sort-none"
                            inputId="sort-none"
                            data-testid="appointments-filters-sort-none"
                            value=""
                            v-model="store.query.filter.sort"
                            class="mr-0.5"
                        />
                        <label for="sort-none" class="cursor-pointer text-gray-700">None</label>
                    </div>
                    <div class="field-radiobutton flex items-center">
                        <RadioButton
                            name="sort-ascending"
                            inputId="sort-ascending"
                            data-testid="appointments-filters-sort-ascending"
                            value="created_at"
                            v-model="store.query.filter.sort"
                            class="mr-0.5"
                        />
                        <label for="sort-ascending" class="cursor-pointer text-gray-700">Date of booking (Ascending)</label>
                    </div>
                    <div class="field-radiobutton flex items-center">
                        <RadioButton
                            name="sort-descending"
                            inputId="sort-descending"
                            data-testid="appointments-filters-sort-descending"
                            value="created_at:desc"
                            v-model="store.query.filter.sort"
                            class="mr-0.5"
                        />
                        <label for="sort-descending" class="cursor-pointer text-gray-700">Date of booking (Descending)</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="booking-status-filter-container bg-white p-4 rounded-lg shadow-md mb-4">
            <h3 class="text-lg font-semibold mb-2">Booking Status:</h3>
            <div class="flex flex-col space-y-2">
                <div class="field-radiobutton flex items-center mr-2">
                    <RadioButton
                        name="active-all"
                        inputId="active-all"
                        value="null"
                        data-testid="appointments-filters-active-all"
                        v-model="store.query.filter.is_active"
                        class="mr-0.5"
                    />
                    <label for="active-all" class="cursor-pointer text-gray-700">All</label>
                </div>
                <div class="field-radiobutton flex items-center mr-2">
                    <RadioButton
                        name="active-true"
                        inputId="active-true"
                        data-testid="appointments-filters-active-true"
                        value="1"
                        v-model="store.query.filter.is_active"
                        class="mr-0.5"
                    />
                    <label for="active-true" class="cursor-pointer text-gray-700">Only Booked</label>
                </div>
                <div class="field-radiobutton flex items-center">
                    <RadioButton
                        name="active-false"
                        inputId="active-false"
                        data-testid="appointments-filters-active-false"
                        value="0"
                        v-model="store.query.filter.is_active"
                        class="mr-0.5"
                    />
                    <label for="active-false" class="cursor-pointer text-gray-700">Only Cancelled</label>
                </div>
            </div>
        </div>
        <div class="trashed-status-filter-container bg-white p-4 rounded-lg shadow-md mb-4">
            <h3 class="text-lg font-semibold mb-2">Show Appointments:</h3>
            <div class="flex flex-col space-y-2">
                <div class="field-radiobutton flex items-center mr-2">
                    <RadioButton
                        name="trashed-exclude"
                        inputId="trashed-exclude"
                        data-testid="appointments-filters-trashed-exclude"
                        value=""
                        v-model="store.query.filter.trashed"
                        class="mr-0.5"
                    />
                    <label for="trashed-exclude" class="cursor-pointer text-gray-700">Exclude Trashed</label>
                </div>
                <div class="field-radiobutton flex items-center mr-2">
                    <RadioButton
                        name="trashed-include"
                        inputId="trashed-include"
                        data-testid="appointments-filters-trashed-include"
                        value="include"
                        v-model="store.query.filter.trashed"
                        class="mr-0.5"
                    />
                    <label for="trashed-include" class="cursor-pointer text-gray-700">Include Trashed</label>
                </div>
                <div class="field-radiobutton flex items-center">
                    <RadioButton
                        name="trashed-only"
                        inputId="trashed-only"
                        data-testid="appointments-filters-trashed-only"
                        value="only"
                        v-model="store.query.filter.trashed"
                        class="mr-0.5"
                    />
                    <label for="trashed-only" class="cursor-pointer text-gray-700">Only Trashed</label>
                </div>
            </div>
        </div>

    </div>
</template>

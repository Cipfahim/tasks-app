<script setup>
import {useStore} from '@/store';
import NotificationItem from "@/Components/NotificationItem.vue";
import {onMounted, watch} from "vue";
import {usePage} from '@inertiajs/vue3';

const store = useStore();

watch(
    () => usePage().props.flash,
    (newFlash, oldFlash) => {
        if (newFlash.success) {
            store.notify('success', newFlash.success)
        } else if (newFlash.error) {
            store.notify('error', newFlash.error)
        }
    },
    {deep: true}
)
onMounted(() => {
    if (usePage().props.flash && !store.notifications.length) {
        if (usePage().props.flash.success) {
            store.notify('success', usePage().props.flash.success)
        } else if (usePage().props.flash.error) {
            store.notify('error', usePage().props.flash.error)
        }
    }
})
</script>

<template>
    <!-- Global notification live region, render this permanently at the end of the document -->
    <div aria-live="assertive"
         class="pointer-events-none fixed inset-0 flex items-end px-4 py-6 sm:items-start sm:p-6 z-50">
        <div class="flex w-full flex-col items-center space-y-4 sm:items-end">
            <!-- Notification panel, dynamically insert this into the live region when it needs to be displayed -->
            <transition v-for="(notification, index) in store.notifications"
                        enter-active-class="transform ease-out duration-300 transition"
                        enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                        enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
                        leave-active-class="transition ease-in duration-100" leave-from-class="opacity-100"
                        leave-to-class="opacity-0">
                <NotificationItem :notification="notification" :index="index"/>
            </transition>
        </div>
    </div>
</template>

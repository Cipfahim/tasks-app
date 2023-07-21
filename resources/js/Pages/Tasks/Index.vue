<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DialogModal from "@/Components/DialogModal.vue";
import TextInput from "@/Components/TextInput.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import InputError from "@/Components/InputError.vue";
import {ref} from "vue";
import {Link, useForm} from "@inertiajs/vue3";
// Import quill editor
import {QuillEditor} from "@vueup/vue-quill";
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import Pagination from "@/Components/Pagination.vue";
import SingleTask from "@/Components/SingleTask.vue";

const props = defineProps({
    users: Array,
    tasks: Object
})

const showCreateModal = ref(false)

const form = useForm({
    title: null,
    description: null,
    deadline: null
})

const closeCreateModal = () => {
    showCreateModal.value = false
}

const submit = () => {
    form.post(route('tasks.store'), {
        onSuccess: () => {
            form.reset()
            closeCreateModal()
        }
    })
}

</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tasks App
            </h2>

            <PrimaryButton @click="showCreateModal = true">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                <span>Task</span>
            </PrimaryButton>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow-xl sm:rounded-lg">
                    <div class="flex border-b">
                        <Link :href="route('tasks.index')"
                              class="text-base px-4 py-2 rounded-t-lg hover:bg-gray-50"
                              :class="{'font-semibold  border-indigo-400 border-b':route().current('tasks.index')}">
                            All Tasks
                        </Link>
                        <Link :href="route('tasks.assigned')"
                              class="text-base px-4 py-2 rounded-t-lg hover:bg-gray-50"
                              :class="{'font-semibold  border-indigo-400 border-b':route().current('tasks.assigned')}">
                            My Tasks
                        </Link>
                    </div>
                    <SingleTask v-if="tasks.data.length" v-for="task in tasks.data" :task="task" :users="users"/>

                    <div v-else class="text-center py-10">
                        No data found.
                    </div>

                    <Pagination :links="tasks.links"/>
                </div>
            </div>
        </div>
    </AppLayout>

    <DialogModal :show="showCreateModal" @close="closeCreateModal">
        <template #title>
            New task
        </template>

        <template #content>
            <div class="space-y-4">
                <div>
                    <label for="title">Title</label>
                    <TextInput
                        v-model="form.title"
                        type="text"
                        id="title"
                        class="w-full"
                        placeholder="Enter task title"
                    />
                    <InputError :message="form.errors.title" class="mt-2"/>
                </div>


                <!-- Description -->
                <div>
                    <label for="content">Description</label>
                    <QuillEditor
                        id="Description"
                        v-model:content="form.description"
                        content-type="html"
                    />
                    <InputError :message="form.errors.description" class="mt-2"/>
                </div>

                <!-- Deadline -->
                <div>
                    <label for="deadline">Deadline</label>
                    <TextInput
                        v-model="form.deadline"
                        type="date"
                        id="deadline"
                        class="w-full"
                    />
                    <InputError :message="form.errors.deadline" class="mt-2"/>
                </div>
            </div>
        </template>

        <template #footer>
            <SecondaryButton @click="closeCreateModal">
                Cancel
            </SecondaryButton>

            <PrimaryButton
                type="button"
                @click.prevent="submit"
                class="ml-3"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                Create task
            </PrimaryButton>
        </template>
    </DialogModal>
</template>

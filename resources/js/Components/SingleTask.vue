<script setup>
import moment from "moment";
import {
    Combobox,
    ComboboxButton,
    ComboboxInput,
    ComboboxOption,
    ComboboxOptions,
    Menu,
    MenuButton,
    MenuItem,
    MenuItems
} from "@headlessui/vue";
import {computed, ref} from "vue";
import {router, useForm} from "@inertiajs/vue3";
import DialogModal from "@/Components/DialogModal.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {QuillEditor} from "@vueup/vue-quill";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";

const props = defineProps({
    task: Object,
    users: Array
})

const showInviteBox = ref(false)

const selectedUser = ref(null)
const query = ref('')

const filteredUsers = computed(() =>
    query.value === ''
        ? props.users
        : props.users.filter((user) => {
            return user.name.toLowerCase().includes(query.value.toLowerCase())
        })
)

const assignUser = () => {
    router.post(route('tasks.assign', props.task), {
        user: selectedUser.value.id
    }, {
        onSuccess: () => {
            showInviteBox.value = false
        }
    })
}

const removeUser = (user) => {
    router.post(route('tasks.remove', props.task), {
        user: user.id
    })
}

const showDetailsModal = ref(false)

const showUpdateModal = ref(false)

const form = useForm({
    title: props.task.title,
    description: props.task.description,
    deadline: props.task.deadline
})

const closeUpdateModal = () => {
    showUpdateModal.value = false
}

const update = () => {
    form.put(route('tasks.update', props.task), {
        onSuccess: () => {
            form.reset()
            closeUpdateModal()
        }
    })
}

const showDeleteModal = ref(false)
const destroy = () => {
    router.delete(route('tasks.destroy', props.task), {
        onSuccess: () => {
            showDeleteModal.value = false
        }
    })
}
</script>

<template>
    <div class="flex justify-between items-center border-b py-2 px-4">
        <div class="flex flex-col gap-0.5">
            <h2 class="text-base">{{ task.title }}</h2>
            <p class="text-sm text-gray-400">{{ task.creator?.name }} . {{ moment(task.created_at).fromNow() }}</p>
        </div>
        <div class="flex gap-4 items-center">
            <!--Assignees-->
            <div class="flex">
                <dd class="relative group" v-for="user in task.users">
                    <img class="w-8 h-8 rounded-full"
                         :src="user.profile_photo_url"
                         :alt="user.name">

                    <button @click="removeUser(user)"
                            class="absolute top-0 right-0 text-red-500 hidden group-hover:block transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                             stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </button>
                </dd>

                <dd>
                    <div class="relative">
                        <button class="rounded-full bg-gray-50 p-1.5 hover:scale-110"
                                @click="showInviteBox = !showInviteBox">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                 stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z"/>
                            </svg>
                        </button>
                        <div v-if="showInviteBox" class="absolute w-max z-20">
                            <Combobox v-model="selectedUser" @update:modelValue="assignUser">
                                <div class="relative mt-1">
                                    <div
                                        class="relative w-full cursor-default overflow-hidden rounded-lg bg-white text-left shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-opacity-75 focus-visible:ring-offset-2 focus-visible:ring-offset-teal-300 sm:text-sm"
                                    >
                                        <ComboboxInput
                                            class="w-full border-none py-2 pl-3 pr-10 text-sm leading-5 text-gray-900 focus:ring-0"
                                            @change="query = $event.target.value"
                                        />
                                        <ComboboxButton
                                            class="absolute inset-y-0 right-0 flex items-center pr-2"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                 stroke-width="1.5" stroke="currentColor" class="h-5 w-5 text-gray-400">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9"/>
                                            </svg>
                                        </ComboboxButton>
                                    </div>
                                    <ComboboxOptions
                                        static
                                        class="absolute mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
                                    >
                                        <div
                                            v-if="filteredUsers.length === 0 && query !== ''"
                                            class="relative cursor-default select-none py-2 px-4 text-gray-700"
                                        >
                                            Nothing found.
                                        </div>

                                        <ComboboxOption
                                            v-for="user in filteredUsers"
                                            as="template"
                                            :key="user.id"
                                            :value="user"
                                            v-slot="{ selectedUser, active }"
                                        >
                                            <li
                                                class="relative cursor-default select-none py-2 pl-10 pr-4"
                                                :class="{
                                                      'bg-gray-800 text-white': active,
                                                      'text-gray-900': !active,
                                                    }"
                                            >
                                                    <span
                                                        class="block truncate"
                                                        :class="{ 'font-medium': selectedUser, 'font-normal': !selectedUser }"
                                                    >
                                                      {{ user.name }}
                                                    </span>
                                                <span
                                                    class="absolute inset-y-0 left-0 flex items-center pl-3"
                                                    :class="{ 'text-white': active, 'text-teal-600': !active }"
                                                >
                                                        <img class="w-5 h-5 rounded-full"
                                                             :src="user.profile_photo_url"
                                                             :alt="user.name">
                                                    </span>
                                            </li>
                                        </ComboboxOption>
                                    </ComboboxOptions>
                                </div>
                            </Combobox>
                        </div>
                    </div>
                </dd>
            </div>

            <!--Show details-->
            <button @click="showDetailsModal = true"
                    class="flex gap-1 items-center px-3 py-1 text-base border rounded-lg hover:bg-gray-50">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                View
            </button>

            <!--More options-->
            <Menu as="div" class="relative inline-block text-left">
                <div>
                    <MenuButton
                        class="border border-transparent hover:border-gray-300 hover:rounded-full hover:bg-gray-50"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                             stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 6.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 12.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 18.75a.75.75 0 110-1.5.75.75 0 010 1.5z"/>
                        </svg>
                    </MenuButton>
                </div>

                <transition
                    enter-active-class="transition duration-100 ease-out"
                    enter-from-class="transform scale-95 opacity-0"
                    enter-to-class="transform scale-100 opacity-100"
                    leave-active-class="transition duration-75 ease-in"
                    leave-from-class="transform scale-100 opacity-100"
                    leave-to-class="transform scale-95 opacity-0"
                >
                    <MenuItems
                        class="absolute right-0 mt-2 w-56 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-10"
                    >
                        <div class="px-1 py-1">
                            <MenuItem v-slot="{ active }">
                                <button
                                    @click="showUpdateModal = true"
                                    :class="[
                                      active ? 'bg-gray-800 text-white' : 'text-gray-900',
                                      'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                                    ]"
                                >
                                    Edit
                                </button>
                            </MenuItem>
                            <MenuItem v-slot="{ active }">
                                <button
                                    @click="showDeleteModal = true"
                                    :class="[
                                      active ? 'bg-red-500 text-white' : 'text-gray-900',
                                      'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                                    ]"
                                >

                                    Delete
                                </button>
                            </MenuItem>
                        </div>
                    </MenuItems>
                </transition>
            </Menu>
        </div>
    </div>

    <!--Details modal-->
    <DialogModal :show="showDetailsModal" @close="showDetailsModal=false">
        <template #title>
            {{ task.title }}
        </template>

        <template #content>
            <div class="flex justify-between">
                <div class="space-y-4">
                    <div v-html="task.description"></div>
                    <div>
                        <p><span class="font-semibold">Deadline</span>: {{ task.deadline }} </p>
                    </div>
                </div>
                <div class="border-l px-4 my-auto">
                    <h5 class="font-semibold">Assignee</h5>
                    <ul>
                        <li v-if="task.users.length" class="flex gap-2 items-center py-1" v-for="user in task.users">
                            <img class="w-6 h-6 rounded-full"
                                 :src="user.profile_photo_url"
                                 :alt="user.name">
                            <p>{{ user.name }}</p>
                        </li>
                        <li v-else>
                            No users found.
                        </li>
                    </ul>
                </div>
            </div>
        </template>

        <template #footer>

            <SecondaryButton @click="showDetailsModal = false">
                Close
            </SecondaryButton>
        </template>
    </DialogModal>
    <!--End details modal-->

    <!--Edit modal-->
    <DialogModal :show="showUpdateModal" @close="closeUpdateModal">
        <template #title>
            Edit task
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
            <SecondaryButton @click="closeUpdateModal">
                Cancel
            </SecondaryButton>

            <PrimaryButton
                type="button"
                @click.prevent="update"
                class="ml-3"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                Save
            </PrimaryButton>
        </template>
    </DialogModal>
    <!--End edit modal-->

    <!--Delete modal-->
    <DialogModal :show="showDeleteModal" @close="showDeleteModal=false">
        <template #title>
            Are you sure?
        </template>

        <template #content>
            You wanted to delete this task.
        </template>

        <template #footer>
            <SecondaryButton @click="showDeleteModal = false">
                Cancel
            </SecondaryButton>

            <PrimaryButton
                type="button"
                @click.prevent="destroy"
                class="ml-3 bg-red-500"
            >
                Delete
            </PrimaryButton>
        </template>
    </DialogModal>
    <!--End delete modal-->

</template>

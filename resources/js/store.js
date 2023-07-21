import {defineStore} from 'pinia'

export const useStore = defineStore('main', {
    state: () => {
        return {
            notifications: [],
        }
    },
    actions: {
        notify(type, message) {
            this.notifications.push({
                show: true,
                type: type,
                message: message,
            })
        }
    },
})

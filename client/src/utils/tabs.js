import { ref } from "vue";

class Tabs {
    constructor(tabs) {
        this.tabs = ref(tabs)
        this.current = ref({})

        if (this.tabs.value.length > 0) {
            this.current.value = this.tabs.value[0]
        }
    }

    navigate(id) {
        this.current.value = this.tabs.value.find(obj => obj.id === id)
    }

    is(id) {
        return this.current.value.id == id
    }

    resetScroll() {
        window.scroll({
            top: 0,
            left: 0,
            behavior: "smooth",
        })
    }

    next() {
        const index = this.tabs.value.indexOf(this.current.value)
        if (index !== -1 && index < this.tabs.value.length - 1) {
            this.current.value = this.tabs.value[index + 1]
            this.resetScroll()
        }
    }
    prev() {
        const index = this.tabs.value.indexOf(this.current.value)
        if (index !== -1 && index > 0) {
            this.current.value = this.tabs.value[index - 1]
            this.resetScroll()
        }
    }
}

export default Tabs
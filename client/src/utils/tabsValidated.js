import { ref } from "vue";

class Tabs {
    constructor(tabs) {
        this.elements = ref(tabs)
        this.currentIndex = ref(0)
    }

    #getToggler() {
        const ids = Object.keys(this.elements.value)
        if (this.currentIndex.value >= 0 && this.currentIndex.value < ids.length) {
            console.log('#' + ids[this.currentIndex.value])
            return document.querySelector(
                `[data-bs-target="${'#' + ids[this.currentIndex.value]}"]`
            )
        }
        return null
    }
    is() {

    }

    #resetScroll() {
        window.scroll({
            top: 0,
            left: 0,
            behavior: "smooth",
        })
    }

    next() {
        this.currentIndex.value++
        this.#getToggler()?.click()
        this.#resetScroll()
    }

    prev() {
        this.currentIndex.value--
        this.#getToggler()?.click()
        this.#resetScroll()
    }
}

export default Tabs
class Tabs {
    constructor(tabs) {
        this.tabs = tabs
    }

    activate_tab(tab) {
        return this.tabs.value.find(obj => obj.id === tab)?.status
    }
    
    navigate_tab(flux, target = null) {
        let index = 0
        for (let i = 0; i < this.tabs.value.length; i++) {
            const tab = this.tabs.value[i];
            if (tab.status) {
                index = i
                break;
            }
        }
        this.tabs.value.forEach((t, i) => {
            if (target !== null) {
                t.status = target === t.id
            } else {
                let active_index = flux === 'prev'
                    ? index > 0 ? index - 1 : index
                    : index < (this.tabs.value.length - 1) ? index + 1 : index
                t.status = active_index === i
            }
        });
    }
}

export default Tabs
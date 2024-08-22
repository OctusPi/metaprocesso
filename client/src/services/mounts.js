export default class Mounts {
    static Normal(address) {
        return {
            value: address,
            classes: []
        }
    }

    static Cast(dataset, datasetKey = "id", datasetCast = "title") {
        if (!Array.isArray(dataset)) {
            return Mounts.Normal
        }

        return (address) => {
            const value = dataset.find((item) => item[datasetKey] == address)
            return {
                value: value ? value[datasetCast] : address,
                classes: []
            }
        }
    }

    static CastVirt(key, compareFunc) {
        return (address, instance) => {
            return {
                value: compareFunc(address, instance._virtual)[key],
                classes: []
            }
        }
    }

    static Cls(...cls) {
        return (address) => ({
            value: address,
            classes: cls
        })
    }

    static Truncate(num = 100) {
        return (address) => {
            if (num >= String(address).length) {
                return {
                    value: address,
                    classes: []
                }
            }
            return {
                value: String(address).slice(0, num - 3) + '...',
                classes: []
            }
        }
    }

    static StripHTML() {
        return (address) => {
            const parser = (new DOMParser())
                .parseFromString(String(address), 'text/html')
            return {
                value: parser.body.textContent,
                classes: []
            }
        }
    }

    static Status() {
        const badges = {
            'tls-yellow': 'Rascunho|Inativo|Baixa|Deserto|Fracassado',
            'tls-blue': 'Enviado|Aberto|Ativo',
            'tls-orange': 'Pendente|Média|Adiado|Revogado',
            'tls-red': 'Bloqueado|Alta|Anulado|Cancelado',
            'tls-grey': 'Processado|Ativo|Ativa|Finalizado'
        }

        return (address) => {
            for (let key in badges) {
                if (badges[key].includes(address)) {
                    return {
                        value: address,
                        classes: ['badge', 'tls', key]
                    }
                }
            }

            return {
                value: address,
                classes: ['badge', 'tls', 'tls-grey']
            }
        }
    }
}
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
            console.log(instance)
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
        return (address) => {
            let colorClass = 'tls-grey'

            switch(address) {
                case 'Rascunho':
                case 'Inativo':
                case 'Baixa':
                case 'Deserto':
                case 'Fracassado':
                    colorClass = 'tls-yellow'
                    break
                case 'Enviado':
                case 'Aberto':
                case 'Ativo':
                case 'Ativa':
                    colorClass = 'tls-blue'
                    break
                case 'Pendente':
                case 'Média':
                case 'Adiado':
                case 'Revogado':
                    colorClass = 'tls-orange'
                    break
                case 'Bloqueado':
                case 'Alta':
                case 'Anulado':
                case 'Cancelado':
                    colorClass = 'tls-red'
                    break
                case 'Processado':
                case 'Finalizado':
                    colorClass = 'tls-grey'
                    break
                default:
                    colorClass = 'tls-grey'
                    break
            }

            return {
                value: address,
                classes: ['badge', 'tls', colorClass]
            }
        }
    }
}
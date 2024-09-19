import { reactive } from "vue"

const maskcep = reactive({
    mask: "#####-###",
    eager: true
})

const maskdate = reactive({
    mask: "##/##/####",
    eager: true
})

const maskcpf = reactive({
    mask: "###.###.###-##",
    eager: true
})

const maskcnpj = reactive({
    mask: "##.###.###/####-##",
    eager: true
})

const maskphone = reactive({
    mask: "(##)9.####-####",
    eager: true
})

const maskmoney = {
    preProcess: val => {
      if (!val) return '';
  
      // remove all not numbers
      const preval = val.replace(/[^\d]/g, '');
  
      return preval;
    },
    postProcess: val => {
      if (!val) return '';
  
      // add cents
      let numberVal = parseFloat(val) / 100;
      if (isNaN(numberVal)) return '';
  
      // format pt-BR Currency
      const postval = Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
      }).format(numberVal);
  
      return postval.replace('R$ ', '');
    }
  }
  
  

const masknumbs = reactive({
    mask: "########",
    eager: true,
})

export default{
    maskcep,
    maskdate,
    maskcpf,
    maskcnpj,
    maskphone,
    maskmoney,
    masknumbs
}
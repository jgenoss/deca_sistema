new Vue({
  el:'#kardex',
  data(){
    return{
      list:true,
      txt:'',
      list_search:[],
      object_search:{
        id:'',
        ean:'',
        nombre:''
      },
      object_kardex:{
        id:'',
        nombre:'',

      }
    }
  },
  methods:{
    search(txt){
      if(true){
        axios.post('controlador/kardex.php?op=getProduct',{txt:this.txt}).then(resp => {
          this.list_search = resp.data;
        })
      }
    },
    find(id){
      axios.post('controlador/kardex.php?op=addKardex',{id}).then(resp => {
        console.log(resp.data);
      })
    }
  }
})

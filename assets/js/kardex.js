new Vue({
  el:'#kardex',
  data(){
    return{
      list:true,
      txt:'',
      list_search:[],
      list_kardex:[],
      object_search:{
        id:'',
        ean:'',
        nombre:''
      },
      object_kardex:{
        p_id:'',
        p_ean:'',
        p_nombre:'',
        e_cantidad:'',
        s_cantidad:'',
        i_cantidad:'',
        es_cantidad:''
      }
    }
  },
  methods:{
    search:function(txt){
      if(true){
        axios.post('controlador/kardex.php?op=getProduct',{txt:this.txt}).then(resp => {
          this.list_search = resp.data;
        })
      }
    },
    find:function(id){
      axios.post('controlador/kardex.php?op=addKardex',{id}).then(resp => {
        console.log(resp.data);
        if (resp.data.data == null) {
          alert("No hay movimientos");
        }else {
          this.addlist(
            resp.data.p_id,
            resp.data.p_ean,
            resp.data.p_nombre,
            resp.data.e_cantidad,
            resp.data.s_cantidad,
            resp.data.i_cantidad,
            resp.data.es_cantidad
          );
        }
      })
    },
    addlist:function(p1,p2,p3,p4,p5,p6,p7){
      console.log(p1,p2,p3,p4,p5,p6,p7);
      this.list_kardex.push({
        p_id:p1,
        p_ean:p2,
        p_nombre:p3,
        e_cantidad:p4,
        s_cantidad:p5,
        i_cantidad:p6,
        es_cantidad:p7
      });
    },
    deleteFind: function (index) {
      this.list_kardex.splice(index, 1);
    },
  }
})

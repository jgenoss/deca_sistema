new Vue({
  el:'#appInventario',
  data(){
    return {
      list:true,
      producto:{
        id_inventario:'',
        nombre:'',
        cantidad:''
      }
    }
  },
  created(){
    this.tabla();
    this.init();
  },
  methods: {
    tabla () {
      $(function () {
        $('#list').DataTable({
          dom:'Bfrtip',
          "buttons": ["copy", "excel", "colvis"],
          "responsive": true,
          "autoWidth": false,
          "lengthMenu": [[50, 100, -1], [50, 100, "All"]],
          "aProcessing": true, //Activamos el procesamiento del datatables
          "aServerSide": true, //Paginacion y filtrado realizados por el servidor
          "ajax": {
            "url": 'controlador/inventario.php?op=getInventario',
            "type": "POST",
            "error": function(e) {console.log(e);}
          },
          "bDestroy": true,
          "iDisplayLength": 25, //Paginacion
          "order": [[1, "asc"]]
        });
      })
    },
    init(){
      const thisJq = this;
      $(function () {
        $(document).on("click", "#edit", function() {
          let id = $(this).val();
          axios.post('controlador/inventario.php?op=getInventarioaId',{id:id}).then(resp =>{
            thisJq.producto = resp.data;
            thisJq.list = true;

          });
        });
      });
    },
    submit:function () {
      axios.post('controlador/inventario.php?op=edit',this.producto).then(resp =>{
        if (resp.data.type == "success") {
          this.sweetalert2(resp.data.tittle,resp.data.message,resp.data.type);
          $("#close").click();
          this.reset();
          this.tabla();
        }else {
          this.sweetalert2(resp.data.tittle,resp.data.message,resp.data.type);
        }
      });
    },
    sweetalert2:function(tittle,message,type) {
			Swal.fire(
				'¡'+tittle+'!',
				''+message+'',
				''+type+''
			)
		},
    reset(){
      this.producto.id_inventario='';
      this.id_inventario='';
      this.nombre='';
      this.cantidad='';
    }
  }
})

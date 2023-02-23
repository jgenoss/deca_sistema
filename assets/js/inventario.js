new Vue({
  el:'#appInventario',
  data(){
    return {
      list:true,
      id_bodega:'',
      producto:{
        id_inventario:'',
        nombre:'',
        cantidad:''
      },
      array:[],
      result:{
        id:'',
        nombre:''
      },
      index:'all'
    }
  },
  created(){
    this.tabla();
    this.init();
    this.getBodega();
  },
  methods: {
    loadInventario(index){
      if (index == "") {
        this.tabla("all");
        this.index = "all"
      }else {
        this.tabla(index);
        this.index = index
      }

    },
    tabla (type) {
      $(function () {
        $('#list').DataTable({
          dom:'Bfrtip',
          "buttons": ["copy", "excel", "colvis","print"],
          "responsive": true,
          "autoWidth": false,
          "lengthMenu": [[50, 100, -1], [50, 100, "All"]],
          "aProcessing": true, //Activamos el procesamiento del datatables
          "aServerSide": true, //Paginacion y filtrado realizados por el servidor
          "ajax": {
            "url": `controlador/inventario.php?op=getInventario&bodega_id=${type}`,
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
        $(document).on("click", "#delete", function() {
          let id = $(this).val();
          Swal.fire({
            title: '¿Estas seguro?',
            text: "!No podrás revertir esto!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText:'Cancelar',
            confirmButtonText: 'Si!'
          }).then((result) => {if (result.isConfirmed) {
              axios.post('controlador/inventario.php?op=delete',{id:id}).then(resp =>{
                if (resp.data.type == "success") {
                  thisJq.sweetalert2(resp.data.tittle,resp.data.message,resp.data.type);
                  thisJq.cancel();
                }else {
                  thisJq.list = true;
                  thisJq.sweetalert2(resp.data.tittle,resp.data.message,resp.data.type);
                }
              });
            }
          });
        });
        $(document).on("click", "#duplicate", function() {
          let id = $(this).val();
          Swal.fire({
            title: '¿Estas seguro?',
            text: "!Duplicararas este Producto!",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText:'Cancelar',
            confirmButtonText: 'Si!'
          }).then((result) => {if (result.isConfirmed) {
              axios.post('controlador/inventario.php?op=duplicate',{id:id}).then(resp =>{
                if (resp.data.type == "success") {
                  thisJq.sweetalert2(resp.data.tittle,resp.data.message,resp.data.type);
                  thisJq.cancel();
                }else {
                  thisJq.list = true;
                  thisJq.sweetalert2(resp.data.tittle,resp.data.message,resp.data.type);
                }
              });
            }
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
          this.tabla(this.index);
        }else {
          this.sweetalert2(resp.data.tittle,resp.data.message,resp.data.type);
        }
      });
    },
    getBodega(){
      axios.post('controlador/entrada.php?op=getBodega').then(resp =>{
        this.array = resp.data;
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

new Vue({
  el:'#bodega',
  data(){
    return{
      //id_cliente	nombre	descripcion	status
      bodega:{
        id_bodega:'',
        id_cliente:'',
        nombre:'',
        descripcion:'',
        status:''
      },
      arrayCliente:[],
      resultCliente:{
        id:'',
        nombre:''
      },
      Modulo:'',
      list:true,
      form:false
    }
  },
  created(){
    this.tabla();
    this.init();
  },
  methods:{
    
    tabla() {
      $(function() {
        $("#list").DataTable({
          "responsive": true,
          "autoWidth": false,
          "lengthMenu": [[50, 100, -1], [50, 100, "All"]],
          "aProcessing": true, //Activamos el procesamiento del datatables
          "aServerSide": true, //Paginacion y filtrado realizados por el servidor
          "ajax": {
            "url": 'controlador/bodegas.php?op=getBodegas',
            "type": "POST",
            "error": function(e) {console.log(e);}
          },
          "bDestroy": true,
          "iDisplayLength": 25, //Paginacion
          "order": [[1, "desc"]]
        });
      });
    },
    cancel:function () {
      this.tabla();
      this.limpiarInputs();
      this.list = true;
      this.form = false;
    },
    init(){
      const thisJq = this;
      $(function () {
        $(document).on("click", "#edit", function() {
          let id = $(this).val();
          axios.post('controlador/bodegas.php?op=getBodegaId',{id:id}).then(resp =>{
            thisJq.Modulo = 'Editar Bodega';
            thisJq.bodega = resp.data;
            thisJq.list = false;
            thisJq.form = true;
            thisJq.getCliente();
          });
        });
        $('[data-mask]').inputmask();
        $(document).on("click", "#delete", function() {
          let id = $(this).val();
          Swal.fire({
            title: '¿Estás seguro?',
            text: "¡Se eliminarán todos los productos, inventarios y movimientos asociados a esta bodega! Esta acción no se puede deshacer.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar todo',
            cancelButtonText: 'Cancelar'
          }).then((result) => {
            if (result.isConfirmed) {
                // Realizar la petición de eliminación
                axios.post('controlador/bodegas.php?op=eliminar', { id: id })
                .then(resp => {
                    if (resp.data.status) {
                        Swal.fire(
                            '¡Eliminado!',
                            resp.data.msg,
                            'success'
                        );
                        // Recargar la tabla
                        if (thisJq && thisJq.tabla) {
                            thisJq.tabla(); 
                        } else {
                            // Fallback si la referencia a Vue no está directa
                            location.reload(); 
                        }
                    } else {
                        Swal.fire('Error', resp.data.msg, 'error');
                    }
                })
                .catch(error => {
                    console.error(error);
                    Swal.fire('Error', 'Ocurrió un error en el servidor', 'error');
                });
            }
          });
        });
      });
    },
    newbutton:function () {
      this.limpiarInputs();
      this.Modulo = 'Nuevo Cliente';
      this.list = false;
      this.form = true;
      this.getCliente();
    },
    sweetalert2:function(tittle,message,type) {
			Swal.fire(
				'¡'+tittle+'!',
				''+message+'',
				''+type+''
			)
		},
    delSubmit:function(id) {
      Swal.fire({
        title: '¿Estas seguro?',
        text: "No podrás revertir esto.!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText:'Cancelar',
        confirmButtonText: 'Si, eliminarlo!'
      }).then((result) => {
        if (result.isConfirmed) {
          axios.post('ajax/clientes.php?option=del',{id:id})
            .then(resp =>{
              this.postDataList();
              this.sweetalert2(resp.data.tittle,resp.data.message,resp.data.type);
            })
        }
      })
    },
    submitBtn:function () {
      axios.post('controlador/bodegas.php?op=newEdit',this.bodega).then(resp =>{
        if (resp.data.type == "success") {
          this.sweetalert2(resp.data.tittle,resp.data.message,resp.data.type);
          this.cancel();
        }else {
          this.sweetalert2(resp.data.tittle,resp.data.message,resp.data.type);
          console.log(resp.data.message);
        }
      });
    },
    getCliente:function () {
      axios.post('controlador/bodegas.php?op=getCliente').then(resp =>{
        this.arrayCliente = resp.data;
      });
    },
    limpiarInputs:function() {
      this.bodega.id_cliente='',
      this.bodega.empresa='',
      this.bodega.nombre='',
      this.bodega.descripcion='',
      this.bodega.status=''
    }
  }
})

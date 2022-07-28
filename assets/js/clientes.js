new Vue({
  el:'#clientes',
  data(){
    return{
      cliente:{
        id_cliente:'',
        nombre:'',
        empresa:'',
        telefono:'',
        direccion:'',
        habilitado:'',
        id_usuario:'',
        tipo_documento:'',
        documento:'',
        correo:''
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
          "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
          "aProcessing": true, //Activamos el procesamiento del datatables
          "aServerSide": true, //Paginacion y filtrado realizados por el servidor
          "ajax": {
            "url": 'controlador/clientes.php?op=getClientes',
            "type": "POST",
            "error": function(e) {console.log(e);}
          },
          "bDestroy": true,
          "iDisplayLength": 10, //Paginacion
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
          axios.post('controlador/clientes.php?op=getClienteId',{id:id}).then(resp =>{
            thisJq.Modulo = 'Editar Cliente';
            thisJq.cliente = resp.data;
            thisJq.list = false;
            thisJq.form = true;
          });
        });
        $('[data-mask]').inputmask();
      });
    },
    newbutton:function () {
      this.limpiarInputs();
      this.Modulo = 'Nuevo Cliente';
      this.list = false;
      this.form = true;
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
      axios.post('controlador/clientes.php?op=newEdit',this.cliente).then(resp =>{
        if (resp.data.type == "success") {
          this.sweetalert2(resp.data.tittle,resp.data.message,resp.data.type);
          this.cancel();
        }else {
          this.sweetalert2(resp.data.tittle,resp.data.message,resp.data.type);
          console.log(resp.data.message);
        }
      });
    },
    limpiarInputs:function() {
      this.cliente.id_cliente='',
      this.cliente.nombre='',
      this.cliente.empresa='',
      this.cliente.telefono='',
      this.cliente.direccion='',
      this.cliente.habilitado='',
      this.cliente.id_usuario='',
      this.cliente.tipo_documento='',
      this.cliente.documento='',
      this.cliente.correo=''
    }
  }
})

new Vue({
  el:'#devolucion',
  data(){
    return{
      codigo:'',
      total:0,
      file_name:'Selecione un document',
      rtnL:{
        id:'',
        codigo:'',
        nombre:'',
        cantidad:0
      },
      rtnLD:{
        id:'',
        codigo:'',
        nombre:'',
        cantidad:0
      },
      arrayCliente:[],
      resultCliente:{
        id:'',
        nombre:''
      },
      devolucion:{
        id_salida:'',
        id_cliente:'',
        referencia:'',
        factura:'',
        fecha:'',
        file:'',
        serie:'',
        observacion:'',
        tpago:'',
        listp:[]
      },
      ver:{
        id_salida:'',
        id_cliente:'',
        referencia:'',
        factura:'',
        fecha:'',
        file:'',
        serie:'',
        observacion:'',
        tpago:'',
        listp:[]
      },
      fila:'',
      Modulo:'',
      list:true,
      form:false,
      sale:false,
      view:false,
      btnR:true
    }
  },
  created(){
    this.tabla();
    this.init();
    this.getCliente();
    this.getConsecutivo();
  },

  methods:{
    keyNa(){
      this.devolucion.observacion = "N/A";
    },
    getConsecutivo(){
      axios.post('controlador/devolucion.php?op=getConsecutivo').then(resp =>{
        this.generadNumber(resp.data.next);
        //this.devolucion.serie = resp.data.next;
      });
    },
    generadNumber(num){
      if (parseInt(num) < 10) {
        this.devolucion.serie = `0000000${num}`;
      }else if (parseInt(num) < 100) {
        this.devolucion.serie = `000000${num}`;
      }else if (parseInt(num) < 1000) {
        this.devolucion.serie = `00000${num}`;
      }else if (parseInt(num) < 10000) {
        this.devolucion.serie = `0000${num}`;
      }else if (parseInt(num) < 100000) {
        this.devolucion.serie = `000${num}`;
      }else {
        this.sweetalert2("ATENCION","CONTACTE CON EL PROGRAMADOR","error");
        this.devolucion.serie = `andale hagale un aumento de sueldo`;
      }
    },
    count(index){
      axios.post('controlador/devolucion.php?op=valiDate',{sr:this.ver.serie,list:this.devolucion.listp[index]}).then(resp =>{
        if (resp.data.type == "error") {
          this.sweetalert2(resp.data.tittle,resp.data.message,resp.data.type);
          this.devolucion.listp[index].cantidad = resp.data.max;
        }else if (resp.data.max == "0") {
          this.sweetalert2(resp.data.tittle,resp.data.message,resp.data.type);
          this.devolucion.listp[index].cantidad = resp.data.max;
        }
      });
    },
    loadInventario(index){
      this.listaInventario(index);
    },
    submit:function () {
      if (!this.devolucion.listp.length) {
        this.sweetalert2("info","Agregue un producto","info");
      }else {
        Swal.fire({
          title: '¿Estas seguro?',
          text: "!No podrás revertir esto!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          cancelButtonText:'Cancelar',
          confirmButtonText: 'Si!'
        }).then((result) => {
          if (result.isConfirmed) {
            axios.post('controlador/devolucion.php?op=regSalida',this.devolucion).then(resp =>{
              if (resp.data.type == "success") {
                this.sweetalert2(resp.data.tittle,resp.data.message,resp.data.type);
                this.cancel();
              }else {
                this.sweetalert2(resp.data.tittle,resp.data.message,resp.data.type);
              }
            });
          }
        });
      }
    },
    uploadImage: function() {
      thisJq = this;
      const file = document.querySelector('input[type=file]').files[0];
      const reader = new FileReader();
      reader.onload = function(e) {
        thisJq.file_name = file.name;
        thisJq.devolucion.file = e.target.result;
      };
      reader.onerror = function(error) {
        alert(error);
      };
      reader.readAsDataURL(file);
    },
    tabla() {
      $(function() {
        $("#list").DataTable({
          "responsive": true,
          "autoWidth": false,
          "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
          "aProcessing": true, //Activamos el procesamiento del datatables
          "aServerSide": true, //Paginacion y filtrado realizados por el servidor
          "ajax": {
            "url": 'controlador/devolucion.php?op=getSalidas',
            "type": "POST",
            "error": function(e) {console.log(e);}
          },
          "bDestroy": true,
          "iDisplayLength": 10, //Paginacion
          "order": [[1, "asc"]]
        });
      });
    },
    cancel:function () {
      $("#css").attr("class","sidebar-mini layout-fixed");
      this.tabla();
      this.limpiarInputs();
      this.list = true;
      this.sale = false;
      this.view = false;
    },
    getCliente(){
      axios.post('controlador/devolucion.php?op=getClientes').then(resp =>{
        this.arrayCliente = resp.data;
      });
    },
    init(){
      const thisJq = this;
      $(function () {
        $(document).on("click", "#view", function() {
          let id = $(this).val();
          axios.post('controlador/devolucion.php?op=getSalidaId',{id:id}).then(resp =>{
            thisJq.ver = resp.data;
            thisJq.devolucion.id_salida = resp.data.id_salida;
            thisJq.devolucion.id_cliente = resp.data.id_cliente;
            thisJq.devolucion.referencia = resp.data.referencia;
            thisJq.devolucion.factura = resp.data.factura;
            thisJq.devolucion.fecha = resp.data.fecha;
            thisJq.devolucion.serie = resp.data.serie;
            thisJq.devolucion.observacion = resp.data.observacion;
            thisJq.devolucion.tpago = resp.data.tpago;
            thisJq.total = resp.data.total;
            $("#css").attr("class","sidebar-mini layout-fixed sidebar-collapse");
            thisJq.view = true;
            thisJq.list = false;
          });
        });
        $(document).on("click", "#prod", function() {
          let id = $(this).val();
          let nombre = $(this).attr("nombre");
          let codigo = $(this).attr("codigo");
          let existencia = $(this).attr("existencia");
          thisJq.addFind(id,codigo,nombre,existencia);
        });
      });
    },
    addFind: function (id,codigo,nombre) {
      this.devolucion.listp.push({
        id:id,
        codigo:codigo,
        nombre:nombre,
        cantidad:0
      });
    },
    deleteFind: function (index) {
      console.log(index);
      this.devolucion.listp.splice(index, 1);
    },
    getProductId:function (id) {
      axios.post('controlador/devolucion.php?op=getProductId',{id:id}).then(resp =>{
        this.agregarDetalle(resp.data.id,resp.data.producto,resp.data.codigo);
      });
    },
    newbutton:function () {
      this.getCliente();
      this.getConsecutivo();
      this.limpiarInputs();
      $("#css").attr("class","sidebar-mini layout-fixed sidebar-collapse");
      this.Modulo = 'Nuevo Cliente';
      this.list = false;
      this.sale = true;
    },
    sweetalert2:function(tittle,message,type) {
			Swal.fire(
				'¡'+tittle+'!',
				''+message+'',
				''+type+''
			)
		},
    limpiarInputs:function() {
      this.devolucion.id_salida='',
      this.devolucion.id_cliente='',
      this.devolucion.referencia='',
      this.devolucion.factura='',
      this.devolucion.fecha='',
      this.devolucion.file='',
      this.devolucion.serie='',
      this.devolucion.observacion='',
      this.devolucion.tpago='',
      this.devolucion.listp = [];
      this.file_name='Selecione un document';
    }
  }
})

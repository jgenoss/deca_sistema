new Vue({
  el:'#salida',
  data(){
    return{
      codigo:'',
      total:0,
      file_name:'Selecione un document',
      resultListP:{
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
      salida:{
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
      this.salida.observacion = "N/A";
    },
    getConsecutivo(){
      axios.post('controlador/salida.php?op=getConsecutivo').then(resp =>{
        this.generadNumber(resp.data.next);
        //this.salida.serie = resp.data.next;
      });
    },
    generadNumber(num){
      if (parseInt(num) < 10) {
        this.salida.serie = `0000000${num}`;
      }else if (parseInt(num) < 100) {
        this.salida.serie = `000000${num}`;
      }else if (parseInt(num) < 1000) {
        this.salida.serie = `00000${num}`;
      }else if (parseInt(num) < 10000) {
        this.salida.serie = `0000${num}`;
      }else if (parseInt(num) < 100000) {
        this.salida.serie = `000${num}`;
      }else {
        this.sweetalert2("ATENCION","CONTACTE CON EL PROGRAMADOR","error");
        this.salida.serie = `andale hagale un aumento de sueldo`;
      }
    },
    count(index){
      axios.post('controlador/salida.php?op=valiDate',this.salida.listp[index]).then(resp =>{
        if (resp.data.type == "error") {
          this.sweetalert2(resp.data.tittle,resp.data.message,resp.data.type);
          this.salida.listp[index].cantidad = resp.data.max;
        }else if (resp.data.max == "0") {
          this.sweetalert2(resp.data.tittle,resp.data.message,resp.data.type);
          this.salida.listp[index].cantidad = resp.data.max;
        }
      });
    },
    loadInventario(index){
      this.listaInventario(index);
    },
    submit:function () {
      if (!this.salida.listp.length) {
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
            axios.post('controlador/salida.php?op=regSalida',this.salida).then(resp =>{
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
        thisJq.salida.file = e.target.result;
      };
      reader.onerror = function(error) {
        alert(error);
      };
      reader.readAsDataURL(file);
    },
    tabla() {
      $(function() {
        $("#list").DataTable({
          dom:'Bfrtip',
          "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
          "responsive": true,
          "autoWidth": false,
          "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
          "aProcessing": true, //Activamos el procesamiento del datatables
          "aServerSide": true, //Paginacion y filtrado realizados por el servidor
          "ajax": {
            "url": 'controlador/salida.php?op=getSalidas',
            "type": "POST",
            "error": function(e) {console.log(e);}
          },
          "bDestroy": true,
          "iDisplayLength": 10, //Paginacion
          "order": [[1, "desc"]]
        });
      });
    },
    listaInventario(type) {
      $(function() {
        $("#invt").DataTable({
          "responsive": true,
          "autoWidth": false,
          "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
          "aProcessing": true, //Activamos el procesamiento del datatables
          "aServerSide": true, //Paginacion y filtrado realizados por el servidor
          "ajax": {
            "url": `controlador/salida.php?op=getInventario&id=${type}`,
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
      $("#css").attr("class","sidebar-mini layout-fixed");
      this.tabla();
      this.limpiarInputs();
      this.list = true;
      this.sale = false;
      this.view = false;
    },
    getCliente(){
      axios.post('controlador/salida.php?op=getClientes').then(resp =>{
        this.arrayCliente = resp.data;
      });
    },
    init(){
      const thisJq = this;
      $(function () {
        $(document).on("click", "#view", function() {
          let id = $(this).val();
          axios.post('controlador/salida.php?op=getSalidaId',{id:id}).then(resp =>{
            thisJq.ver = resp.data;
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
      this.salida.listp.push({
        id:id,
        codigo:codigo,
        nombre:nombre,
        cantidad:0
      });
    },
    deleteFind: function (index) {
      console.log(index);
      this.salida.listp.splice(index, 1);
    },
    getProductId:function (id) {
      axios.post('controlador/salida.php?op=getProductId',{id:id}).then(resp =>{
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
      this.salida.id_cliente='',
      this.salida.referencia='',
      this.salida.factura='',
      this.salida.fecha='',
      this.salida.file='',
      this.salida.serie='',
      this.salida.observacion='',
      this.salida.tpago='',
      this.salida.listp = [];
      this.file_name='Selecione un document';
    }
  }
})

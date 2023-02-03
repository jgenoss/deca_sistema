new Vue({
  el:'#entrada',
  data(){
    return{
      codigo:'',
      total:0,
      file_name:'Selecione un document',
      file:'',
      f_start:'',
      f_end:'',
      resultListP:{
        id:'',
        codigo:'',
        nombre:'',
        fecha_v:'',
        fv:false,
        cantidad:0,
        umb:'',
        cj:''
      },
      arrayCliente:[],
      resultCliente:{
        id:'',
        nombre:''
      },
      arrayBodega:[],
      resultBodega:{
        id:'',
        nombre:''
      },
      entrada:{
        id_cliente:'',
        id_bodega:'',
        referencia:'',
        factura:'',
        tipo_comprobante:'',
        fecha:'',
        file:'',
        serie:'',
        observacion:'',
        direccion:'',
        listp:[]
      },
      ver:{
        total:0,
        id_cliente:'',
        id_bodega:'',
        referencia:'',
        factura:'',
        tipo_comprobante:'',
        fecha:'',
        file:'',
        serie:'',
        observacion:'',
        direccion:'',
        listp:[]
      },
      fila:'',
      Modulo:'',
      list:true,
      form:false,
      view:false,
      btnR:true,
      edit:false
    }
  },
  created(){
    this.tabla();
    this.init();
    this.getCliente();
    this.getBodega();
    this.getConsecutivo();
  },

  methods:{
    getConsecutivo(){
      axios.post('controlador/entrada.php?op=getConsecutivo').then(resp =>{
        this.generadNumber(resp.data.next);
        //this.entrada.serie = resp.data.next;
      });
    },
    getListDate(){
      thisJq = this;
      $(function() {
        $("#list").DataTable({
          dom:'Bfrtip',
          "buttons": ["copy", "excel", "colvis"],
          "responsive": true,
          "autoWidth": false,
          "lengthMenu": [[50, 100, -1], [50, 100, "All"]],
          "aProcessing": true, //Activamos el procesamiento del datatables
          "aServerSide": true, //Paginacion y filtrado realizados por el servidor
          "ajax": {
            "url": `controlador/entrada.php?op=getListDate&f_start=${thisJq.f_start}&f_end=${thisJq.f_end}`,
            "type": "POST",
            "error": function(e) {console.log(e);}
          },
          "bDestroy": true,
          "iDisplayLength": 40, //Paginacion
          "order": [[0, "asc"]]
        });
      });
    },
    generadNumber(num){
      if (parseInt(num) < 10) {
        this.entrada.serie = `0000000${num}`;
      }else if (parseInt(num) < 100) {
        this.entrada.serie = `000000${num}`;
      }else if (parseInt(num) < 1000) {
        this.entrada.serie = `00000${num}`;
      }else if (parseInt(num) < 10000) {
        this.entrada.serie = `0000${num}`;
      }else if (parseInt(num) < 100000) {
        this.entrada.serie = `000${num}`;
      }else {
        this.sweetalert2("ATENCION","CONTACTE CON EL PROGRAMADOR","error");
        this.entrada.serie = `andale hagale un aumento de sueldo`;
      }
    },
    count(index){
      axios.post('controlador/entrada.php?op=valiDate',this.entrada.listp[index]).then(resp =>{
        if (resp.data.type == "error") {
          this.sweetalert2(resp.data.tittle,resp.data.message,resp.data.type);
          this.entrada.listp[index].cantidad = resp.data.max;
        }else if (resp.data.max == "0") {
          this.sweetalert2(resp.data.tittle,resp.data.message,resp.data.type);
          this.entrada.listp[index].cantidad = resp.data.max;
        }
      });
    },
    loadInventario(index){
      this.listaInventario(index);
    },
    submit:function () {
      if (!this.entrada.listp.length) {
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
            axios.post('controlador/entrada.php?op=regentrada',this.entrada).then(resp =>{
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
        thisJq.entrada.file = e.target.result;
      };
      reader.onerror = function(error) {
        alert(error);
      };
      reader.readAsDataURL(file);
    },
    onChangeFileUpload(){
      thisJq = this;
      file = this.$refs.uploadfiles0.files[0];
      this.file_name = file.name;
      this.file = file;
    },
    submitUpload() {
      const formData = new FormData();
      formData.append('file',this.file)
      axios.post('controlador/entrada.php?op=uploadFile',formData).then(resp =>{
        if (resp.data.type == "success") {
          this.sweetalert2(resp.data.tittle,resp.data.message,resp.data.type);
          this.entrada.listp = resp.data.listp;
        }else {
          this.sweetalert2(resp.data.tittle,resp.data.message,resp.data.type);
        }
      });
    },
    tabla() {
      $(function() {
        $("#list").DataTable({
          dom:'Bfrtip',
          "buttons": ["copy", "excel", "colvis"],
          "responsive": true,
          "autoWidth": false,
          "lengthMenu": [[50, 100, -1], [50, 100, "All"]],
          "aProcessing": true, //Activamos el procesamiento del datatables
          "aServerSide": true, //Paginacion y filtrado realizados por el servidor
          "ajax": {
            "url": 'controlador/entrada.php?op=getentradas',
            "type": "POST",
            "error": function(e) {console.log(e);}
          },
          "bDestroy": true,
          "iDisplayLength": 40, //Paginacion
          "order": [[0, 'desc' ]]
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
            "url": `controlador/entrada.php?op=getInventario&id=${type}`,
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
      $("#css").attr("class","sidebar-mini layout-fixed");
      this.tabla();
      this.limpiarInputs();
      this.list = true;
      this.form = false;
      this.view = false;
    },
    getCliente(){
      axios.post('controlador/entrada.php?op=getClientes').then(resp =>{
        this.arrayCliente = resp.data;
      });
    },
    getBodega(){
      axios.post('controlador/entrada.php?op=getBodega').then(resp =>{
        this.arrayBodega = resp.data;
      });
    },
    init(){
      const thisJq = this;
      $(function () {
        $(document).on("click", "#view", function() {
          let id = $(this).val();
          axios.post('controlador/entrada.php?op=getentradaId',{id:id}).then(resp =>{
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
          thisJq.addFind(id,codigo,nombre);
        });
      });
    },
    addFind: function (id,codigo,nombre) {
      this.entrada.listp.push({
        id:id,
        codigo:codigo,
        nombre:nombre,
        fecha_v:'',
        fv:false,
        cantidad:0
      });
    },
    deleteFind: function (index) {
      console.log(index);
      this.entrada.listp.splice(index, 1);
    },
    getProductId:function (id) {
      axios.post('controlador/entrada.php?op=getProductId',{id:id}).then(resp =>{
        this.agregarDetalle(resp.data.id,resp.data.producto,resp.data.codigo);
      });
    },
    newbutton:function () {
      this.getCliente();
      this.getBodega();
      this.getConsecutivo();
      this.limpiarInputs();
      $("#css").attr("class","sidebar-mini layout-fixed sidebar-collapse");
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
    limpiarInputs:function() {
      this.entrada.id_cliente='',
      this.entrada.referencia='',
      this.entrada.factura='',
      this.entrada.fecha='',
      this.entrada.file='',
      this.entrada.serie='',
      this.entrada.observacion='',
      this.entrada.tpago='',
      this.entrada.listp = [];
      this.file_name = 'Selecione un document';
    }
  }
})

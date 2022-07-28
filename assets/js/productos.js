new Vue({
  el:'#appProducto',
  data(){
    return {
      list:true,
      form:false,
      productos:{
        id:'',
        codigo:'',
        code1:'',
        code2:'',
        ean:'',
        nombre:'',
        umb:'',
        id_categoria:'',
        id_bodega:'',
        estampilla:'',
        status:''
      },
      arraycategoria:[],
      resultCategoria:{
        id:'',
        nombre:''
      },
      arraybodega:[],
      resultbodega:{
        id:'',
        nombre:''
      },
      Modulo:''
    }
  },
  created(){
    this.tabla();
    this.activos();
    this.getTipoProducto();
    this.getBodega();
  },
  methods: {
    keyNa(){
      this.productos.code1 = "N/A";
      this.productos.code2 = "N/A";
    },
    tabla() {
      $(function () {
        $('#list').DataTable({
          /*
          dom:'Bfrtip',
          "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],*/
          "responsive": true,
          "autoWidth": false,
          "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
          "aProcessing": true, //Activamos el procesamiento del datatables
          "aServerSide": true, //Paginacion y filtrado realizados por el servidor
          "ajax": {
            "url": 'controlador/productos.php?op=getProductos',
            "type": "POST",
            "error": function(e) {console.log(e);}
          },
          "bDestroy": true,
          "iDisplayLength": 10, //Paginacion
          "order": [[1, "asc"]]
        });
      });
    },
    randCode:function(num) {
      const characters ='ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
      let result = "";
      const charactersLength = characters.length;
      for (let i = 0; i < num; i++) {
          result += characters.charAt(Math.floor(Math.random() * charactersLength));
      }
      this.productos.codigo = result;
    },
    activos:function () {
      const thisJq = this;
      $(function () {
        $(document).on("click", "#edit", function() {
          let id = $(this).val();
          axios.post('controlador/productos.php?op=getProductoId',{id:id}).then(resp =>{
            thisJq.Modulo = 'Editar Producto';
            thisJq.productos = resp.data;
            thisJq.list = false;
            thisJq.form = true;
          });
        });
      });
    },
    cancel () {
      this.tabla();
      this.activos();
      this.limpiarInputs();
      this.list = true;
      this.form = false;
    },
    newbutton:function () {
      this.limpiarInputs();
      this.randCode(6);
      this.getBodega();
      this.Modulo = 'Crear Nuevo Producto';
      this.list = false;
      this.form = true;
    },
    getTipoProducto:function () {
      axios.post('controlador/productos.php?op=getTipoProducto').then(resp =>{
        this.arraycategoria = resp.data;
      });
    },
    getBodega:function () {
      axios.post('controlador/productos.php?op=getbodega').then(resp =>{
        this.arraybodega = resp.data;
        console.log(resp.data);
      });
    },
    limpiarInputs:function () {
        this.productos.id='',
        this.productos.codigo='',
        this.productos.code1='',
        this.productos.code2='',
        this.productos.ean='',
        this.productos.nombre='',
        this.productos.umb='',
        this.productos.id_bodega='',
        this.productos.estampilla='',
        this.productos.status='',
        this.productos.id_categoria='';
    },
    submitBtn:function () {
      axios.post('controlador/productos.php?op=newEdit',this.productos).then(resp =>{
        if (resp.data.type == "success") {
          this.sweetalert2(resp.data.tittle,resp.data.message,resp.data.type);
          this.cancel();
        }else {
          this.sweetalert2(resp.data.tittle,resp.data.message,resp.data.type);
          console.log(resp.data.message);
        }
      });
    },
    sweetalert2:function(tittle,message,type) {
			Swal.fire(
				'¡'+tittle+'!',
				''+message+'',
				''+type+''
			)
		}
  }
})

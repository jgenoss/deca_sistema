new Vue({
  el:'#appInventario',
  data(){
    return {
      list:true
    }
  },
  created(){
    this.tabla();
  },
  methods: {
    tabla () {
      $(function () {
        $('#list').DataTable({
          dom:'Bfrtip',
          "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
          "responsive": true,
          "autoWidth": false,
          "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
          "aProcessing": true, //Activamos el procesamiento del datatables
          "aServerSide": true, //Paginacion y filtrado realizados por el servidor
          "ajax": {
            "url": 'controlador/inventario.php?op=getInventario',
            "type": "POST",
            "error": function(e) {console.log(e);}
          },
          "bDestroy": true,
          "iDisplayLength": 10, //Paginacion
          "order": [[1, "asc"]]
        });
      })
    }
  }
})

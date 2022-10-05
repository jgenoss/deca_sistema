new Vue({
	el:'#usuarios',
	data(){
		return{
			data:{
				login:'',
				password:'',
				name:'',
				status:''
			},
			modulo:'',
			list:true,
			form:false
		}
	},
	created(){
		this.listUsers();
	},
	methods:{
		submit:function() {
			axios.post('controlador/usuarios.php?op=new',this.data)
				.then(resp =>{
					console.log(resp.data);
			})
		},
		listUsers(){
			$(function() {
        $("#list").DataTable({
          "responsive": true,
          "autoWidth": false,
          "lengthMenu": [[50, 100, -1], [50, 100, "All"]],
          "aProcessing": true, //Activamos el procesamiento del datatables
          "aServerSide": true, //Paginacion y filtrado realizados por el servidor
          "ajax": {
            "url": 'controlador/usuarios.php?op=listUsers',
            "type": "POST",
            "error": function(e) {console.log(e);}
          },
          "bDestroy": true,
          "iDisplayLength": 25, //Paginacion
          "order": [[1, "desc"]]
        });
      });
		},
		cancel(){
			this.list = true;
			this.form = false;
			this.resetInput();

		},
		newbutton(){
			this.modulo = 'Crear nuevo usuario';
			this.list = false;
			this.form = true;
			this.resetInput();
		},
		resetInput(){
			this.listUsers();
			this.data.login='';
			this.data.password='';
			this.data.name='';
			this.data.status='';
		}
	}
})

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
		this.activos();
	},
	methods:{
		submit:function() {
			axios.post('controlador/usuarios.php?op=new',this.data)
				.then(resp =>{
					this.sweetalert2(resp.data.tittle,resp.data.message,resp.data.type);
					this.cancel();
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
		activos() {
      const thisJq = this;
      $(function () {
        $(document).on("click", "#disabled", function() {
					thisJq.sweetalert2("ERROR","NO TIENES PERMISOS","warning");
          // let id = $(this).val();
          // axios.post('controlador/usuarios.php?op=disabled',{id:id}).then(resp =>{
					// 	thisJq.listUsers();
					// 	thisJq.sweetalert2(resp.data.tittle,resp.data.message,resp.data.type);
          // });

        });
				$(document).on("click", "#edit", function() {
					thisJq.sweetalert2("ERROR","NO TIENES PERMISOS","warning");
          // let id = $(this).val();
          // axios.post('controlador/usuarios.php?op=edit',{id:id}).then(resp =>{
          //   thisJq.list = false;
          //   thisJq.form = true;
          // });
        });
      });
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
		},
		sweetalert2:function(tittle,message,type) {
			Swal.fire(
				'¡'+tittle+'!',
				''+message+'',
				''+type+''
			)
		},
	}
})
